<?php

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

use FOS\UserBundle\Controller\RegistrationController as FOSUserRegistrationController;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller managing the registration
 */
class RegistrationController extends FOSUserRegistrationController
{
     public function registerAction(Request $request)
     {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted(User::ROLE_REGISTERED)) {
            return new RedirectResponse($this->container->get('router')->generate('HelloBundle_homepage'));
        }

        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);

        if ($process) {
            $user = $form->getData();

            $em = $this->container->get('doctrine')->getEntityManager();

            $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());

            if ($this->container->get('request')->isXmlHttpRequest()) {
                // Handle XHR here
                $result = array(
                    'status' => 'ok',
                    'message' => 'Пользователь создан. На email ' . $user->getEmail() . ' отправлено письмо с инструкцией по активации акккаунта.',
                );

                return new Response(json_encode($result));
            }
        } else {
            if ($this->container->get('request')->isXmlHttpRequest()) {
                // Handle XHR here
                $result = array(
                    'status' => 'error',
                    'message' => 'Ввведите все данные формы'
                );

                return new Response(json_encode($result));
            } 
        }

        $template = 'FOSUserBundle:Registration:register.html.'.$this->getEngine();

        return $this->container->get('templating')->renderResponse($template, array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     *
     * @param object $token Token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function confirmAction(Request $request, $token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('Регистрационный токен "%s" не найден', $token));
        }

        $dispatcher = $this->container->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setExpiresAtNull();
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());

        // Set ROLE_REGISTERED role
        $user->addRole(User::ROLE_REGISTERED);

        $event = new GetResponseUserEvent($user, $request);

        $this->container->get('fos_user.user_manager')->updateUser($user);
        //$this->container->get('session')->setFlash('notice', 'Регистрация успешно подтверждена. Вы были автоматически авторизованы.');

        if (null === $response = $event->getResponse()) {
            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Success page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->container->get('templating')->renderResponse('ApplicationSonataUserBundle:Registration:confirmed.html.'.$this->getEngine(), array('user' => $user));
    }
}
