<?php

namespace Application\Sonata\AdminThemeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class JsonType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->resetClientTransformers();
        $builder->appendClientTransformer(new JsonTransformer());

    }

    public function getDefaultOptions(array $options)
    {
        return array();
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'sonata_json';
    }

//    public function buildView(FormView $view, FormInterface $form)
//    {
//    }
}
