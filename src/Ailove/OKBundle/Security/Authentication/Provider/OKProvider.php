<?php

namespace Ailove\OKBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserChecker;

use Ailove\OKBundle\Service\OKOauthSessionProxy;
use Ailove\OKBundle\Security\Authentication\Token\OKUserToken;

/**
 * Odnoklassniki authentication provider.
 */
class OKProvider implements AuthenticationProviderInterface
{
    /**
     * @var \Application\Sonata\UserBundle\Service\OdnoklassnikiOauthSessionProxy
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
     * @param \Application\Sonata\UserBundle\Service\OdnoklassnikiOauthSessionProxy $oauthProxy   Odnoklassniki oAuth proxy
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface           $userProvider User provider
     * @param \Symfony\Component\Security\Core\User\UserChecker                     $userChecker  User checker
     */
    public function __construct(OKOauthSessionProxy $oauthProxy, UserProviderInterface $userProvider, UserChecker $userChecker)
    {
        $this->userProvider = $userProvider;
        $this->oauthProxy = $oauthProxy;
        $this->userChecker = $userChecker;
    }

    /**
     * Authenticate user.
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token Auth token
     *
     * @return \Application\Sonata\UserBundle\Security\Odnoklassniki\Authentication\Token\OdnoklassnikiUserToken|null
     *
     * @throws \Exception|\Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        try {
            if ($this->oauthProxy->authorize() === true) {

                $authenticatedToken = $this->createAuthenticatedToken($this->oauthProxy->getUserId());

                return $authenticatedToken;
            }
        } catch (AuthenticationException $failed) {
            throw $failed;
        } catch (\Exception $failed) {
            throw new AuthenticationException('Odnoklassniki auth error: ' . $failed->getMessage(), $failed->getMessage(), $failed->getCode(), $failed);
        }

        throw new AuthenticationException('Не получилось загрузить данные пользователя через Odnoklassniki');
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
        return $token instanceof OKUserToken;
    }

    /**
     * Create auth token.
     *
     * @param int $odnoklassnikiId Odnoklassniki user ID
     *
     * @return \Application\Sonata\UserBundle\Security\Odnoklassniki\Authentication\Token\OdnoklassnikiUserToken
     *
     * @throws \RuntimeException
     */
    protected function createAuthenticatedToken($odnoklassnikiId)
    {

        if (null === $this->userProvider) {

            return new OKUserToken($odnoklassnikiId);
        }

        $user = $this->userProvider->loadUserByUsername($odnoklassnikiId);

        $this->userChecker->checkPostAuth($user);

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('User provider did not return an implementation of user interface.');
        }

        return new OKUserToken($user, $user->getRoles());
    }
}
