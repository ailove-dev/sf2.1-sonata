<?php

namespace Application\Sonata\UserBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Application\Sonata\UserBundle\Entity\User;

/**
 * Registration form handler.
 */
class RegistrationFormHandler
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
     * @var \FOS\UserBundle\Mailer\MailerInterface
     */
    protected $mailer;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\Form\Form                     $form        Form instance
     * @param \Symfony\Component\DependencyInjection\Container $container   Request
     * @param \FOS\UserBundle\Model\UserManagerInterface       $userManager User manager
     * @param \FOS\UserBundle\Mailer\MailerInterface           $mailer      Mailer
     */
    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Process form.
     *
     * @param bool $confirmation Confirmation flag
     *
     * @return bool
     */
    public function process($confirmation = false)
    {
        $user = $this->userManager->createUser();

        $this->form->setData($user);

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user, $confirmation);

                return true;
            }
        }

        return false;
    }

    /**
     * Success form processing.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user         User instance
     * @param bool                                $confirmation Confirmation flag
     */
    protected function onSuccess(UserInterface $user, $confirmation)
    {
        if ($confirmation) {
            $user->setEnabled(false);
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);
        } else {
            $user->setEnabled(true);
        }

        // Age
        if (null !== ($birthday = $user->getDateOfBirth())) {
            $currDatetime = new \Datetime();
            $age = $currDatetime->diff($birthday)->format('%y');

            $user->setAge($age);
        }

        // Set username = email
        $user->setUsername($user->getEmail());
        // Set ROLE_REGISTERED role
        $user->addRole(User::ROLE_REGISTERED);

        $this->userManager->updateUser($user);
    }
}
