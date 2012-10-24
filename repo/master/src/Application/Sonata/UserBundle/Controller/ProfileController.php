<?php

/**
 * This file is part of the "AiloveOliport" package.
 *
 * Copyright Ailove company <info@ailove.ru>
 *
 */

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as FOSUserProfileController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller managing the user profile
 *
 * @author Dmitry Bykadorov <dmitry.bykadorov@gmail.com>
 */
class ProfileController extends FOSUserProfileController
{
    /**
     * Show the user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function showAction()
    {
        throw new NotFoundHttpException('Not implemeted yet');

        parent::showAction();
    }

    /**
     * Edit the user
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction()
    {
        throw new NotFoundHttpException('Not implemeted yet');

        parent::editAction();
    }

    /**
     * @param string $action Action name
     * @param string $value  Value
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function setFlash($action, $value)
    {
        throw new NotFoundHttpException('Not implemeted yet');

        parent::setFlash($action, $value);
    }
}
