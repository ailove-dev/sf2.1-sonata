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
 * Social connect form type.
 *
 * @author Dmitry Bykadorov <dmitry.bykadorov@gmail.com>
 */
class SocialConnectPasswordCheckFormType extends AbstractType
{
    /**
     * Build form.
     *
     * @param \Symfony\Component\Form\FormBuilder $builder Form builder instance
     * @param array                               $options Options array
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('password', 'password', array(
            'label' => 'Пароль',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'connect_password';
    }
}
