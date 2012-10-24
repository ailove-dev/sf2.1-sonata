<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Registration form.
 */
class RegistrationFormType extends AbstractType
{
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array('label' => 'Имя'))
            ->add('lastName', 'text', array('label' => 'Фамилия'))
            ->add('dateOfBirth', 'date', array('label' => 'Дата рождения', 'years' => range(1910,2012)))
            ->add('cityText', 'text', array('label' => 'Город'))
            ->add('email', 'email', array('label' => 'E-mail'))
            ->add('username', 'hidden')
            ->add('plainPassword', 'repeated', array('label' => 'Пароль', 'type' => 'password'));
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
        return array('data_class' => $this->class);
    }

    /**
     * Return form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'firstlove_registration_form_type';
    }
}
