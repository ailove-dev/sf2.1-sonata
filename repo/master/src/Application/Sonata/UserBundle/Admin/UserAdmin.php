<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use FOS\UserBundle\Model\UserManagerInterface;

/**
 * UserAdmin 
 */
class UserAdmin extends BaseUserAdmin
{
    protected $formOptions = array(
        'validation_groups' => 'Profile'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('groups')
            ->add('enabled')
            ->add('createdAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('email')
            ->add('groups');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
                ->add('username')
                ->add('email')
            ->end()
            ->with('Groups')
                ->add('groups')
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('enabled')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text', array('required' => false))
                ->add('sex', 'choice', array('required' => false, 'label' => 'Пол', 'choices' => array(1 => 'Жен', 2 => 'Муж')))
                ->add('age', null, array('required' => false, 'label' => 'Возраст'))
                ->add('dateOfBirth', null, array('required' => false, 'label' => 'Дата рождения', 'years' => range(1910,2012)))
                ->add('firstname', null, array('required' => false, 'label' => 'Имя'))
                ->add('lastname', null, array('required' => false, 'label' => 'Фамилия'))
                ->add('cityText', null, array('required' => false, 'label' => 'Город'))
                ->add('photo', 'sonata_type_model_list', array(
                    'required' => false, 'label' => 'Аватар'))
            ->end()
//            ->with('Авторизация')
//                ->add('expiresAt', null, array('required' => false, 'label' => 'Активен до'))
//            ->end()
            ->with('Группы')
                ->add('groups', 'sonata_type_model', array(
                    'required' => false, 
                    'by_reference' => false,
                    'multiple' => true,
                    'expanded' => true))
            ->end()
            ->with('География')
                ->add('clientIp', 'text', array('required' => false, 'label' => 'IP адрес'))
                ->add('city', 'sonata_type_model', array(
                    'required' => false, 
                    'expanded' => false, 'label' => 'Город'), array('edit' => 'standard'))
                ->add('country', 'sonata_type_model', array(
                    'required' => false, 
                    'expanded' => false, 'label' => 'Страна'), array('edit' => 'standard'))
            ->end()
            ->with('VK')
                ->add('vkUid', null, array('required' => false, 'label' => 'UID'))
                ->add('vkFirstName', null, array('required' => false, 'label' => 'Имя'))
                ->add('vkLastName', null, array('required' => false, 'label' => 'Фамилия'))
                ->add('vkBirthday', null, array('required' => false, 'label' => 'Дата рождения', 'years' => range(1910,2012)))
                ->add('vkFriends', 'sonata_json', array('required' => false, 'label' => 'Друзья (uids)'))
                ->add('vkData', 'sonata_json', array('required' => false, 'label' => 'Data json'))
            ->end()
            ->with('OK')
                ->add('okUid', null, array('required' => false, 'label' => 'UID'))
                ->add('okFirstName', null, array('required' => false, 'label' => 'Имя'))
                ->add('okLastName', null, array('required' => false, 'label' => 'Фамилия'))
                ->add('okBirthday', null, array('required' => false, 'label' => 'Дата рождения', 'years' => range(1910,2012)))
                ->add('okFriends', 'sonata_json', array('required' => false, 'label' => 'Друзья (uids)'))
                ->add('okData', 'sonata_json', array('required' => false, 'label' => 'Data json'))
            ->end();
    }
}
