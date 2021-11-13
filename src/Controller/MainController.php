<?php
namespace App\Controller;

use App\Communicate\Communicate;
use App\Communicate\CommunicateBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Device;
use App\Entity\Type;
use App\Form\LoginForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Entity\Location;
use App\Form\DeviceOperationTypeValidator;
use App\Form\MainOperationForm;
use App\Form\Type\DeviceOperationType;
use App\Html\DoctrineCell;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class MainController extends AbstractController
{
    /**
    * @Route("/", name="index")
     */
    public function index(Request $request){
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
     *
     * @Route("/homepage/{location}/{type}/{order}/{communicate}", name="homepage", defaults={"location": "MIT", "type": null, "communicate": null, "order": "default"})
     * @ParamConverter("type", converter="type_converter", class="App\Entity\Type", options={ "mapping": { "type": "name"}})
     * @ParamConverter("location", class="App\Entity\Location", options={"mapping": { "location": "shortName"}})
     * @ParamConverter("communicate", converter="communicate_converter", class="string")
     */
    public function homepage(Request $request, Location $location, ?Type $type, ?Communicate $communicate, string $order){
        $this->denyAccessUnlessGranted('ROLE_USER');    //w trakcie (sortowanie w typie też jako parameter) - usunąć javascript po przeróbce, metode ajax oraz z repository
        $coummincate_text = null;
        $error_text = null;
        $devOpVal = new DeviceOperationTypeValidator();
        $devOpVal->setCurrentloc($location);
        $devOpVal->setOrder($order);
        if($type!==null){
            $devOpVal->setType($type);
        }
        if($communicate!==null){
            $error_text = $communicate->getErrorText();
            $coummincate_text = $communicate->getCommunicateText();
        }
        $options = array(
            'submits' => [
                ['name' => 'send', 'label' => 'Wyślij'],
                ['name' => 'service', 'label' => 'Na serwis'],
                ['name' => 'change_state', 'label' => 'Zmiana stanu urządzenia'],
                ['name' => 'change_desc', 'label' => 'Zmień opis urządzenia'],
                ['name' => 'utilization', 'label' => 'Utylizacja']
            ],
            'query_builder_where' => 'd.location = :location and d.type = :type and d.service = false and d.utilization = false and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) ',
            'choice_label_cells' => [
                new DoctrineCell('ModelName', 'string', null),
                new DoctrineCell('State', 'string', array('N' => 'td-font-red', 'S' => 'td-font-green')),
                new DoctrineCell('SN', 'string', null),
                new DoctrineCell('SN2', 'string', null),
                new DoctrineCell('Desc', 'string', null)
            ],
            'query_builder_parameters' => ['type' => 'getType', 'location' => 'getCurrentloc']
        );
        switch($order){
            case "default": 
                $options['order_by_columns'] = 'd.state, m.name';
                $options['order_by_direction'] = 'desc';
                break;
            case 'description-asc':
                $options['order_by_columns'] = 'd.desc';
                $options['order_by_direction'] = 'asc';
                break;
            case 'description-desc':
                $options['order_by_columns'] = 'd.desc';
                $options['order_by_direction'] = 'desc';
                break;
        }
        $formDevices = $this->createForm(DeviceOperationType::class, $devOpVal, $options);
        if($request->isMethod('POST')) $formDevices->handleRequest($request);            
        if($formDevices->isSubmitted() && $formDevices->isValid()){
            //$checkboxes = $request->request->all('checkbox');
            $currentLoc = $formDevices->getData()->getCurrentloc();
            if($formDevices->getClickedButton()===$formDevices->get('send')/*array_key_exists('send', $request->request->all('form'))*/){
                //$devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));                   
                $destLoc = $formDevices->getData()->getDestloc();
                if($currentLoc->getId()==$destLoc->getId()){
                    return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$SEND_LOC_ERROR));
                    //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Lokalizacja źródłowa i docelowa są takie same'));
                }
                else{
                    $em = $this->getDoctrine()->getManager();
                    foreach($formDevices->getData()->getDevices() as $device){
                        $device->setLocation($destLoc);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$SEND_SUCCESS));
                    //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia'));                        
                }
            }
            else if($formDevices->getClickedButton()===$formDevices->get('service')/*array_key_exists('service', $request->request->all('form'))*/){
                if($currentLoc->getId()!=1) {
                    return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$SERVICE_LOC_ERROR));
                    //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenia można wysyłać na serwis tylko z Magazynu IT'));
                }
                //$devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($formDevices->getData()->getDevices() as $device){
                    if($device->getState()==='S') {
                        $isBroken = false;
                        break;
                    }
                }
                if(!$isBroken) {
                    return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$SERVICE_STATE_ERROR));
                    //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz wysłać też sprawne urządzenia na serwis'));
                }
                $em = $this->getDoctrine()->getManager();
                foreach($formDevices->getData()->getDevices() as $device){
                    $device->setService(true);
                    $em->persist($device);
                }
                $em->flush();
                return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$SERVICE_SUCCESS));
                //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia na serwis'));                   
            }
            else if($formDevices->getClickedButton()===$formDevices->get('utilization')/*array_key_exists('utilization', $request->request->all('form'))*/){   
                if($currentLoc->getId()!=1) {
                    return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$UTILIZATION_LOC_ERROR));
                    //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenia można utylizować tylko z Magazynu IT'));
                }
                //$devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($formDevices->getData()->getDevices() as $device){
                    if($device->getState()==='S') {
                        $isBroken = false;
                        break;
                    }
                }
                if(!$isBroken) {
                    return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$UTILIZATION_STATE_ERROR));
                    //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz zutylizować sprawne urządzenia'));
                }
                $em = $this->getDoctrine()->getManager();
                foreach($formDevices->getData()->getDevices() as $device){
                    $device->setUtilization(true);
                    $device->setPerson(null);
                    $em->persist($device);
                }
                $em->flush();
                return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$UTILIZATION_SUCCESS));
                //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Zutylizowano urządzenia'));
                
            }
            else if($formDevices->getClickedButton()===$formDevices->get('change_desc')/*array_key_exists('change_desc', $request->request->all('form'))*/){
                $em = $this->getDoctrine()->getManager();
                foreach($formDevices->getData()->getDevices() as $device){
                    $device->setDesc($formDevices->getData()->getNewDesc());
                    $em->persist($device);
                }
                $em->flush();
                return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$CHANGE_DESC_SUCCESS));
                //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Opis zmieniony'));
            }
            else if($formDevices->getClickedButton()===$formDevices->get('change_state')/*array_key_exists('change_state', $request->request->all('form'))*/){
                $em = $this->getDoctrine()->getManager();
                foreach($formDevices->getData()->getDevices() as $device){
                    if($device->getState()==='N'){
                        $device->setState('S');
                    }
                    else{
                        $device->setState('N');
                    }
                    $em->persist($device);
                }
                $em->flush();
                return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder(), 'communicate' => CommunicateBuilder::$CHANGE_STATE_SUCCESS));
                //return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => 'Zmieniono stan urządzeń'));
            }
            return $this->redirectToRoute('homepage', array('location' => $formDevices->getData()->getCurrentloc()->getShortName(), 'type' => urlencode($formDevices->getData()->getType()->getName()), 'order' => $formDevices->getData()->getOrder()));
        }            
        return $this->render('homepage.html.twig', array('form_devices' => $formDevices->createView(), 'communicate_text' => $coummincate_text, 'error_text' => $error_text));  
    }
    
    /**
    * @Route("/logout", name="logout")
     */
    public function logout(){
        $this->denyAccessUnlessGranted('ROLE_USER');
    }
}