<?php

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

use Application\Sonata\UserBundle\Form\Type\SocialConnectFormType;
use Application\Sonata\UserBundle\Form\Type\SocialConnectPasswordCheckFormType;
use Application\Sonata\UserBundle\Entity\User;
use Application\Sonata\MediaBundle\Entity\Media;

use Buzz\Browser;

use Ailove\VKBundle\Security\Authentication\Token\VKUserToken;
use Ailove\OKBundle\Security\Authentication\Token\OKUserToken;

use Monolog\Logger;

/**
 * Social connect.
 */
class SocialConnectController extends Controller
{
    const EMAIL_VERIFICATION_SESSION_KEY = '_connect_email_verification';

    const CONNECT_TYPE_NONE = 'CONNECT_TYPE_NONE';
    const CONNECT_TYPE_VK = 'CONNECT_TYPE_VK';
    const CONNECT_TYPE_OK = 'CONNECT_TYPE_OK';

    protected $userManager;

    /**
     * Index action.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function connectAction()
    {
        $log = $this->get('monolog.user_connect');
        $log->addInfo('[start] Appeal to user connect controller');
        
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted(User::ROLE_REGISTERED)) {

            $log->addInfo('User has ROLE_REGISTERED');

            $user = $this->fillUserDependsOnConnectType($securityContext->getToken()->getUser(), $log);
            $this->getUserManager()->updateUser($user);

            $log->addInfo('[end] Update user with ROLE_REGISTERED and redirect to form');

            return $this->redirect($this->container->get('router')->generate('HelloBundle_homepage'));
        }
        
        /**
         * Get User from security context
         */
        $user = $securityContext->getToken()->getUser();

        $form = $this->createForm(new SocialConnectFormType());

        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $log->addInfo('Form submit data is valid');

                $data = $form->getData();
                $email = $data['email'];
                $userWithEmailExists =  $this->getUserManager()->findUserByEmail($email);

                if ($userWithEmailExists) {
                    $log->addInfo('User email has in DB');
                    // Email exists, need verify
                    $this->get('session')->set(static::EMAIL_VERIFICATION_SESSION_KEY, $userWithEmailExists->getEmail());
                    
                    if ($this->getRequest()->isXmlHttpRequest()) {
                        // Handle XHR here
                        $result = array(
                            'status' => 'error',
                            'message' => 'В нашей системе есть пользователь с таким e-mail',
                            'email' => $userWithEmailExists->getEmail()
                        );

                        return new Response(json_encode($result));
                    }
            
                    return $this->redirect($this->container->get('router')->generate('StoryBundle_homepage'));
                } else {
                    $log->addInfo('Before fillUserDependsOnConnectType');
                    // Now we can create new user
                    $user = $this->fillUserDependsOnConnectType($user, $log);

                    $log->addInfo('Set email and ROLE_REGISTERED');

                    $user->setUsername($email);
                    $user->setEmail($email);
                    $user->addRole(User::ROLE_REGISTERED);
                    
                    // Expire At
                    $datetime = new \Datetime();
                    $user->setExpiresAt($datetime->modify('+ 10 days'));

                    // Token 
                    $confirmToken = $user->getConfirmationToken();
                    if (null === $confirmToken) {
                        $tokenGenerator = $this->get('fos_user.util.token_generator');
                        $confirmToken = $tokenGenerator->generateToken();
                        $user->setConfirmationToken($confirmToken);
                    }
                    
                    // Update User
                    $this->getUserManager()->updateUser($user);

                    // Send confirmation email
//                    $this->get('fos_user.mailer')->sendConfirmationEmailMessage($user);

                    $this->get('session')->setFlash('notice', 'Вы авторизованы');

                    // Refresh token
                    $this->refreshToken($user);

                    $log->addInfo('[end] Update user after email form handling and redirect to form');

                    if ($this->getRequest()->isXmlHttpRequest()) {
                        
                        $userMailer = $this->get('fos_user.mailer');

                        $userMailer->sendConfirmationEmailMessage($user);
//                        try {
//                            $params = array(
//                                'email' => $email,
//                                'token' => $confirmToken
//                            );
//
//                            $this->sendEmail($params, 'ApplicationSonataUserBundle:SocialConnect:confirmSocialEmail.txt.twig');
//                        } catch (\Exception $e) {
//
//                        }

                        // Handle XHR here
                        $result = array(
                            'status' => 'ok',
                            'message' => 'Пользователь создан',
                            'redirect' => $this->container->get('router')->generate('HelloBundle_homepage')
                        );

                        return new Response(json_encode($result)); 
                    }

                    return $this->redirect($this->container->get('router')->generate('HelloBundle_homepage'));
                }
            }
        }

        $log->addInfo('Show email form for new user');
        
        return $this->render('ApplicationSonataUserBundle:SocialConnect:email.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Sending email for binding social account with exist account in DB
     * 
     * @param type $email
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendSocialBindEmailAction($email)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $securityContext = $this->container->get('security.context');

            if ($securityContext->getToken()->isAuthenticated()) {
                $user = $securityContext->getToken()->getUser();

                switch ($this->getSocialConnectType()) {
                    case static::CONNECT_TYPE_OK:
                        $socialUid = $user->getOkUid();
                        $social = 'ok';
                        break;
                    case static::CONNECT_TYPE_VK:
                        $socialUid = $user->getVkUid();
                        $social = 'vk';
                        break;
                }

                $userWithEmailExists =  $this->getUserManager()->findUserByEmail($email);
                if (null === $userWithEmailExists) {
                    return $this->redirect($this->container->get('router')->generate('StoryBundle_homepage'));
                }

                $token = $userWithEmailExists->getConfirmationToken();
                if (null === $token) {
                    $tokenGenerator = $this->get('fos_user.util.token_generator');
                    $token = $tokenGenerator->generateToken();
                    $userWithEmailExists->setConfirmationToken($token);
                }

                $userManager = $this->getUserManager();
                $userManager->updateUser($user);
                $userManager->updateUser($userWithEmailExists);

                
                $securityContext->setToken(null);

                $params = array(
                    'email' => $email,
                    'token' => $token,
                    'social' => $social,
                    'socialUid' => $socialUid
                );

                if ($this->sendEmail($params)) {
                    // Handle XHR here
                    $result = array(
                        'status' => 'ok',
                        'message' => 'Письмо с инструкцией отправлено',
                        'redirect' => $this->redirect($this->container->get('router')->generate('HelloBundle_homepage'))
                    );

                    
                    return new Response(json_encode($result));        
                }

            }
        }
        
        return $this->redirect($this->container->get('router')->generate('StoryBundle_homepage'));
    }

    /**
     * Binding accounts and confirm
     * 
     * @param type $email
     * @param type $social
     * @param type $socialUid
     * @return type
     */
    public function socialBindAction($token, $social, $socialUid)
    {
        if (!in_array($social, array('ok', 'vk'))) {
            return $this->redirect($this->container->get('router')->generate('StoryBundle_homepage'));
        }

        $userManager = $this->getUserManager();
        $userWithEmail = $userManager->findUserByConfirmationToken($token);
        
        if (null === $userWithEmail) {
            return $this->redirect($this->container->get('router')->generate('StoryBundle_homepage'));
        }
        
        $email = $userWithEmail->getEmail();
        $emailSocial = $email;
        if ($social == 'ok') {
            $emailSocial = $socialUid . '@odnoklassniki.ru';
        } else if ($social == 'vk') {
            $emailSocial = $socialUid . '@vk.com';
        }
        
        $userSocial = $userManager->findUserBy(array($social . 'Uid' => $socialUid, 'email' => $emailSocial));

        if (null !== $userWithEmail && null !== $userSocial) {
                if ($social == 'ok') {
                    $userWithEmail->setOkUid($socialUid);
                } else if ($social == 'vk') {
                    $userWithEmail->setVkUid($socialUid);
                }

                $userWithEmail->setConfirmationToken(null);

                $userManager->updateUser($userWithEmail);
                $userManager->deleteUser($userSocial);

                try {
                    $params = array(
                        'email' => $email,
                        'social' => $social,
                        'socialUid' => $socialUid
                    );
                    
                    $this->sendEmail($params, 'ApplicationSonataUserBundle:SocialConnect:confirmBindEmail.txt.twig');
                } catch (\Exception $e) {
                    
                }

                return $this->render('ApplicationSonataUserBundle:SocialConnect:bind.html.twig', array(
                    'email' => $email,
                    'social' => $social,
                    'socialUid' => $socialUid
                ));
        }
        
        return $this->redirect($this->container->get('router')->generate('StoryBundle_homepage'));
    }

    /**
     * Connect
     *
     * @return RedirectResponse
     */
    public function connectVkAction()
    {
//xdebug_start_trace('/tmp/vk_get_access_token/'.date("Ymd-His"));
        $vkOAuthProxy = $this->get('vk.oauth.proxy');
        $isAuthorizedByVk = $vkOAuthProxy->authorize();

        if ($isAuthorizedByVk instanceof RedirectResponse) {
            // Redirect to /login_check
            $redirectResponse = $isAuthorizedByVk;
        } else {
            // Redirect to /login_check
            $redirectResponse = new RedirectResponse($this->generateUrl('_vk_security_check'));
        }

        return $redirectResponse;
    }

    /**
     * Connect
     *
     * @return RedirectResponse
     */
    public function connectOkAction()
    {
        $okOAuthProxy = $this->get('ok.oauth.proxy');
        $isAuthorizedByOk = $okOAuthProxy->authorize();

        if ($isAuthorizedByOk instanceof RedirectResponse) {
            // Redirect to /login_check
            $redirectResponse = $isAuthorizedByOk;
        } else {
            // Redirect to /login_check
            $redirectResponse = new RedirectResponse($this->generateUrl('_ok_security_check'));
        }

        return $redirectResponse;
    }
    
    /**
     * Get connect type by token.
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getSocialConnectType()
    {
        $token = $this->container->get('security.context')->getToken();
        $social = static::CONNECT_TYPE_NONE;

        if ($token instanceof VKUserToken) {
            $social = static::CONNECT_TYPE_VK;
        } elseif ($token instanceof OKUserToken) {
            $social = static::CONNECT_TYPE_OK;
        }

        return $social;
    }
    
    public function fillVkData(User $user, Logger $log) {

        $log->addInfo('VK uid: ' . $user->getVkUid() . '. Type VK');

        try {
            $vkApiHelper = $this->get('vk_api_helper');
        } catch (\Exception $e) {
            $log->addError('Error get vk_api_helper service');
            return;
        }

        try {
            $vkId = $vkApiHelper->getVkUid();
        } catch (\Exception $e) {
            $log->addError('Error get vk_uid from api');
            $vkId = null;
        }

        try {
            $userProfile = $vkApiHelper->getProfiles(array('uid', 'sex', 'bdate', 'first_name', 'last_name', 'nickname', 'screen_name', 'photo', 'photo_medium', 'photo_big', 'country', 'city', 'interests', 'movies', 'tv', 'books', 'games', 'about'));
            $info = $userProfile[0];
        } catch (\Exception $e) {
            $log->addError('Error get user info from api');
            return;
        }

        try {
            $friends = $vkApiHelper->getFriends();
        } catch (\Exception $e) {
            $log->addError('Error get friends info from api');
        }

        if (null !== $vkId) {
            $user = $this->getUserManager()->findUserBy(array('vkUid' => $vkId));
            $entityManager = $this->getDoctrine()->getEntityManager();

            $user->setEnabled(true);
            $user->setVkUId($vkId);

            $user->setVkFirstName($info['first_name']);
            $user->setFirstname($info['first_name']);
            $user->setVkLastName($info['last_name']);
            $user->setLastname($info['last_name']);

            // Sex
            $user->setSex($info['sex']);

            $log->addInfo('VK uid: ' . $vkId . '. Update main user info');

            // Photo
            if (null === $user->getPhoto()) {
                $this->addAvatar($user, $info['photo_medium']);
                $log->addInfo('VK uid: ' . $vkId . '. Add user avatar');
            }

            // Friends
            if (is_array($friends)) {
                $user->setVkFriends($friends);
                $log->addInfo('VK uid: ' . $vkId . '. Add user friends');
            }

            // Client IP
            $user->setClientIp($this->getRequest()->getClientIp());
            $log->addInfo('VK uid: ' . $vkId . '. Add client IP');

            $user->setVkData($info);

            $log->addInfo('VK uid: ' . $vkId . '. Set user json data');
        }
    }
    
    public function fillOkData(User $user, Logger $log)
    {
        $okOauthProxy = $this->get('ok.oauth.proxy');
        $okUid = $okOauthProxy->getUserId();
        $okApi = $okOauthProxy->getSdk();

        try {
            $userProfile = $okApi->api('users.getInfo', array('uids' => $okUid, 'fields' => 'uid,first_name,last_name,name,gender,pic_1,pic_2,location,age,birthday'));
            $info = $userProfile[0];
            $friends = $okApi->api('friends.get');
        } catch(\Exception $e) {
            return;
        }

        if (null != $okUid) {
            $user = $this->getUserManager()->findUserBy(array('okUid' => $okUid));
            $entityManager = $this->getDoctrine()->getEntityManager();

            $user->setEnabled(true);
            $user->setOkUid($okUid);

            $user->setOkFirstName($info['first_name']);
            $user->setFirstname($info['first_name']);
            $user->setOkLastName($info['last_name']);
            $user->setLastname($info['last_name']);

            // Sex
            $sex = 0;
            if ($info['gender'] == 'male') {
                $sex = 2;
            } elseif ($info['gender'] == 'female') {
                $sex = 1;
            }
            $user->setSex($sex);

            // Photo
            if (null === $user->getPhoto()) {
                $this->addAvatar($user, $info['pic_2']);
            }

            // Friends
            $user->setOkFriends($friends);

            // Client IP
            $user->setClientIp($this->getRequest()->getClientIp());

            $user->setOkData($info);

        }
        
    }

    /**
     * Fill user depends on connect type.
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     *
     * @throws \Exception
     */
    protected function fillUserDependsOnConnectType(User $user, $log)
    {
        $log->addInfo('Switch update user data by connection type');

        switch ($connectType = $this->getSocialConnectType()) {
            case static::CONNECT_TYPE_VK:
                $this->fillVkData($user, $log);
                break;
            case static::CONNECT_TYPE_OK:
                $this->fillOkData($user, $log);
                break;
            default:
                throw new \Exception('Нет коннекта ни к одной из поддерживаемых сетей');
        }

        return $user;
    }

    /**
     * Add avatar.
     *
     * @param object $user     User instance
     * @param string $imageUrl Image url
     */
    protected function addAvatar($user, $imageUrl)
    {
        try {
            // Download avatar
            $media = new Media;
            $browser = new Browser();
            $data = $browser->get($imageUrl);
            $filename = tempnam(sys_get_temp_dir(), 'avatar');
            file_put_contents($filename, $data->getContent());

            // Save avatar
            $media->setName($filename);
            $media->setEnabled(true);
            $media->setBinaryContent($filename);
            $media->setContext('avatar');
            $media->setProviderName('sonata.media.provider.image');
            $mediaManager = $this->get('sonata.media.manager.media');
            $mediaManager->save($media);

            // Attach avatar
            $user->setPhoto($media);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send mail with link to confirm
     * 
     * @param type $email
     * @param type $uniq 
     */
    private function sendEmail($params, $template = null)
    {
        $transport = $this->get('swiftmailer.transport');
        $mailer = \Swift_Mailer::newInstance($transport);
        
        if (null === $template) {
            $template = 'ApplicationSonataUserBundle:SocialConnect:bindEmail.txt.twig';
        }

        $message = \Swift_Message::newInstance()
                    ->setSubject('Настоящая любовь - Связывание аккаунтов')
                    ->setFrom(array('reallove.videomore@gmail.com' => 'Настоящая любовь '))
                    ->setTo(array($params['email']))
                    ->setBody($this->renderView($template, $params), 'text/html');
        
        if(!$mailer->send($message)){
                $mailer->getTransport()->stop();

                return false;
        }

        $mailer->getTransport()->stop();

        return true;

    }      

    /**
     * Refresh token.
     *
     * @param object $user User instance
     */
    protected function refreshToken($user)
    {
        if ($user instanceof UserInterface) {
            $roles = $user->getRoles();
        } else {
            $roles = array();
        }

        $newToken = $this->createToken($user, $roles);
        $this->container->get('security.context')->setToken($newToken);
    }

    protected function createToken($user, $roles)
    {
        $tokenClass = get_class($this->container->get('security.context')->getToken());
        $newToken = new $tokenClass($user, $roles);

        return $newToken;
    }
    
    protected function getUserManager()
    {
        if (null === $this->userManager) {
            $this->userManager = $this->get('fos_user.user_manager');
        }
        
        return $this->userManager;
    }
}
