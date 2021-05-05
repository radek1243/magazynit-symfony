<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PersonForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Position;
use App\Entity\Location;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Person;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use App\Entity\Protocol;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends AbstractController
{
    public function addPerson(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $personForm = new PersonForm();
        $form = $this->createFormBuilder($personForm)
                ->add('name', TextType::class, array('label' => 'Imię: ', 'attr' => array('maxlength' => 20)))
                ->add('surname', TextType::class, array('label' => 'Nazwisko: ', 'attr' => array('maxlength' => 40)))
                ->add('email', EmailType::class, array('label' => 'Email: ', 'attr' => array('maxlength' => 50)))
                ->add('position', EntityType::class, array(
                    'label' => 'Stanowisko: ', 
                    'class' => Position::class,
                    'choice_label' => 'name'
                ))
                ->add('location', EntityType::class, array(
                    'label' => 'Lokalizacja: ',
                    'class' => Location::class,
                    'choice_label' => function($location){
                        return $location->getName()." - ".$location->getShortName();
                    },
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('l')->orderBy('l.name','asc');
                    }
                ))
                ->add('submit', SubmitType::class, array('label' => 'Dodaj'))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $person = new Person();
                $person->setName($form->getData()->getName());
                $person->setSurname($form->getData()->getSurname());
                $person->setEmail($form->getData()->getEmail());
                $person->setIsWorking(true);
                $person->setPosition($form->getData()->getPosition());
                $person->setLocation($form->getData()->getLocation());
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();
                return $this->render('addperson.html.twig', array('addperson_form' => $form->createView(), 'communicate_text' => 'Dodano osobę'));
            }
            catch(UniqueConstraintViolationException $ex){
                return $this->render('addperson.html.twig', array('addperson_form' => $form->createView(), 'error_text' => 'Nie dodano osoby. Podany adres email jest już uzywany'));
            }
        }
        return $this->render('addperson.html.twig', array('addperson_form' => $form->createView()));
    }
    
    public function personHistory(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $formPerson = $this->createFormBuilder()
                    ->add('person', EntityType::class, array(
                        'label' => false,
                        'class' => Person::class,
                        'choice_label' => function($person){
                            return $person->getName()." ".$person->getSurname();
                        },
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                        }
                    ))->getForm();        
        if($request->isXmlHttpRequest()){
            $person = $request->request->get('person');
            $protocols = $this->getDoctrine()->getRepository(Protocol::class)->findBy(array('receiver' => $person), array('date' => 'desc'));
            $html = "<p>";
            foreach($protocols as $protocol){
                if($protocol->getType()==='P') $html .= "Sprzęt przekazany dnia ";
                else $html .= "Sprzęt zdany dnia ";
                $html .= $protocol->getDate()->format('Y-m-d')."</p>";
                $html .= "<table><tr class='tr-back'><td>Typ</td><td>Model</td><td>Numer seryjny</td><td>Numer seryjny 2</td><td>Lokalizacja</td></tr>";             
                $devices = $protocol->getDevices();
                $counter=0;
                foreach($devices as $device){
                    if($counter%2==0) $html .= "<tr class='tr-back'>";
                    else $html .= "<tr>";
                    $html .= "<td>".$device->getType()->getName()."</td>";
                    $html .= "<td>".$device->getModel()->getName()."</td>";
                    $html .= "<td>".$device->getSN()."</td>";
                    $html .= "<td>".$device->getSN2()."</td>";
                    $html .= "<td>".$device->getLocation()->getName()." ".$device->getLocation()->getShortName()."</td></tr>";
                    $counter++;
                }
                $html .= "</table><br>";
            }
            return new Response($html);
        }
        else{
            return $this->render('personhistory.html.twig',array('person_form' => $formPerson->createView()));
        }
    }
}

