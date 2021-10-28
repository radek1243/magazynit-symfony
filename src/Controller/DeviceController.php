<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Type;
use App\Entity\Model;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Device;
use App\Entity\Location;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use App\Form\ModelForm;
use App\Form\TypeForm;
use App\Form\LocationForm;
use Symfony\Component\HttpFoundation\Response;
use App\Form\DeviceHistoryForm;
use App\Entity\History;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\TypeModSN;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\ChangeSnForm;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use App\Entity\Invoicing;
use App\Form\DeviceForm;
use App\Form\FindOperationForm;
use App\Form\HistoryByDateForm;
use App\Form\OnlyTypeForm;
use App\Form\ServiceTypeValidator;
use App\Form\Type\ServiceType;
use App\Html\ArrayCell;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Html\HtmlBuilder;
use App\Html\InputSpec;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DeviceController extends AbstractController
{

    /**
    * @Route("/adddevice", name="adddevice")
     */
    public function adddevice(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $deviceForm = new DeviceForm();
        $formBuilder = $this->createFormBuilder($deviceForm, array('allow_extra_fields' => true))                   
                    ->add('type', EntityType::class, array(
                        'class' => Type::class,
                        'choice_label' => 'name',
                        'label' => 'Typ urządzenia: '
                    ))
                    ->add('sn', TextType::class, array(
                        'label' => 'Numer seryjny: ',
                        'required' => true,
                        'attr' => array('maxlength' => 30)
                    ))
                    ->add('sn2', TextType::class, array(
                        'label' => 'Numer seryjny 2 (opcjonalnie): ',
                        'required' => false,
                        'attr' => array('maxlength' => 30)
                    ))
                    ->add('state', ChoiceType::class, array(
                        'label' => 'Stan: ',
                        'choices' => array(
                            'Sprawny' => 'S',
                            'Niesprawny' => 'N'
                        )
                    ))      
                    ->add('invoicing', CheckboxType::class, array(
                        'label' => 'Podlega fakturowaniu? ',
                        'required' => false
                    ))
                    ->add('desc', TextareaType::class, array(
                        'label' => 'Opis: ',
                        'required' => false
                    ))
                    ->add('submit', SubmitType::class, array(
                        'label' => 'Dodaj urządzenie'
                    ));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $device = new Device();
                $device->setType($form->getData()->getType());
                $device->setModel($this->getDoctrine()->getRepository(Model::class)->find($request->request->get('form')['model']));
                $device->setLocation($this->getDoctrine()->getRepository(Location::class)->find(1));
                $device->setSN(strtoupper($form->getData()->getSn()));
                $device->setSN2(strtoupper($form->getData()->getSn2()));
                $device->setState($form->getData()->getState());
                $device->setDesc($form->getData()->getDesc());
                $device->setInvoicing($form->getData()->getInvoicing());
                $em = $this->getDoctrine()->getManager();
                $em->persist($device);
                $em->flush();
                $request->getSession()->set('rem_model', $request->request->get('form')['model']);
                return $this->render('adddevice.html.twig', array('addform' => $form->createView(), 'communicate_text' => 'Dodano urządzenie'));
            }
            catch(UniqueConstraintViolationException $ex){
                $error_text = "Urządzenie o podanym numerze seryjnym jest już dodane.";
                return $this->render('adddevice.html.twig', array('addform' => $form->createView(), 'error_text' => $error_text));
            }
        }
        else return $this->render('adddevice.html.twig', array('addform' => $form->createView()));
    }
    
    /**
    * @Route("/model", name="model")
     */
    public function model(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $modelForm = new ModelForm();
        $form = $this->createFormBuilder($modelForm)
            ->add('types', EntityType::class, array('label' => "Typy: ", 'class' => Type::class, 'choice_label' => 'name', 'multiple' => true))
            ->add('name', TextType::class, array('label' => 'Nazwa modelu: ', 'attr' => array('maxlength' => 30)))
            ->add('submit', SubmitType::class, array('label' => 'Dodaj model'))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $model = new Model();
                $model->setName($form->getData()->getName());
                $model->setTypes($form->getData()->getTypes());
                $em = $this->getDoctrine()->getManager();
                $em->persist($model);
                $em->flush();
                $modelList = $this->getDoctrine()->getRepository(Model::class)->findAll();
                return $this->render('model.html.twig', array('model_form' => $form->createView(), 'communicate_text' => 'Dodano model', 'model_list' => $modelList));
            }
            catch(UniqueConstraintViolationException $ex){
                $modelList = $this->getDoctrine()->getRepository(Model::class)->findAll();
                return $this->render('model.html.twig', array('model_form' => $form->createView(), 'error_text' => 'Model o podanej nazwie już istnieje', 'model_list' => $modelList));
            }                
        }
        else{
            $modelList = $this->getDoctrine()->getRepository(Model::class)->findAll();
            return $this->render('model.html.twig', array('model_form' => $form->createView(), 'model_list' => $modelList));
        }      
    }
    
    /**
    * @Route("/type", name="type")
     */
    public function type(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $typeForm = new TypeForm();
        $form = $this->createFormBuilder($typeForm)
            ->add('name', TextType::class, array('label' => 'Typ urządzenia: ', 'attr' => array('maxlength' => 30)))
            ->add('submit', SubmitType::class, array('label' => 'Dodaj typ'))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $type = new Type();
                $type->setName($form->getData()->getName());
                $em = $this->getDoctrine()->getManager();
                $em->persist($type);
                $em->flush();
                $typeList = $this->getDoctrine()->getRepository(Type::class)->findAll();
                return $this->render('type.html.twig', array('type_form' => $form->createView(), 'communicate_text' => 'Dodano typ', 'type_list' => $typeList));
            }
            catch(UniqueConstraintViolationException $ex){
                $typeList = $this->getDoctrine()->getRepository(Type::class)->findAll();
                return $this->render('type.html.twig', array('type_form' => $form->createView(), 'error_text' => 'Podany typ już istnieje', 'type_list' => $typeList));
            }
        }
        else{
            $typeList = $this->getDoctrine()->getRepository(Type::class)->findAll();
            return $this->render('type.html.twig', array('type_form' => $form->createView(), 'type_list' => $typeList));
        }      
    }
    
    /**
    * @Route("/location", name="location")
     */
    public function location(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $locForm = new LocationForm();
        $form = $this->createFormBuilder($locForm)
                ->add('name', TextType::class, array('label' => 'Nazwa lokalizacji: ', 'attr' => array('maxlength' => 40)))
                ->add('shortName', TextType::class, array('label' => 'Skrót lokalizacji: ', 'attr' => array('maxlength' => 10)))
                ->add('submit', SubmitType::class, array('label' => "Dodaj lokalizację"))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $location = new Location();
                $location->setName($form->getData()->getName());
                $location->setShortName(strtoupper($form->getData()->getShortName()));
                $location->setVisible(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($location);
                $em->flush();
                $locList = $this->getDoctrine()->getRepository(Location::class)->findAll();
                return $this->render('location.html.twig', array('location_form' => $form->createView(), 'communicate_text' => 'Dodano lokalizację', 'loc_list' => $locList));
            }
            catch(UniqueConstraintViolationException $ex){
                $locList = $this->getDoctrine()->getRepository(Location::class)->findAll();
                return $this->render('location.html.twig', array('location_form' => $form->createView(), 'error_text' => 'Lokalizacja o podanym skrócie jest już dodana.', 'loc_list' => $locList));
            }
        }
        else{
            $locList = $this->getDoctrine()->getRepository(Location::class)->findAll();
            return $this->render('location.html.twig', array('location_form' => $form->createView(), 'loc_list' => $locList));
        }
    }
    
    /**
    * 
    * @Route("/invoicing/{type}/{success}", name="invoicing", defaults={"type": null, "success": null})
    * @ParamConverter("type", converter="type_converter", class="App\Entity\Type", options={ "mapping": { "type": "name"}})
     */
    public function invoicing(Request $request, ?Type $type, ?string $success){
        $this->denyAccessUnlessGranted('ROLE_USER');    
        $validator = new ServiceTypeValidator();
        $communicate_text = null;
        if($type!==null){           
            $validator->setType($type);
        }
        if($success!==null && $success==='success'){
            $communicate_text = 'Zafakturowano urządzenia.';
        } 
        $options = array();
        $options['submits'] = [
            ['name' => 'submit_invoice', 'label' => 'Zafakturuj']
        ];
        $options['query_builder_where'] = 'd.location!=1 and d.type= :type and d.service=0 and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) and d.fv=0 and d.utilization=0 and '
        . 'd.invoicing=1';
        $options['choice_label_methods'] = [
            'ModelName', 'State', 'SN', 'SN2', 'LocationName', 'LocationShortName', 'Desc', 'OperationTime'
        ];
        $form = $this->createForm(ServiceType::class, $validator,$options);
        if($request->isMethod('POST')) $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $updated = null;
            $em = $this->getDoctrine()->getManager();
            if($form->getClickedButton()===$form->get('submit_invoice')){
                foreach($form->getData()->getDevices() as $device){
                    $device->setFV(true);
                    $em->persist($device);
                }
                $em->flush();
                $updated = 'success';
            }
            return $this->redirectToRoute('invoicing', ['type' => urlencode( $form->getData()->getType()->getName()), 'success' => $updated]);
        }
        $params = array('invoicing_form' => $form->createView());
        if($communicate_text!==null && is_string($communicate_text)) {
            $params['communicate_text'] = $communicate_text;
        }
        return $this->render('invoicing.html.twig', $params);
    }
    
    /**
    * @Route("/onservice/{type}/{success}", name="onservice", defaults={"type": null, "success": null})
    * @ParamConverter("type", converter="type_converter", class="App\Entity\Type", options={ "mapping": { "type": "name"}})
     */
    public function onservice(Request $request, ?Type $type, ?string $success){
        $this->denyAccessUnlessGranted('ROLE_USER');    
        $validator = new ServiceTypeValidator();
        $communicate_text = null;
        if($type!==null){           
            $validator->setType($type);
        }
        if($success!==null){
             if($success==='ret_success'){
                $communicate_text = 'Przywrócono urzadzenia z serwisu.';
             }
             else if($success==='util_success'){
                $communicate_text = 'Zutylizowano urządzenia.';
             }
        } 
        $form = $this->createForm(ServiceType::class, $validator);
        if($request->isMethod('POST')) $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $updated = null;
            $em = $this->getDoctrine()->getManager();
            if($form->getClickedButton()===$form->get('submit_return')){
                foreach($form->getData()->getDevices() as $device){
                    $device->setService(false);
                    $device->setDesc(null);
                    $device->setState('S');
                    $em->persist($device);
                }
                $em->flush();
                $updated = 'ret_success';
            }
            else if($form->getClickedButton()===$form->get('submit_utilization')){
                foreach($form->getData()->getDevices() as $device){
                    $device->setUtilization(true);
                    $em->persist($device);
                }
                $em->flush();
                $updated = 'util_success';
            }
            return $this->redirectToRoute('onservice', ['type' => urlencode( $form->getData()->getType()->getName()), 'success' => $updated]);
        }
        $params = array('onservice_form' => $form->createView());
        if($communicate_text!==null && is_string($communicate_text)) {
            $params['communicate_text'] = $communicate_text;
        }
        return $this->render('onservice.html.twig', $params);
    }
    
    /**
    * @Route("/devicehistory", name="devicehistory")
     */
    public function devicehistory(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $devHistForm = new DeviceHistoryForm();
        $form = $this->createFormBuilder($devHistForm)
                ->add('sn', TextType::class, array('label' => 'Numer seryjny urządzenia: ', 'attr' => array('maxlength' => 30)))
                ->add('submit', SubmitType::class, array('label' => 'Pokaż historię'))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $sn = strtoupper($request->request->all('form')['sn']);
            $currentState = $this->getDoctrine()->getRepository(Device::class)->findBy(array('sn' => $sn));
            $history = $this->getDoctrine()->getRepository(History::class)->findBy(array('serialNumber' => $sn), array('operation_time' => 'desc'));
            return $this->render('devicehistory.html.twig', array('history_form' => $form->createView(), 'current_state' => $currentState, 'history' => $history));
        }
        else{
            return $this->render('devicehistory.html.twig', array('history_form' => $form->createView()));
        }
    }
    
    /**
    * @Route("/historybydate", name="historybydate")
     */
    public function historybydate(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $maxDate = new \DateTime("now");
        $historyForm = new HistoryByDateForm();
        $form = $this->createFormBuilder($historyForm)
        ->add('type', EntityType::class, array(
            'label' => false,
            'choice_label' => 'name',
            'class' => Type::class
        ))
        ->add('date', DateType::class, array(
            'label' => 'Wybierz datę: ',
            'widget' => 'single_text',
            'input' => 'datetime',
            'attr' => array('max' => $maxDate->format('Y-m-d'), 'value' => $maxDate->sub(new \DateInterval('P30D'))->format('Y-m-d'))
        ))
        ->add('submit', SubmitType::class, array('label' => 'Pokaż historię'))->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $history = $this->getDoctrine()->getRepository(History::class)->getHistoryByDate($form->getData()->getType(), $form->getData()->getDate());
            return $this->render('historybydate.html.twig', array('history_form' => $form->createView(), 'history' => $history));
        }
        else{
            return $this->render('historybydate.html.twig', array('history_form' => $form->createView()));
        }
    }
    
    /**
    * @Route("/changesn", name="changesn")
     */
    public function changesn(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $deviceHistoryForm = new DeviceHistoryForm();
        $form = $this->createFormBuilder($deviceHistoryForm)
                    ->add('sn',TextType::class, array('label' => 'Numer seryjny urządzenia do zmiany: ', 'attr' => array('maxlength' => 30)))
                    ->add('submit', SubmitType::class, array('label' => 'Wyszukaj'))->getForm();
        $changeSnForm = new ChangeSnForm();
        $form2 = $this->createFormBuilder($changeSnForm)
                    ->add('serialnumber', TextType::class, array('label' => 'Nowy nr seryjny urządzenia: ', 'attr' => array('maxlength' => 30)))
                    ->add('dev_id', HiddenType::class)
                    ->add('submit2', SubmitType::class, array('label' => 'Zmień nr seryjny'))->getForm();
        if($request->request->has('form') && array_key_exists('sn', $request->request->get('form'))) $form->handleRequest($request);        
        if($form->isSubmitted() && $form->isValid()){
            $enabledTypes = $this->getDoctrine()->getRepository(TypeModSN::class)->findAll();
            $types = array();
            foreach($enabledTypes as $enType){
                $types[$enType->getId()] = $enType->getType()->getId();
            }
            $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('type' => $types, 'sn' => strtoupper($form->getData()->getSn())));
            if(sizeof($devices)==1)   {
                return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'devices' => $devices, 'changesn_form' => $form2->createView()));
            }
            else if(sizeof($devices)==0){
                return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Nie znaleziono żadnego urządzenia o podanym numerze seryjnym lub nie można mu zmienić nr seryjnego'));
            }
            else{
                return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Błąd. Znaleziono więcej niż jedno urządzenie o podanym numerze seryjnym'));             
            }
        }
        else{
            $form2->handleRequest($request);
            if($form2->isSubmitted()){
                if($form2->isValid()){
                    try{
                    //zostało tylko zapamiętać jakoś ostatnio wpisany nr seryjny przy błędzie                         
                        $device = $this->getDoctrine()->getRepository(Device::class)->find($form2->getData()->getDevId());
                        //$request->getSession()->set('searchsn', $device->getSN());
                        $form->get('sn')->setData($device->getSN());    //na razie zapamietujemy w pierwszym formularzu stary SN - zawsze można zmienić na nowy
                        $device->setSN(strtoupper($form2->getData()->getSerialNumber()));
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($device);
                        $em->flush();
                        return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'communicate_text' => 'Zmieniono nr seryjny urządzenia'));
                    }
                    catch(UniqueConstraintViolationException $ex){
                        return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Urządzenie o podanym numerze seryjnym już istnieje'));                    
                    }
                }
                else{
                    return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Numer seryjny może zawierać tylko litery i cyfry!'));
                }
            }
            else{
                return $this->render('changesn.html.twig', array('search_form' => $form->createView()));
            }
        }       
    }
    
    /**
    * @Route("/finddevice", name="finddevice")
     */
    public function finddevice(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $searchForm = new DeviceHistoryForm();
        $form = $this->createFormBuilder($searchForm)
                ->add('sn', TextType::class, array('label' => 'Podaj numer seryjny: ', 'required' => false))
                ->add('submit', ButtonType::class, array('label' => 'Wyszukaj'))->getForm();
        $findOperationForm = new FindOperationForm();
        $formDevices = $this->createFormBuilder($findOperationForm)
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
            ->add('current_sn', HiddenType::class)
            ->add('newdesc', HiddenType::class)
            ->getForm();        
        $formDevices->handleRequest($request);
        if($formDevices->isSubmitted() && $formDevices->isValid()){
            $checkboxes = $request->request->all('checkbox');
            //$currentLoc = $request->request->all('form')['current_loc'];
            $form->get('sn')->setData($formDevices->getData()->getCurrentSn());
            //dd($form2->get('typ')->getData());
            if(array_key_exists('send', $request->request->all('form'))){
                //dd($request->request->all('form'));
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $destLoc = $formDevices->getData()->getDestLoc();
                foreach($devices as $device){
                    if($device->getLocation()->getId()===$destLoc->getId()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Lokalizacja źródłowa i docelowa są takie same'));
                    else if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie w serwisie. Aby móc je wysłać musisz je przwyrócić z serwisu'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie zutylizowane. Wysyłka niemożliwa'));
                }
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setLocation($this->getDoctrine()->getRepository(Location::class)->find($destLoc));
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia'));
                //dd($devices);
            }
            else if(array_key_exists('service', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($devices as $device){
                    if($device->getLocation()->getId()!==1) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Nie można wysyłać urządzeń na serwis z innej lokalizacji niż Magazyn IT'));
                    else if($device->getState()==='S'){
                        $isBroken = false;
                        break;
                    }
                    else if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie jest już w serwisie'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie zutylizowane. Wysyłka na serwis niemożliwa'));
                }                    
                if(!$isBroken) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz wysłać też sprawne urządzenia na serwis'));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setService(true);
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Wysłano urządzenia na serwis'));
            }
            else if(array_key_exists('utilization', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($devices as $device){
                    if($device->getLocation()->getId()!==1) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Nie można utylizować urządzeń z innej lokalizacji niż Magazyn IT'));
                    else if($device->getState()==='S'){
                        $isBroken = false;
                        break;
                    }
                    //else if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie jest w serwisie. Aktualnie nie można go zutylizować.'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie jest już zutylizowane.'));
                }   
                if(!$isBroken) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Próbujesz zutylizować sprawne urządzenia'));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setUtilization(true);
                    $device->setPerson(null);
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Zutylizowano urządzenia'));
                
            }
            else if(array_key_exists('change_desc', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setDesc($formDevices->getData()->getNewDesc());
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Opis zmieniony'));
            }
            else if(array_key_exists('change_state', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie jest w serwisie. Nie mozna zmienić stanu tego urządzenia.'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urządzenie jest zutylizowane. Nie mozna zmienić stanu tego urządzenia.'));
                }                   
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
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Zmieniono stan urządzeń'));
            }
        }
        return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView()));
    }
    
    /**
    * @Route("/get_type_models", name="get_type_models")
     */
    public function getTypeModels(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $builder = new HtmlBuilder();
                $models = $this->getDoctrine()->getRepository(Type::class)->getSortedModelsByType($request->request->get('type'));
                $html = $builder->createSelectTagFromArray($request->request->get('label'), $request->request->get('selectId'), 
                    $request->request->get('selectName'), $models, $request->request->get('indexValue'), $request->request->get('indexName'));
                return new Response($html);
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
    * @Route("/devices_from_loc", name="devices_from_loc")
     */
    public function getDevicesFromLoc(Request $request){
        try{
            if($request->isXmlHttpRequest()){
                $type = $request->request->get('typ');
                $array = $this->getDoctrine()->getRepository(Device::class)->getDeviceByTypeFromLoc($type, 1);
                $builder = new HtmlBuilder();
                $html = $builder->createTable(array('Model','Stan','Numer seryjny','Numer seryjny 2','Opis'),
                    array(
                        new ArrayCell(array('name')),
                        new ArrayCell(array('state'), array('N' => 'td-font-red', 'S' => 'td-font-green')),
                        new ArrayCell(array('sn')),
                        new ArrayCell(array('sn2')),
                        new ArrayCell(array('desc'))
                    ),
                    $array, false
                );
                return new Response($html);
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
    * @Route("/devices_by_type_from_loc", name="devices_by_type_from_loc")
     */
    public function getDevicesByTypeFromLoc(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $type = $request->request->get('typ');
                $location = $request->request->get('loc');
                $array = $this->getDoctrine()->getRepository(Device::class)->getDeviceByTypeFromLoc($type, $location);
                $builder = new HtmlBuilder();
                $html = $builder->createTable(
                    array('Model','Stan','Numer seryjny','Numer seryjny 2','Opis','Zaznacz'), 
                    array(
                        new ArrayCell(array('name')),
                        new ArrayCell(array('state'), array('N' => 'td-font-red', 'S' => 'td-font-green')),
                        new ArrayCell(array('sn')),
                        new ArrayCell(array('sn2')),
                        new ArrayCell(array('desc')),
                        new ArrayCell(array('id'), null, new InputSpec('checkbox', 'checkbox', true))
                    ),
                    $array, false);
                return new Response($html);
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
    * @Route("/sorted_models_by_type", name="sorted_models_by_type")
     */
    public function getSortedModelsByType(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $type = $this->getDoctrine()->getRepository(Invoicing::class)->findBy(array('type' => $request->request->get('type')));
                $models = $this->getDoctrine()->getRepository(Type::class)->getSortedModelsByType($request->request->get('type'));
                $builder = new HtmlBuilder();
                $html = $builder->createSelectTagFromArray("Model urządzenia: ", "form_model", "form[model]", $models, "id", "name", $request->getSession()->get('rem_model'));
                $request->getSession()->remove('rem_model');
                if(sizeof($type)==0){
                    return new JsonResponse(array('inv' => "false", 'html' => $html));   
                }
                else{
                    return new JsonResponse(array('inv' => "true", 'html' => $html));  
                }
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
    * @Route("/devices_by_sn", name="devices_by_sn")
     */
    public function getDevicesBySN(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $currentSn = strtoupper($request->request->get('sn'));
                $array = $this->getDoctrine()->getRepository(Device::class)->getDevBySN($currentSn);
                $builder = new HtmlBuilder();
                $html = $builder->createTable(array('Typ','Model','Numer seryjny','Numer seryjy 2','Opis','Lokalizacja','W serwisie','Stan','Utylizacja','Zaznacz'),
                    array(
                        new ArrayCell(array('type_name')),
                        new ArrayCell(array('model_name')),
                        new ArrayCell(array('sn')),
                        new ArrayCell(array('sn2')),
                        new ArrayCell(array('desc')),
                        new ArrayCell(array('location_name')),
                        new ArrayCell(array('service'), null, null, null, array("1" => 'Tak', '0' => 'Nie')),
                        new ArrayCell(array('state'), array('N' => 'td-font-red', 'S' => 'td-font-green')),
                        new ArrayCell(array('utilization'), null, null, null, array("1" => 'Tak', '0' => 'Nie')),
                        new ArrayCell(array('id'), null, new InputSpec('checkbox', 'checkbox', true))
                    ),
                    $array,
                    false
                );
                return new Response($html);
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }
}
