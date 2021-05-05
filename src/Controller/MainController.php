<?php
namespace App\Controller;

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
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;


class MainController extends AbstractController
{
    public function index(Request $request, Security $security){
        if($request->getSession()->has(Security::LAST_USERNAME)) {
            //dd($request->getSession());
            return new RedirectResponse($this->generateUrl('homepage'));
        }
        //$this->denyAccessUnlessGranted('ROLE_USER');
        if($request->isXmlHttpRequest()){
            $type = $request->request->get('typ');
            $array = $this->getDoctrine()->getRepository(Device::class)->getDeviceByTypeFromLoc($type, 1);
            $html = "<table class='col-12'><tr class='tr-back'><td>Model</td><td>Stan</td><td>Numer seryjny</td><td>Numer seryjny 2</td><td>Opis</td></tr>";
            foreach ($array as $key => $device){
                if($key%2===0){
                    $html .= "<tr class='tr-back'><td>";
                }
                else{
                    $html .= "<tr><td>";
                }
                $html .= $device['name']."</td>";
                if($device['state']==='N'){
                    $html .= "<td class='td-font-red'>";
                }
                else{
                    $html .= "<td class='td-font-green'>";
                }
                $html .= $device['state']."</td><td>";
                $html .= $device['sn']."</td><td>";
                $html .= $device['sn2']."</td><td>";
                $html .= $device['desc']."</td></tr>";               
            }
            $html .= "</table>";
            return new Response($html);
        }
        else{
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
            $form2 = $formBuilder->getForm();
            if($request->getSession()->has(SECURITY::AUTHENTICATION_ERROR)){
                $error = $request->getSession()->get(SECURITY::AUTHENTICATION_ERROR);
                $request->getSession()->remove(SECURITY::AUTHENTICATION_ERROR);
                return $this->render('index.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView(), 'error_text' => $error));
            }
            else return $this->render('index.html.twig', array('form' => $form->createView(), 'form2' => $form2->createView()));
        }  
    }
    
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepage(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $formDevices = $this->createFormBuilder()
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
                        ->add('current_loc', HiddenType::class)
                        ->add('current_type', HiddenType::class)
                        ->add('newdesc', HiddenType::class)
            ->getForm();
        if($request->isXmlHttpRequest()){
            $type = $request->request->get('typ');
            $location = $request->request->get('loc');
            //dd($request->request);
            $array = $this->getDoctrine()->getRepository(Device::class)->getDeviceByTypeFromLoc($type, $location);
            $html = "<table class='col-12'><tr class='tr-back'><td>Model</td><td>Stan</td><td>Numer seryjny</td><td>Numer seryjny 2</td><td>Opis</td><td>Zaznacz</td></tr>";
            foreach ($array as $key => $device){
                if($key%2===0){
                    $html .= "<tr class='tr-back'><td>";
                }
                else{
                    $html .= "<tr><td>";
                }
                $html .= $device['name']."</td>";
                if($device['state']==='N'){
                    $html .= "<td class='td-font-red'>";
                }
                else{
                    $html .= "<td class='td-font-green'>";
                }
                $html .= $device['state']."</td><td>";
                $html .= $device['sn']."</td><td>";
                $html .= $device['sn2']."</td><td>";
                $html .= $device['desc']."</td>";
                $html .= "<td><input type='checkbox' name='checkbox[".$device['id']."]' value='".$device['id']."'></td></tr>";
            }
            $html .= "</table>";
            return new Response($html);
        }
        else{
            $formBuilder = $this->createFormBuilder(null);
            $formBuilder->add('location', EntityType::class, array(
                'label' => false,
                'class' => Location::class,
                'query_builder' => function(EntityRepository $er){
                      return $er->createQueryBuilder('l')->orderBy('l.name','ASC');    
                },
                'choice_label' => function($location){
                    return $location->getName().' '.$location->getShortName();
                    }
                ));
            $formBuilder->add('typ', EntityType::class, array(
                'label' => false,
                'class' => Type::class,
                'choice_label' => 'name'
            ));
            $form2 = $formBuilder->getForm();
            $form2->get('location')->setData($this->getDoctrine()->getManager()->getReference('App\Entity\Location', 1));
            $formDevices->handleRequest($request);            
            if($formDevices->isSubmitted()){
                $checkboxes = $request->request->all('checkbox');
                $currentLoc = $request->request->all('form')['current_loc'];
                $form2->get('typ')->setData($this->getDoctrine()->getManager()->getReference('App\Entity\Type', $request->request->all('form')['current_type']));
                $form2->get('location')->setData($this->getDoctrine()->getManager()->getReference('App\Entity\Location', $currentLoc));
                //dd($form2->get('typ')->getData());  
                if(array_key_exists('send', $request->request->all('form'))){
                    //dd($request->request->all('form'));
                    $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));                   
                    $destLoc = $request->request->all('form')['dest_loc'];
                    if($currentLoc===$destLoc){
                        return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Lokalizacja źródłowa i docelowa są takie same'));
                    }
                    else{
                        $em = $this->getDoctrine()->getManager();
                        foreach($devices as $device){
                            $device->setLocation($this->getDoctrine()->getRepository(Location::class)->find($destLoc));
                            $em->persist($device);
                        }
                        $em->flush();
                        return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia'));                        
                    }
                    //dd($devices);
                }
                else if(array_key_exists('service', $request->request->all('form'))){
                    if($currentLoc!=="1") return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenia można wysyłać na serwis tylko z Magazynu IT'));
                    $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                    $isBroken = true;
                    foreach($devices as $device){
                        if($device->getState()==='S') {
                            $isBroken = false;
                            break;
                        }
                    }
                    if(!$isBroken) return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz wysłać też sprawne urządzenia na serwis'));
                    $em = $this->getDoctrine()->getManager();
                    foreach($devices as $device){
                        $device->setService(true);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia na serwis'));                   
                }
                else if(array_key_exists('utilization', $request->request->all('form'))){
                    if($currentLoc!=="1") return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenia można utylizować tylko z Magazynu IT'));
                    $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                    $isBroken = true;
                    foreach($devices as $device){
                        if($device->getState()==='S') {
                            $isBroken = false;
                            break;
                        }
                    }
                    if(!$isBroken) return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz zutylizować sprawne urządzenia'));
                    $em = $this->getDoctrine()->getManager();
                    foreach($devices as $device){
                        $device->setUtilization(true);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Zutylizowano urządzenia'));
                    
                }
                else if(array_key_exists('change_desc', $request->request->all('form'))){
                    $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                    $em = $this->getDoctrine()->getManager();
                    foreach($devices as $device){
                        $device->setDesc($request->request->all('form')['newdesc']);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Opis zmieniony'));
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
                    return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Zmieniono stan urządzeń'));
                }
            }            
            return $this->render('homepage.html.twig', array('form2' => $form2->createView(), 'form_devices' => $formDevices->createView()));
        }  
    }
    
    public function logout(){
        $this->denyAccessUnlessGranted('ROLE_USER');
    }
}