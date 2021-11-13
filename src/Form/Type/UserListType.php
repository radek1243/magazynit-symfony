<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class UserListType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('users', CollectionType::class, array(
            'entry_type' => UserRowFormType::class,
            'entry_options' => array('label' => false)
        ));
    }
}