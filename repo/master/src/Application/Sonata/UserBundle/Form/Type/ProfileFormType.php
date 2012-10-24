<?php

/**
 * This file is part of the "AiloveOliport" package.
 *
 * Copyright Ailove company <info@ailove.ru>
 *
 */

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Profile form.
 *
 * @author Dmitry Bykadorov <dmitry.bykadorov@gmail.com>
 */
class ProfileFormType extends AbstractType
{
    /**
     * @var string User class name
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Build form.
     *
     * @param \Symfony\Component\Form\FormBuilder $builder Form builder
     * @param array                               $options Options array
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $child = $builder->create('user', 'form', array('data_class' => $this->class));
        $this->buildUserForm($child, $options);

        $builder
            ->add($child)
            ->add('current', 'password');
    }

    /**
     * Defaults.
     *
     * @param array $options Options array
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'FOS\UserBundle\Form\Model\CheckPassword');
    }

    /**
     * Return form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'oliport_user_profile';
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilder $builder Form builder
     * @param array       $options Options array
     */
    protected function buildUserForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email');
    }
}
