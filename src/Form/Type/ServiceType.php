<?php

namespace App\Form\Type;

use App\Entity\Device;
use App\Entity\Type;
use DateTime;
use Doctrine\ORM\EntityRepository;
use ReflectionObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['submits'] as $submit){
            $builder->add($submit['name'], SubmitType::class, ['label' => $submit['label']]);
        }
        $builder->add('type', EntityType::class, array(
            'class' => Type::class,
            'choice_label' => 'name',
            'label' => false,
            'placeholder' => 'Wybierz typ urządzenia...',
            'attr' => array(
                'onchange' => 'submit();'
            )
        ));
        $builder->get('type')->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event){
            $form = $event->getForm()->getParent();
            $type = $event->getData();
            $form->add('devices', EntityType::class, array(
                'class' => Device::class,
                'choice_label' => function($device) use ($form){
                    $reflector = new ReflectionObject($device);
                    $html = null;
                    foreach($form->getConfig()->getOptions()['choice_label_methods'] as $method){
                        $data = $reflector->getMethod('get'.$method)->invoke($device);
                        if($data instanceof DateTime){
                            $data = $data->format('d-m-Y H:i:s');
                        }
                        $html .= "<td>".$data."</td>";
                    }
                    return $html;
                },
                'label_html' => true,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) use ($type, $form){
                    return $er->createQueryBuilder('d')
                            ->join('d.model','m')
                            ->where($form->getConfig()->getOptions()['query_builder_where'])
                            ->setParameter('type', $type)
                            ->orderBy('m.name, d.sn', 'asc');                        
                }
            ));            
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'submits' => [
                    ['name' => 'submit_return', 'label' => 'Powrót'],
                    ['name' => 'submit_utilization', 'label' => 'Utylizacja']
                ],
                'query_builder_where' => 'd.type = :type and d.location=1 and d.service=1 and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) and d.utilization=0',
                'choice_label_methods' => [
                    'ModelName', 'SN', 'SN2', 'Desc'
                ]
            )
        );
        $resolver->setAllowedTypes('submits',['array', 'array', 'string']);
        $resolver->setAllowedTypes('query_builder_where', 'string');
        $resolver->setAllowedTypes('choice_label_methods', ['array', 'string']);
    }
}