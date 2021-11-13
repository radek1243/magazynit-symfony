<?php

namespace App\Form\Type;

use App\Entity\Location;
use App\Entity\Person;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class PrincLocEditType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('principal', EntityType::class, array(
            'class' => Person::class,
            'choice_label' => function($principal){
                return $principal->getName()." ".$principal->getSurname();
            },
            'placeholder' => "Wybierz osobę...",
            'label' => "Wybierz osobę: ",
            'query_builder' => function($em){
                return $em->createQueryBuilder('p')
                        ->join('p.position', 's')
                        ->where('s.isSetLoc = true and p.isWorking = true')
                        ->orderBy('p.name, p.surname', 'asc');
            },
            'attr' => ['onchange' => 'submit();']
        ));
        $builder->add('submit_save', SubmitType::class, array('label' => 'Zapisz'));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function($event){
            $data = $event->getData();
            $principal = $data->getPrincipal();
            if($data->getLocations()!==null && !$data->getLocations()->isEmpty()){
                $event->getForm()->add(
                    'locations',
                    EntityType::class,
                    array(
                        'class' => Location::class,
                        'label' => null,
                        'choices' => $data->getLocations()->toArray(),
                        'choice_label' => function(?Location $location){
                            return $location->getName();
                        },
                        'multiple' => true,
                        'expanded' => true
                    )
                );
            }
            if($principal!=null){
                $event->getForm()->add('mislocations', EntityType::class,
                    array(
                        'label' => "Nieprzypisane lokalizacje",
                        'class' => Location::class,
                        'choice_label' => function(?Location $location){
                            return $location->getName()." ".$location->getShortName();
                        },
                        'multiple' => true,
                        'expanded' => true,
                        'query_builder' => function($em) use ($principal){
                            return $em->createQueryBuilder('l')
                                    ->leftJoin('l.persons', 'p')
                                    ->where('p is null or l.id not in (select lo.id from App\Entity\Location lo join lo.persons pe where pe.position = :position)')   //nie jest przypisana wgl lub nie jest przypisana do osoby o takim stanowisku
                                    ->orderBy('l.name', 'asc')
                                    ->setParameter('position', $principal->getPosition());
                        }
                    )
                );
            }
        });
    }
}