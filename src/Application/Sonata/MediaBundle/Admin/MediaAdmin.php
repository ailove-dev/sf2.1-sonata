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

use Sonata\MediaBundle\Admin\BaseMediaAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Media admin 
 */
class MediaAdmin extends Admin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('context');
    }

    /**
     * @param string $name
     *
     * @return null|string
     */
    public function getTemplate($name)
    {
        if ($name == 'layout') {
            return 'AdminThemeBundle::layout.html.twig';
        }

        if ($name == 'list') {
            return 'AdminThemeBundle:MediaAdmin:list.html.twig';
        }

        if (isset($this->templates[$name])) {
            return $this->templates[$name];
        }

        return null;
    }

    /**
     * Get list template MediaAdmin
     * @return string 
     */
    public function getListTemplate()
    {
        return 'AdminThemeBundle:MediaAdmin:list.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper->remove('enabled');
        $formMapper->remove('copyright');
        $formMapper->remove('cdnIsFlushable');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'AdminThemeBundle:MediaAdmin:list_image.html.twig'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                )
            ));
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('view', $this->getRouterIdParameter() . '/view');

    }
//    
//    /**
//     *
//     * @param ErrorElement $errorElement
//     * @param type $object 
//     */
//    public function validate(ErrorElement $errorElement, $object)
//    {
//        $errorElement
//            ->with('binaryContent')
//                ->assertFile(array('mimeTypes' => array(
//                    'application/pdf', 
//                    'application/x-pdf',
//                    'application/rtf',
//                    'text/html',
//                    'text/rtf',
//                    'text/plain',
//                    'application/excel',
//                    'application/msword',
//                    'application/vnd.ms-office',
//                    'application/vnd.ms-excel',
//                    'application/vnd.ms-powerpoint',
//                    'application/vnd.ms-powerpoint',
//                    'application/vnd.oasis.opendocument.text',
//                    'application/vnd.oasis.opendocument.graphics',
//                    'application/vnd.oasis.opendocument.presentation',
//                    'application/vnd.oasis.opendocument.spreadsheet',
//                    'application/vnd.oasis.opendocument.chart',
//                    'application/vnd.oasis.opendocument.formula',
//                    'application/vnd.oasis.opendocument.database',
//                    'application/vnd.oasis.opendocument.image',
//                    'text/comma-separated-values',
//                    'text/xml',
//                    'application/xml',
//                    'application/zip',
//                    'application/x-gzip',
//                    'application/x-tar',
//                    'application/x-rar',
//                    'application/x-rar-compressed',
//                    'image/jpeg',
//                    'image/gif',
//                    'image/gif',
//                    'image/png'
//                    )))
//            ->end();
//    }
}