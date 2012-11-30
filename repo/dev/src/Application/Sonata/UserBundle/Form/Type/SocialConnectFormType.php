<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Social connect form type.
 *
 */
class SocialConnectFormType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method gets called for each type in the hierarchy starting from the
     * top most type.
     * Type extensions can further modify the form.
     *
     * @param FormBuilder $builder The form builder
     * @param array       $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'text', array(
                'label' => 'Email',
            ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'connect_register';
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
        return array();
    }
}
