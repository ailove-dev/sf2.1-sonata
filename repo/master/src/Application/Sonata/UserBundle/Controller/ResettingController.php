<?php

/**
 * This file is part of the "AiloveOliport" package.
 *
 * Copyright Ailove company <info@ailove.ru>
 *
 */

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ResettingController as BaseResettingController;

/**
 * Controller managing the resetting of the password
 *
 * @author Dmitry Bykadorov <dmitry.bykadorov@gmail.com>
 */
class ResettingController extends BaseResettingController
{
    /**
     * Generate the redirection url when the resetting is completed.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('AiloveOlimpBundle_homepage');
    }
}
