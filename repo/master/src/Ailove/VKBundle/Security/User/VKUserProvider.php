<?php

namespace Ailove\VKBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Application\Sonata\UserBundle\Entity\Manager\UserManager;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\Container;

use Oauth2Proxy;

/**
 * VK user provider.
 */
class VKUserProvider implements UserProviderInterface
{
    /**
     * @var \Oauth2Proxy
     */
    protected $oauthProxy;

    /**
     * @var \Application\Sonata\UserBundle\Entity\Manager\UserManager
     */
    protected $userManager;

    /**
     * Constructor.
     *
     * @param \Oauth2Proxy                               $oauthProxy  VK Oauth2Proxy instance
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager User manager
     */
    public function __construct(Oauth2Proxy $oauthProxy, UserManagerInterface $userManager)
    {
        $this->oauthProxy = $oauthProxy;
        $this->userManager = $userManager;
    }

    /**
     * Check class support.
     *
     * @param string $class Classname
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }

    /**
     * Find VK user by ID.
     *
     * @param integer $vkId VK user id
     *
     * @return \FOS\UserBundle\Model\UserInterface
     *
     * @throws \Exception
     */
    public function findUserByVkId($vkId)
    {
        if (empty($vkId)) {
            throw new \Exception('VK ID не определён');
        }

        $user = $this->userManager->findUserBy(array('vkUid' => $vkId));

        return $user;
    }

    /**
     * Load user by username.
     *
     * @param mixed $username Username
     *
     * @return \Application\Sonata\UserBundle\Entity\User|\FOS\UserBundle\Model\UserInterface
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByVkId($username);

        if (empty($user)) {
            try {
                $this->oauthProxy->authorize();
                $vkId = $this->oauthProxy->getUserId();

            } catch (\Exception $e) {
                $vkId = null;
            }

            if (!empty($vkId)) {
                $user = new User();
                $user->setEnabled(true); // Temporary enable user - to access connect page
                $user->setPassword('');
                $user->setUsername($vkId);
                $user->setEmail($vkId . '@vk.com');
                $user->setVkUid($vkId);
                $user->addRole(User::ROLE_VK_USER);
                $this->userManager->updateUser($user, false);
            }
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on VK');
        }

        return $user;
    }

    /**
     * Refresh user.
     *
     * @param UserInterface $user User instance
     *
     * @return UserInterface
     *
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        /** @var $user \Application\Sonata\UserBundle\Entity\User */
        if (!$this->supportsClass(get_class($user)) || !$user->getVkUid()) {
            throw new UnsupportedUserException(sprintf('[VKUserProvider] Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getVkUid());
    }
}
