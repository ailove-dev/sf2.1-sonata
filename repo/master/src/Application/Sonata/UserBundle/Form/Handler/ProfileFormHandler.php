<?php

/**
 * This file is part of the "AiloveOliport" package.
 *
 * Copyright Ailove company <info@ailove.ru>
 *
 */

namespace Application\Sonata\UserBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Form\Model\CheckPassword;
use Symfony\Component\DependencyInjection\Container;

/**
 * Profile form handler.
 *
 * @author Dmitry Bykadorov <dmitry.bykadorov@gmail.com>
 */
class ProfileFormHandler
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface
     */
    protected $userManager;

    /**
     * @var \Symfony\Component\Form\Form
     */
    protected $form;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Form\Form                     $form        Form instance
     * @param \Symfony\Component\DependencyInjection\Container $container   Request
     * @param \FOS\UserBundle\Model\UserManagerInterface       $userManager User manager
     */
    public function __construct(Form $form, Container $container, UserManagerInterface $userManager)
    {
        $this->form = $form;
        $this->request = $container->get('request');
        $this->userManager = $userManager;
    }

    /**
     * Process form.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user User instance
     *
     * @return bool
     */
    public function process(UserInterface $user)
    {
        $this->form->setData(new CheckPassword($user));

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user);

                return true;
            }

            // Reloads the user to reset its username. This is needed when the
            // username or password have been changed to avoid issues with the
            // security layer.
            $this->userManager->reloadUser($user);
        }

        return false;
    }

    /**
     * Success form processing.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user User instance
     */
    protected function onSuccess(UserInterface $user)
    {
        $this->userManager->updateUser($user);
    }
}
