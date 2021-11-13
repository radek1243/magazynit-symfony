<?php

namespace App\Form\Type;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use App\Form\GetUserForm;
use App\Form\UserForm;
use App\Form\UserListSubformValidator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserRowFormType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login', TextType::class, array(
            'label' => false,
            'disabled' => true
        ));
        $builder->add('id', HiddenType::class);
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();
            $form->add('roles', ChoiceType::class, array(
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $data->getRoles(),
                'choice_label' => function($choice, $key, $value){
                    return $value;
                },
                'disabled' => true
            ));
            $form->add('submit', SubmitType::class, array(
                'label' => 'Edytuj'
            ));
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => UserListSubformValidator::class)
        );
    }
}