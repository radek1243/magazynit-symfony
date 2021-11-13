<?php

namespace App\Form\Type;

use App\Entity\Location;
use App\Entity\Type;
use DateTime;
use Doctrine\ORM\EntityRepository;
use ReflectionObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceOperationType extends DeviceByTypeType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('currentloc', EntityType::class, array(
            'label' => false,
            'class' => Location::class,
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('l')->orderBy('l.name','ASC');    
            },
            'choice_label' => function($location){
                return $location->getName().' '.$location->getShortName();
            },
            'attr' => array(
                'onchange' => 'submit();'
            )                           
        ));
        $builder->add('destloc', EntityType::class, array(
            'label' => 'Lokalizacja docelowa: ',
            'class' => Location::class,
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('l')->orderBy('l.name','ASC');
            },
            'choice_label' => function($location){
                return $location->getName().' '.$location->getShortName();
            }));
        $builder->add('newdesc', HiddenType::class);
        $builder->add('order', ChoiceType::class, array(
            'label' => "Sortowanie: ",
            'choices' => $options['order_choices'],
            'choice_label' => function($choice, $key, $value){
                return $key;
            },
            'attr' => array(
                'onchange' => 'submit();'
            )  
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'order_choices' => [
                    'Domyślne' => 'default',
                    'Opis rosnąco' => 'description-asc',
                    'Opis malejąco' => 'description-desc'
                ]
            ]
        );
        $resolver->setAllowedTypes('order_choices', ['array', 'string']);
        parent::configureOptions($resolver);
    }
}