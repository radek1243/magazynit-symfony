<?php

namespace App\Form\Type;

use App\Entity\Role;
use App\Entity\User;
use App\Form\GetUserForm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login', TextType::class, array(
            'label' => 'Login: ',
            'attr' => array(
                'maxlength' => 20
            ),
            'disabled' => $options['login_disabled']
        ));
        $builder->add('password', PasswordType::class, array(
            'label' => 'Hasło: ',
            'required' => false,
            'attr' => array(
                'maxlength' => 30
            )
        ));
        $builder->add('roles', EntityType::class, array(
            'label' => 'Role użytkownika: ',
            'class' => Role::class,
            'choice_label' => 'desc',
            'multiple' => true,
            'expanded' => true,
        ));
        $builder->add('submit', SubmitType::class, array(
            'label' => $options['submit_text']
        ));
        if($options['remove_button']===true) $builder->add('remove', SubmitType::class, array('label' => 'Usuń'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'validation_groups' => array('Default'),
                'submit_text' => 'Dodaj',
                'remove_button' => false,
                'login_disabled' => false
            )
        );
        $resolver->setAllowedTypes('validation_groups', 'array');
        $resolver->setAllowedTypes('submit_text', 'string');
        $resolver->setAllowedTypes('remove_button', 'bool');
        $resolver->setAllowedTypes('login_disabled', 'bool');
        $resolver->setAllowedValues('validation_groups', array(['Default', 'edit_user'], ['Default'], ['edit_user']));
        $resolver->setAllowedValues('submit_text', array('Dodaj', 'Modyfikuj'));

    }
}