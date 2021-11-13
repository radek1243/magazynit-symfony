<?php

namespace App\Form\Type;

use App\Entity\Device;
use App\Html\DoctrineCell;
use App\Html\HtmlBuilder;
use ReflectionObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseDeviceType extends SubmitsType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event){
            $form = $event->getForm();
            $data = $event->getData();
            $form->add('devices', EntityType::class, array(
                'class' => Device::class,
                'choice_label' => function($device) use ($form){
                    $htmlBuilder = new HtmlBuilder();
                    return $htmlBuilder->createCellsFromDoctrine($form->getConfig()->getOptions()['choice_label_cells'], $device);
                },
                'label_html' => true,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function($er) use ($data, $form){
                    $reflection = new ReflectionObject($data);
                    $qb = $er->createQueryBuilder('d');
                    $qb->join('d.model','m')
                        ->where($form->getConfig()->getOptions()['query_builder_where']);
                    foreach($form->getConfig()->getOptions()['query_builder_parameters'] as $key => $value){
                        $qb->setParameter($key, $reflection->getMethod($value)->invoke($data));
                    }                                              
                    $qb->orderBy($form->getConfig()->getOptions()['order_by_columns'], $form->getConfig()->getOptions()['order_by_direction']); 
                    return $qb;
                }
            ));            
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(
            array(
                'query_builder_where' => 'd.type = :type and d.location=1 and d.service=1 and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) and d.utilization=0',
                'choice_label_cells' => [
                    new DoctrineCell('ModelName', 'string', null), 
                    new DoctrineCell('SN', 'string' , null), 
                    new DoctrineCell('SN2', 'string' , null), 
                    new DoctrineCell('Desc', 'string', null)
                ],
                'query_builder_parameters' => ['type' => 'getType'],
                'order_by_columns' => 'm.name, d.sn',
                'order_by_direction' => 'asc'
            )
        );
        $resolver->setAllowedTypes('query_builder_where', 'string');
        $resolver->setAllowedTypes('choice_label_cells', ['array', 'App\Html\DoctrineCell']);
        $resolver->setAllowedTypes('query_builder_parameters', ['array', 'string']);
        $resolver->setAllowedTypes('order_by_columns', 'string');
        $resolver->setAllowedTypes('order_by_direction', 'string');
    }
}