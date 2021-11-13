<?php

namespace App\Form\Type;

use App\Entity\Device;
use App\Entity\Type;
use DateTime;
use Doctrine\ORM\EntityRepository;
use ReflectionObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceByTypeType extends BaseDeviceType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();
            $placeholder = 'Wybierz rodzaj urządzenia...';
            if($data->getType()!==null){
                $placeholder = false;
            }
            $form->remove('type');
                $form->add('type', EntityType::class, array(
                    'class' => Type::class,
                    'choice_label' => 'name',
                    'label' => false,
                    'placeholder' => $placeholder,
                    'attr' => array(
                        'onchange' => 'submit();'
                    )
                ));
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
    }
}