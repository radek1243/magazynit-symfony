<?php
namespace App\Controller;

use App\Communicate\Communicate;
use App\Communicate\CommunicateBuilder;
use App\Communicate\CommunicateValidator;
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
use App\Form\DeviceByTypeTypeValidator;
use App\Form\DeviceForm;
use App\Form\FindOperationForm;
use App\Form\HistoryByDateForm;
use App\Form\OnlyTypeForm;
use App\Form\ServiceTypeValidator;
use App\Form\Type\DeviceByTypeType;
use App\Html\ArrayCell;
use App\Html\DoctrineCell;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Html\HtmlBuilder;
use App\Html\InputSpec;
use Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
                        'label' => 'Typ urz??dzenia: '
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
                        'label' => 'Dodaj urz??dzenie'
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
                return $this->render('adddevice.html.twig', array('addform' => $form->createView(), 'communicate_text' => 'Dodano urz??dzenie'));
            }
            catch(UniqueConstraintViolationException $ex){
                $error_text = "Urz??dzenie o podanym numerze seryjnym jest ju?? dodane.";
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
                return $this->render('model.html.twig', array('model_form' => $form->createView(), 'error_text' => 'Model o podanej nazwie ju?? istnieje', 'model_list' => $modelList));
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
            ->add('name', TextType::class, array('label' => 'Typ urz??dzenia: ', 'attr' => array('maxlength' => 30)))
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
                return $this->render('type.html.twig', array('type_form' => $form->createView(), 'error_text' => 'Podany typ ju?? istnieje', 'type_list' => $typeList));
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
                ->add('shortName', TextType::class, array('label' => 'Skr??t lokalizacji: ', 'attr' => array('maxlength' => 10)))
                ->add('submit', SubmitType::class, array('label' => "Dodaj lokalizacj??"))->getForm();
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
                return $this->render('location.html.twig', array('location_form' => $form->createView(), 'communicate_text' => 'Dodano lokalizacj??', 'loc_list' => $locList));
            }
            catch(UniqueConstraintViolationException $ex){
                $locList = $this->getDoctrine()->getRepository(Location::class)->findAll();
                return $this->render('location.html.twig', array('location_form' => $form->createView(), 'error_text' => 'Lokalizacja o podanym skr??cie jest ju?? dodana.', 'loc_list' => $locList));
            }
        }
        else{
            $locList = $this->getDoctrine()->getRepository(Location::class)->findAll();
            return $this->render('location.html.twig', array('location_form' => $form->createView(), 'loc_list' => $locList));
        }
    }
    
    /**
    * 
    * @Route("/invoicing/{type}/{communicate}", name="invoicing", defaults={"type": null, "communicate": null})
    * @ParamConverter("type", converter="type_converter", class="App\Entity\Type", options={ "mapping": { "type": "name"}})
    * @ParamConverter("communicate", converter="communicate_converter", class="string")
     */
    public function invoicing(Request $request, ?Type $type, ?Communicate $communicate, LoggerInterface $logger){
        $this->denyAccessUnlessGranted('ROLE_USER');    
        $validator = new DeviceByTypeTypeValidator();
        if($type!==null){           
            $validator->setType($type);
        }
        $options = array();
        $options['submits'] = [
            ['name' => 'submit_invoice', 'label' => 'Zafakturuj']
        ];
        $options['query_builder_where'] = 'd.location!=1 and d.type= :type and d.service=0 and d.id not in (select dev.id from App\Entity\Reservation r join r.device dev) and d.fv=0 and d.utilization=0 and '
        . 'd.invoicing=1';
        $options['choice_label_cells'] = [
            new DoctrineCell('ModelName', 'string', null),
            new DoctrineCell('State', 'string', array('N' => 'td-font-red', 'S' => 'td-font-green')), 
            new DoctrineCell('SN', 'string', null), 
            new DoctrineCell('SN2', 'string', null),
            new DoctrineCell('LocationName', 'string', null),
            new DoctrineCell('LocationShortName', 'string', null),
            new DoctrineCell('Desc', 'string', null), 
            new DoctrineCell('OperationTime', 'date', null)
        ];
        $form = $this->createForm(DeviceByTypeType::class, $validator, $options);
        if($request->isMethod('POST')) $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $em = $this->getDoctrine()->getManager();
                if($form->getClickedButton()===$form->get('submit_invoice')){
                    foreach($form->getData()->getDevices() as $device){
                        $device->setFV(true);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->redirectToRoute('invoicing', ['type' => urlencode( $form->getData()->getType()->getName()), 'communicate' => CommunicateBuilder::$INVOICE_SUCCESS]);
                }
                return $this->redirectToRoute('invoicing', ['type' => urlencode( $form->getData()->getType()->getName())]);
            }
            catch(Exception $ex){
                $logger->error($ex->getTraceAsString());
                return $this->redirectToRoute('invoicing', ['type' => urlencode( $form->getData()->getType()->getName()), 'communicate' => CommunicateBuilder::$GENERAL_ERROR]);
            }
        }
        $params = array('invoicing_form' => $form->createView());
        if($communicate!==null){
            $params['communicate_text'] = $communicate->getCommunicateText();
            $params['error_text'] = $communicate->getErrorText();
        }
        return $this->render('invoicing.html.twig', $params);
    }
    
    /**
    * @Route("/onservice/{type}/{communicate}", name="onservice", defaults={"type": null, "communicate": null})
    * @ParamConverter("type", converter="type_converter", class="App\Entity\Type", options={ "mapping": { "type": "name"}})
    * @ParamConverter("communicate", converter="communicate_converter", class="string")
     */
    public function onservice(Request $request, ?Type $type, ?Communicate $communicate, LoggerInterface $logger){
        $this->denyAccessUnlessGranted('ROLE_USER');    
        $validator = new DeviceByTypeTypeValidator();
        if($type!==null){           
            $validator->setType($type);
        }
        $form = $this->createForm(DeviceByTypeType::class, $validator);
        if($request->isMethod('POST')) $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $em = $this->getDoctrine()->getManager();
                if($form->getClickedButton()===$form->get('submit_return')){
                    foreach($form->getData()->getDevices() as $device){
                        $device->setService(false);
                        $device->setDesc(null);
                        $device->setState('S');
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->redirectToRoute('onservice', ['type' => urlencode( $form->getData()->getType()->getName()), 'communicate' => CommunicateBuilder::$RETURN_SERVICE_SUCCESS]);
                }
                else if($form->getClickedButton()===$form->get('submit_utilization')){
                    foreach($form->getData()->getDevices() as $device){
                        $device->setUtilization(true);
                        $em->persist($device);
                    }
                    $em->flush();
                    return $this->redirectToRoute('onservice', ['type' => urlencode( $form->getData()->getType()->getName()), 'communicate' => CommunicateBuilder::$UTILIZATION_SUCCESS]);
                }
                return $this->redirectToRoute('onservice', ['type' => urlencode( $form->getData()->getType()->getName())]);
            }
            catch(Exception $ex){
                $logger->error($ex->getTraceAsString());
                return $this->redirectToRoute('invoicing', ['type' => urlencode( $form->getData()->getType()->getName()), 'communicate' => CommunicateBuilder::$GENERAL_ERROR]);
            }
        }
        $params = array('onservice_form' => $form->createView());
        if($communicate!==null) {
            $params['communicate_text'] = $communicate->getCommunicateText();
            $params['error_text'] = $communicate->getErrorText();
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
                ->add('sn', TextType::class, array('label' => 'Numer seryjny urz??dzenia: ', 'attr' => array('maxlength' => 30)))
                ->add('submit', SubmitType::class, array('label' => 'Poka?? histori??'))->getForm();
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
            'label' => 'Wybierz dat??: ',
            'widget' => 'single_text',
            'input' => 'datetime',
            'attr' => array('max' => $maxDate->format('Y-m-d'), 'value' => $maxDate->sub(new \DateInterval('P30D'))->format('Y-m-d'))
        ))
        ->add('submit', SubmitType::class, array('label' => 'Poka?? histori??'))->getForm();
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
                    ->add('sn',TextType::class, array('label' => 'Numer seryjny urz??dzenia do zmiany: ', 'attr' => array('maxlength' => 30)))
                    ->add('submit', SubmitType::class, array('label' => 'Wyszukaj'))->getForm();
        $changeSnForm = new ChangeSnForm();
        $form2 = $this->createFormBuilder($changeSnForm)
                    ->add('serialnumber', TextType::class, array('label' => 'Nowy nr seryjny urz??dzenia: ', 'attr' => array('maxlength' => 30)))
                    ->add('dev_id', HiddenType::class)
                    ->add('submit2', SubmitType::class, array('label' => 'Zmie?? nr seryjny'))->getForm();
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
                return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Nie znaleziono ??adnego urz??dzenia o podanym numerze seryjnym lub nie mo??na mu zmieni?? nr seryjnego'));
            }
            else{
                return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'B????d. Znaleziono wi??cej ni?? jedno urz??dzenie o podanym numerze seryjnym'));             
            }
        }
        else{
            $form2->handleRequest($request);
            if($form2->isSubmitted()){
                if($form2->isValid()){
                    try{
                    //zosta??o tylko zapami??ta?? jako?? ostatnio wpisany nr seryjny przy b????dzie                         
                        $device = $this->getDoctrine()->getRepository(Device::class)->find($form2->getData()->getDevId());
                        //$request->getSession()->set('searchsn', $device->getSN());
                        $form->get('sn')->setData($device->getSN());    //na razie zapamietujemy w pierwszym formularzu stary SN - zawsze mo??na zmieni?? na nowy
                        $device->setSN(strtoupper($form2->getData()->getSerialNumber()));
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($device);
                        $em->flush();
                        return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'communicate_text' => 'Zmieniono nr seryjny urz??dzenia'));
                    }
                    catch(UniqueConstraintViolationException $ex){
                        return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Urz??dzenie o podanym numerze seryjnym ju?? istnieje'));                    
                    }
                }
                else{
                    return $this->render('changesn.html.twig', array('search_form' => $form->createView(), 'error_text' => 'Numer seryjny mo??e zawiera?? tylko litery i cyfry!'));
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
            ->add('send', SubmitType::class, array('label' => 'Wy??lij'))
            ->add('service', SubmitType::class, array('label' => 'Na serwis'))
            ->add('change_state', SubmitType::class, array('label' => "Zmiana stanu urz??dzenia"))
            ->add('change_desc', SubmitType::class, array('label' => 'Zmie?? opis urz??dzenia'))
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
                    if($device->getLocation()->getId()===$destLoc->getId()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Lokalizacja ??r??d??owa i docelowa s?? takie same'));
                    else if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie w serwisie. Aby m??c je wys??a?? musisz je przwyr??ci?? z serwisu'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie zutylizowane. Wysy??ka niemo??liwa'));
                }
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setLocation($this->getDoctrine()->getRepository(Location::class)->find($destLoc));
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Wys??ano urz??dzenia'));
                //dd($devices);
            }
            else if(array_key_exists('service', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($devices as $device){
                    if($device->getLocation()->getId()!==1) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Nie mo??na wysy??a?? urz??dze?? na serwis z innej lokalizacji ni?? Magazyn IT'));
                    else if($device->getState()==='S'){
                        $isBroken = false;
                        break;
                    }
                    else if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie jest ju?? w serwisie'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie zutylizowane. Wysy??ka na serwis niemo??liwa'));
                }                    
                if(!$isBroken) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Pr??bujesz wys??a?? te?? sprawne urz??dzenia na serwis'));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setService(true);
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Wys??ano urz??dzenia na serwis'));
            }
            else if(array_key_exists('utilization', $request->request->all('form'))){
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('id' => $checkboxes));
                $isBroken = true;
                foreach($devices as $device){
                    if($device->getLocation()->getId()!==1) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Nie mo??na utylizowa?? urz??dze?? z innej lokalizacji ni?? Magazyn IT'));
                    else if($device->getState()==='S'){
                        $isBroken = false;
                        break;
                    }
                    //else if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie jest w serwisie. Aktualnie nie mo??na go zutylizowa??.'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie jest ju?? zutylizowane.'));
                }   
                if(!$isBroken) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Pr??bujesz zutylizowa?? sprawne urz??dzenia'));
                $em = $this->getDoctrine()->getManager();
                foreach($devices as $device){
                    $device->setUtilization(true);
                    $device->setPerson(null);
                    $em->persist($device);
                }
                $em->flush();
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Zutylizowano urz??dzenia'));
                
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
                    if($device->getService()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie jest w serwisie. Nie mozna zmieni?? stanu tego urz??dzenia.'));
                    else if($device->getUtilization()) return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'error_text' => 'Urz??dzenie jest zutylizowane. Nie mozna zmieni?? stanu tego urz??dzenia.'));
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
                return $this->render('finddevice.html.twig', array('form' => $form->createView(), 'form_devices' => $formDevices->createView(), 'communicate_text' => 'Zmieniono stan urz??dze??'));
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
    * @Route("/sorted_models_by_type", name="sorted_models_by_type")
     */
    public function getSortedModelsByType(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $type = $this->getDoctrine()->getRepository(Invoicing::class)->findBy(array('type' => $request->request->get('type')));
                $models = $this->getDoctrine()->getRepository(Type::class)->getSortedModelsByType($request->request->get('type'));
                $builder = new HtmlBuilder();
                $html = $builder->createSelectTagFromArray("Model urz??dzenia: ", "form_model", "form[model]", $models, "id", "name", $request->getSession()->get('rem_model'));
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
