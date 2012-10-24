<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\MediaBundle\Admin;

use Sonata\MediaBundle\Admin\GalleryAdmin as BaseGalleryAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class GalleryAdmin extends BaseGalleryAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $context = $this->getPersistentParameter('context');

        if (!$context) {
            $context = $this->pool->getDefaultContext();
        }

        $formats = array();
        foreach((array)$this->pool->getFormatNamesByContext($context) as $name => $options) {
            $formats[$name] = $name;
        }

        $contexts = array();
        foreach((array)$this->pool->getContexts() as $context => $format) {
            $contexts[$context] = $context;
        }

        $formMapper
            ->add('context', 'choice', array('choices' => $contexts))
            ->add('enabled', null, array('required' => false))
            ->add('name')
            ->add('defaultFormat', 'choice', array('choices' => $formats))
            ->add('galleryHasMedias', 'sonata_type_collection', array(
                'by_reference' => false
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
                'link_parameters' => array('context' => $context)
            ))
        ;
    }
}