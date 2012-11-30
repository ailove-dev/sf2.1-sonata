<?php

namespace Ailove\VKBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserChecker;

use Ailove\VKBundle\Security\Authentication\Token\VKUserToken;

use Oauth2Proxy;

/**
 * VK authentication provider.
 */
class VKProvider implements AuthenticationProviderInterface
{
    /**
     * @var \Oauth2Proxy
     */
    private $oauthProxy;

    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $userProvider;

    /**
     * @var \Symfony\Component\Security\Core\User\UserChecker
     */
    protected $userChecker;

    /**
     * Constructor.
     *
     * @param \Oauth2Proxy                                                $oauthProxy   VK oAuth proxy
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider User provider
     * @param \Symfony\Component\Security\Core\User\UserChecker           $userChecker  UserChecker
     */
    public function __construct(Oauth2Proxy $oauthProxy, UserProviderInterface $userProvider, UserChecker $userChecker)
    {
        $this->userProvider = $userProvider;
        $this->oauthProxy = $oauthProxy;
        $this->userChecker = $userChecker;
    }

    /**
     * Authenticate user.
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token     Auth token
     * @param boolean                                                              $checkUser Check user
     *
     * @return \Application\Sonata\UserBundle\Security\VK\Authentication\Token\VKUserToken|null
     *
     * @throws \Exception|\Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticate(TokenInterface $token, $checkUser = true)
    {
        if (!$this->supports($token)) {

            return null;
        }


        try {
            if ($this->oauthProxy->authorize() === true) {
                $authenticatedToken = $this->createAuthenticatedToken($this->oauthProxy->getUserId(), $checkUser);

                return $authenticatedToken;
            }
        } catch (AuthenticationException $failed) {
            throw $failed;
        } catch (\Exception $failed) {
            throw new AuthenticationException('VK auth error: ' . $failed->getMessage(), $failed->getMessage(), $failed->getCode(), $failed);
        }


        throw new AuthenticationException('Не получилось загрузить данные пользователя через VK');
    }

    /**
     * Is provider supports $token.
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token Auth token.
     *
     * @return bool
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof VKUserToken;
    }

    /**
     * Create auth token.
     *
     * @param int     $vkId      VK user ID
     * @param boolean $checkUser Check user
     *
     * @return \Application\Sonata\UserBundle\Security\VK\Authentication\Token\VKUserToken
     *
     * @throws \RuntimeException
     */
    protected function createAuthenticatedToken($vkId, $checkUser = true)
    {
        if (null === $this->userProvider) {

            return new VKUserToken($vkId);
        }

        $user = $this->userProvider->loadUserByUsername($vkId);

        if (true === $checkUser) {
            $this->userChecker->checkPostAuth($user);
        }

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('User provider did not return an implementation of user interface.');
        }

        return new VKUserToken($user, $user->getRoles());
    }
}
