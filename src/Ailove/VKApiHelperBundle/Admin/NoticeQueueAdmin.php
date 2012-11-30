<?php

namespace Ailove\VKApiHelperBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Admin class for manage ages 
 */
class NoticeQueueAdmin extends Admin
{
    /**
     * Get filter parameters
     * 
     * @return type
     */
    public function getFilterParameters()
    {
        $this->datagridValues = array_merge(array(
            'status' => array(
                'value' => 1,
            )
                ), $this->datagridValues
        );

        return parent::getFilterParameters();
    }

    /**
     * Configure form fields
     * 
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->with('Основные')
                    ->add('status', null, array('label' => 'Статус'))
                    ->add('createAt', null, array('label' => 'Дата создания'))
                    ->add('message', 'sonata_type_model', array('label' => 'Сообщение'))
                    ->add('user', 'sonata_type_model_list', array(
                    'required' => false, 'label' => 'Пользователь'))
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
                ->add('status', null, array('label' => 'Статус'))
                ->add('createAt', null, array('label' => 'Дата создания'))
                ->add('message', null, array('label' => 'Сообщение'))
                ->add('user', null, array('label' => 'Пользователь'));
    }
    
    /**
     * Configure datagrid filters.
     *
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
                ->add('message', null, array('label' => 'Сообщение'))
                ->add('status', 'doctrine_orm_choice', array('label' => 'Статус'), 'choice', array(
                    'choices' => array(
                        1 => 'В очереди',
                        2 => 'Отправлено',
                        3 => 'Не отправлено',
                    ),
                    'required' => false
                        )
                );
    }
}