<?php

namespace Ailove\VKApiHelperBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Knp\Menu\ItemInterface as MenuItemInterface;

/**
 * Admin class for manage ages 
 */
class MessageAdmin extends Admin
{

    /**
     * Configure form fields
     * 
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->with('Основные')
                    ->add('text', null, array('required' => false, 'label' => 'Текст'))
                ->end();
    }

    /**
     * Configure list fields
     * 
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->addIdentifier('id')
                ->add('text', null, array('label' => 'Текст'));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('show')
            ->add('send', $this->getRouterIdParameter() . '/send');
    }

    
    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if (!in_array($action, array('edit', 'view'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $this->getRequest()->get('id');

        $item = $menu->addChild(
            'Отправить',
            array('uri' => $admin->generateUrl('send', array('id' => $id, 'uniqid' => $admin->getUniqid())))
        );

        $item->setLinkAttribute('class', 'btn btn-large btn-primary');
        $item->setLinkAttribute('style', 'margin: auto; display: inline-block; text-shadow: none; padding: 9px 14px; background-color: #0074CC; background-position: 0 0;');
    }
}