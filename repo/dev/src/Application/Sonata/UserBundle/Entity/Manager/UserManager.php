<?php

namespace Application\Sonata\UserBundle\Entity\Manager;

use FOS\UserBundle\Entity\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * User manager.
 */
class UserManager extends BaseUserManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $serviceContainer;

    /** @var \Symfony\Bridge\Monolog\Logger */
    protected $logger;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory        Security encoder factory
     * @param \FOS\UserBundle\Util\CanonicalizerInterface                      $usernameCanonicalizer Username canonicalizer
     * @param \FOS\UserBundle\Util\CanonicalizerInterface                      $emailCanonicalizer    Email canonicalizer
     * @param \Doctrine\ORM\EntityManager                                      $em                    Entity manager
     * @param string                                                           $class                 Classname
     * @param \Symfony\Component\DependencyInjection\Container                 $serviceContainer      DI container
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        CanonicalizerInterface $usernameCanonicalizer,
        CanonicalizerInterface $emailCanonicalizer,
        EntityManager $em,
        $class,
        Container $serviceContainer)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $em, $class);

        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->serviceContainer = $serviceContainer;

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;

        $this->logger = $this->serviceContainer->get('logger');
    }

    /**
     * Refresh user.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user User instance
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     *
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        $this->logger->info('UserManager::refreshUser');
        $class = $this->getClass();
        if (!$user instanceof $class) {
            throw new UnsupportedUserException('Account is not supported.');
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Updates a user.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user     User instance
     * @param bool                                $andFlush Flush flag
     */
    public function updateUser(FOSUserInterface $user, $andFlush = true)
    {
        $this->logger->info('UserManager::updateUser');

        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        if ($andFlush) {
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    /**
     * Update canonical fields.
     *
     * @param FOSUserInterface $user User instance
     */
    public function updateCanonicalFields(FOSUserInterface $user)
    {
        $user->setUsernameCanonical($this->canonicalizeUsername($user->getUsername()));
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    /**
     * Loads a user by username.
     *
     * It is strongly discouraged to call this method manually as it bypasses
     * all ACL checks.
     *
     * @param string $username Username
     *
     * @return UserInterface
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function loadUserByUsername($username)
    {
        $this->logger->info('UserManager::loadUserByUsername');

        $user = $this->findUserByUsernameOrEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }

    /**
     * Finds a user by username
     *
     * @param string $username Username
     *
     * @return UserInterface
     */
    public function findUserByUsername($username)
    {
        $this->logger->info('UserManager::findUserByUsername');

        return $this->findUserBy(
            array('usernameCanonical' => $this->canonicalizeUsername($username))
        );
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string $usernameOrEmail Username or email string
     *
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        $this->logger->info('UserManager::findUserByUsernameOrEmail');

        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class Classname
     *
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return $class === $this->getClass();
    }
}
