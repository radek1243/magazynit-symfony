<?php

namespace App\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DeviceType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('devices', EntityType::class, array(
            'class' => Device::class,
            'extended' => true,
            'multiple' => true,
            'choice_label' => false,
            'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder(d)
                        ->select(array('m.name', 'd.sn', 'd.sn2', 'd.desc'))
                        ->join('d.model', 'm')
                        ->orderBy('m.name', 'asc');
            }
        ));
    }
}