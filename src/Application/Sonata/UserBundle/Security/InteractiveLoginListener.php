<?php

namespace Application\Sonata\UserBundle\Security;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Security\InteractiveLoginListener as BaseListener;
use DateTime;

/**
 * Interactive login listener class.
 */
class InteractiveLoginListener extends BaseListener
{
    /**
     * On security interactive login listener action.
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event Event instance
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var $user UserInterface */
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof UserInterface && $user->getId()) {
            // Don't save new social user:
            $user->setLastLogin(new DateTime());
            $this->userManager->updateUser($user, false);
        } else {
            $event->stopPropagation();
        }
    }
}
