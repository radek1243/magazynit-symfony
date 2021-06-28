<?php
namespace App\Controller;

use App\DataTransformer\EntityDataTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Device;
use App\Entity\Type;
use App\Form\LoginForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use App\Entity\Location;
use App\Form\MainOperationForm;
use App\Html\ArrayCell;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Html\HtmlBuilder;
use App\Html\InputSpec;
use phpDocumentor\Reflection\Types\Callable_;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MainController extends AbstractController
{
    public function index(Request $request, Security $security){
        if($request->getSession()->has(Security::LAST_USERNAME)) {
            return new RedirectResponse($this->generateUrl('homepage'));
        }        
        $loginForm = new LoginForm();          
        $form = $this->createFormBuilder($loginForm)
            ->add('login', TextType::class, array('label' => false, 'attr' => array('placeholder' => "Login")))
            ->add('pass', PasswordType::class, array('label' => false, 'attr' => array('placeholder' => "Hasło")))
            ->add('submit', SubmitType::class, array('label' => 'Zaloguj'))->getForm();           
        $formBuilder = $this->createFormBuilder(null);                  
        $formBuilder->add('typ', EntityType::class, array(
            'label' => false,
            'class' => Type::class,
            'choice_label' => 'name'));   
        //
        $form2 = $formBuilder->getForm();    
        if($request->getSession()->has(SECURITY::AUTHENTICATION_ERROR)){
            $error = $request->getSession()->get(SECURITY::AUTHENTICATION_ERROR);
            $request->getSession()->remove(SECURITY::AUTHENTICATION_ERROR);
            return $this->render('index.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView(), 'error_text' => $error));
        }
        else return $this->render('index.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView())); 
    }
    
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepage(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $devForm = new MainOperationForm();
        $formDevices = $this->createFormBuilder($devForm, array('allow_extra_fields' => true))
                        ->add('current_loc', EntityType::class, array(
                            'label' => false,
                            'class' => Location::class,
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('l')->orderBy('l.name','ASC');    
                            },
                            'choice_label' => function($location){
                                return $location->getName().' '.$location->getShortName();
                            }                           
                        ))
                        ->add('current_type', EntityType::class, array(
                            'label' => false,
                            'class' => Type::class,
                            'choice_label' => 'name'
                        ))
                        ->add('dest_loc', EntityType::class, array(
                            'label' => 'Lokalizacja docelowa: ',
                            'class' => Location::class,
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('l')->orderBy('l.name','ASC');
                            },
                            'choice_label' => function($location){
                                return $location->getName().' '.$location->getShortName();
                            }))
                        ->add('send', SubmitType::class, array('label' => 'Wyślij'))
                        ->add('service', SubmitType::class, array('label' => 'Na serwis'))
                        ->add('change_state', SubmitType::class, array('label' => "Zmiana stanu urządzenia"))
                        ->add('change_desc', SubmitType::class, array('label' => 'Zmień opis urządzenia'))
                        ->add('utilization', SubmitType::class, array('label' => 'Utylizacja'))
                        ->add('newdesc', HiddenType::class)
            ->getForm();
        $formDevices->get('current_loc')->setData($this->getDoctrine()->getManager()->getReference('App\Entity\Location', 1));
        $formDevices->handleRequest($request);            
        if($formDevices->isSubmitted() && $formDevices->isValid()){
            $checkboxes = $request->request->all('checkbox');
            $currentLoc = $formDevices->getData()->getCurrentLoc();
            if(array_key_exists('send', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));                   
                $destLoc = $formDevices->getData()->getDestLoc();
                if($currentLoc->getId()==$destLoc->getId()){
                    return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Lokalizacja źródłowa i docelowa są takie same'));
                }
                else{
                    $em = $this->getDoctrine()->getManager();
                    foreach($devices as $device){
                        $device->setLocation($destLoc);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia'));                        
                }
            }
            else if(array_key_exists('service', $request->request->all('form'))){
                if($currentLoc->getId()!=1) return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenia można wysyłać na serwis tylko z Magazynu IT'));
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($devices as $device){
                    if($device->getState()==='S') {
                        $isBroken = false;
                        break;
                    }
                }
                if(!$isBroken) return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz wysłać też sprawne urządzenia na serwis'));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setService(true);
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia na serwis'));                   
            }
            else if(array_key_exists('utilization', $request->request->all('form'))){
                if($currentLoc->getId()!=1) return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenia można utylizować tylko z Magazynu IT'));
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($devices as $device){
                    if($device->getState()==='S') {
                        $isBroken = false;
                        break;
                    }
                }
                if(!$isBroken) return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz zutylizować sprawne urządzenia'));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setUtilization(true);
                    $device->setPerson(null);
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Zutylizowano urządzenia'));
                
            }
            else if(array_key_exists('change_desc', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setDesc($formDevices->getData()->getNewDesc());
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Opis zmieniony'));
            }
            else if(array_key_exists('change_state', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    if($device->getState()==='N'){
                        $device->setState('S');
                    }
                    else{
                        $device->setState('N');
                    }
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Zmieniono stan urządzeń'));
            }
        }            
        return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView()));  
    }
    
    public function logout(){
        $this->denyAccessUnlessGranted('ROLE_USER');
    }
}