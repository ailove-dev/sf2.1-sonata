<?php

/**
 * This file is part of the "AiloveOliport" package.
 *
 * Copyright Ailove company <info@ailove.ru>
 *
 */

namespace Ailove\OKBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use FOS\UserBundle\Model\UserManagerInterface;

use Application\Sonata\UserBundle\Entity\Manager\UserManager;
use Application\Sonata\UserBundle\Entity\User;

use Ailove\OKBundle\Service\OKOauthSessionProxy;

/**
 * Odnoklassniki user provider.
 *
 * @author Marsel Kagarmanov <m.kagarmanov@ailove.ru>
 */
class OKUserProvider implements UserProviderInterface
{
    /**
     * @var \Ailove\OKBundle\Service\OKOauthSessionProxy
     */
    protected $oauthProxy;

    /**
     * @var \Application\Sonata\UserBundle\Entity\Manager\UserManager
     */
    protected $userManager;

    /**
     * Constructor.
     *
     * @param \Application\Sonata\UserBundle\Service\OdnoklassnikiOauthSessionProxy $oauthProxy  Odnoklassniki Oauth2Proxy instance
     * @param \FOS\UserBundle\Model\UserManagerInterface                            $userManager User manager
     */
    public function __construct(OKOauthSessionProxy $oauthProxy, UserManagerInterface $userManager)
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
     * Find Odnoklassniki user by ID.
     *
     * @param integer $okUid Odnoklassniki user id
     *
     * @return \FOS\UserBundle\Model\UserInterface
     *
     * @throws \Exception
     */
    public function findUserByOkId($okUid)
    {
        if (empty($okUid)) {
            throw new \Exception('Odnoklassniki ID не определён');
        }

        return $this->userManager->findUserBy(array('okUid' => $okUid));
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
        $user = $this->findUserByOkId($username);

        if (empty($user)) {
            try {
                $this->oauthProxy->authorize();
                $okUid = $this->oauthProxy->getUserId();

            } catch (\Exception $e) {
                $okUid = null;
            }

            if (!empty($okUid)) {
                $user = new User();
                $user->setEnabled(true); // Temporary enable user - to access connect page
                $user->setPassword('');
                $user->setUsername($okUid);
                $user->setEmail($okUid . '@odnoklassniki.ru');
                $user->setOkUid($okUid);
                $user->addRole(User::ROLE_OK_USER);
                $this->userManager->updateUser($user, false);
            }
        }

        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on Odnoklassniki');
        }

        return $user;
    }

    /**
     * Refresh user.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user User instance
     *
     * @return \Application\Sonata\UserBundle\Entity\User|\FOS\UserBundle\Model\UserInterface
     *
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        /** @var $user \Application\Sonata\UserBundle\Entity\User */
        if (!$this->supportsClass(get_class($user)) || !$user->getOkUid()) {
            throw new UnsupportedUserException(sprintf('[OdnoklassnikiUserProvider] Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getOkUid());
    }
}
