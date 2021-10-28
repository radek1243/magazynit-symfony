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
use App\Entity\Device;
use App\Form\Type\PrincLocEditType;
use App\Form\PrincLocEditVal;
use App\Html\ArrayCell;
use App\Html\HtmlBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PersonController extends AbstractController
{

    /**
     * @Route("/addperson", name="addperson")
     */
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
                return $this->render('addperson.html.twig', array('addperson_form' => $form->createView(), 'error_text' => 'Nie dodano osoby. Podany adres email jest już używany'));
            }
        }
        return $this->render('addperson.html.twig', array('addperson_form' => $form->createView()));
    }
    
    /**
     * @Route("/personhistory", name="personhistory")
     */
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
        return $this->render('personhistory.html.twig',array('person_form' => $formPerson->createView()));
    }

    /**
     * @Route("/edit_princ_locations/{id}", name="edit_princ_locations", defaults={"id": null})
     * @ParamConverter("person", class="App\Entity\Person")
     */
    public function editPrincLocations(Request $request, ?Person $person){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $principal = new PrincLocEditVal();
        //dd($person);
        if($person!=null){
            $principal->setPrincipal($person);
            $principal->setLocations($person->getPrincLocations());
        }
        $form = $this->createForm(PrincLocEditType::class, $principal);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->getClickedButton()===$form->get('submit_save')){
                $em = $this->getDoctrine()->getManager();
                $new_locations = new ArrayCollection($form->getData()->getLocations()->toArray());
                foreach($form->getData()->getMislocations() as $loc){
                    $new_locations->add($loc);
                }
                $person->setPrincLocations($new_locations);
                $em->persist($person);
                $em->flush();
            }
            return $this->redirectToRoute('edit_princ_locations', array('id' => $form->getData()->getPrincipal()->getId()));
        }
        return $this->render('editprincloc.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/get_person_history", name="get_person_history")
     */
    public function getPersonHistory(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $person = $request->request->get('person');
                $currentDevices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('person' => $person));
                //$protocols = $this->getDoctrine()->getRepository(Protocol::class)->findBy(array('receiver' => $person), array('date' => 'desc')); 
                $protocols = $this->getDoctrine()->getRepository(Protocol::class)->getPersonProtocols($person);
                $builder = new HtmlBuilder();
                $html = $builder->createTable(array('Typ','Model','Numer seryjny','Numer seryjny 2','Lokalizacja'),
                    array(
                        new ArrayCell(array('typeName')),
                        new ArrayCell(array('modelName')),
                        new ArrayCell(array('SN')),
                        new ArrayCell(array('SN2')),
                        new ArrayCell(array('locationName','locationShortName'))),
                        $currentDevices,
                        true
                    );
                $html .= "<br><h3>Historia protokołów przekazania</h3><br>";
                foreach($protocols as $protocol){
                    if($protocol->getType()==='P') $html .= "<p>Sprzęt przekazany dnia ";
                    else {
                        if($protocol->getSender()->getId()==$person){
                            $html .= "<p>Sprzęt zdany dnia ";
                        }
                        else if($protocol->getReceiver()->getId()==$person){
                            $html .= "<p>Sprzęt przekazany dnia ";
                        }                   
                    }
                    $html .= $protocol->getDate()->format('Y-m-d')."</p>";
                    $html .= $builder->createTable(array('Typ','Model','Numer seryjny','Numer seryjny 2','Lokalizacja'),
                        array(
                            new ArrayCell(array('typeName')),
                            new ArrayCell(array('modelName')),
                            new ArrayCell(array('SN')),
                            new ArrayCell(array('SN2')),
                            new ArrayCell(array('locationName','locationShortName'))),
                            $protocol->getDevices()->toArray(),
                            true
                    );
                    $html .= "<br>";
                }
                return new Response($html);
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }
}

