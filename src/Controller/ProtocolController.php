<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Protocol;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Type;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Device;
use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use App\Entity\Location;
use Doctrine\DBAL\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use App\Entity\Person;
use App\Form\AddProtocolForm;
use App\Form\AddProtocolZForm;
use App\Html\ArrayCell;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Html\HtmlBuilder;
use App\Html\InputSpec;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;

class ProtocolController extends AbstractController
{

    /**
     * @Route("/protocollist", name="protocollist")
     */
    public function protocollist(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $protocols = $this->getDoctrine()->getRepository(Protocol::class)->protocolList();
        return $this->render('protocollist.html.twig', array('protocols' => $protocols));
    }
    
    /** 
    * @Route("/protocol/{id}", name="protocol") 
    */
    public function showprotocol(int $id){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $protocol = $this->getDoctrine()->getRepository(Protocol::class)->find($id);  
        $devices = array();
        foreach($protocol->getDevices() as $device){
            $devices[$device->getId()] = '1x '.$device->getType()->getName().' '.$device->getModel()->getName().' sn: '.$device->getSN().',';
        }
        $devices['rest'] = $protocol->getRestDevices();
        return $this->render('protocol.html.twig', array(
            'type' => $protocol->getType(),
            'sender' => $protocol->getSender()->getName()." ".$protocol->getSender()->getSurname(),
            'principal' => $protocol->getPrincipal()->getName()." ".$protocol->getPrincipal()->getSurname(),
            'receiver' => $protocol->getReceiver()->getName()." ".$protocol->getReceiver()->getSurname()." - ".$protocol->getLocation()->getName()." ".$protocol->getLocation()->getShortName(),
            'devices' => $devices,
            'date' => $protocol->getDate()->format('Y-m-d')
        ));
    }
    
    /**
     * @Route("/addprotocol", name="addprotocol")
     */
    public function addprotocol(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $formAdd = $this->createFormBuilder()
                    ->add('type', EntityType::class, array(
                        'label' => false,
                        'choice_label' => 'name',
                        'class' => Type::class
                    ))
                    ->add('add', ButtonType::class, array('label' => 'Rezerwuj'))->getForm();
        $maxDate = new \DateTime('now');
        $addProtForm = new AddProtocolForm();
        $formSend = $this->createFormBuilder($addProtForm)  //zrobi?? formularz do wysy??ania i wtedy dodanie protoko??u
                    ->add('destination_loc', EntityType::class, array(
                        'label' => 'Lokalizacja: ',
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('l')->orderBy('l.name','ASC');
                        },
                        'choice_label' => function($loc){
                            return $loc->getName().' - '.$loc->getShortName();
                        },
                        'class' => Location::class//,
                        //'disabled' => true
                    ))
                    ->add('enable_change_loc', ButtonType::class, array('label' => 'Zmie?? lokalizacj??'))
                    ->add('principal', EntityType::class, array(
                        'label' => 'Zlecajacy: ',
                        'class' => Person::class,
                        'choice_label' => function($person){
                            return $person->getName()." ".$person->getSurname();
                        },
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                        }
                    ))
                    ->add('receiver', EntityType::class, array(
                        'label' => 'Osoba: ',
                        'class' => Person::class,
                        'choice_label' => function($person){
                            return $person->getName()." ".$person->getSurname();
                        },
                        'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                        }
                    ))
                    ->add('date', DateType::class, array(
                        'label' => 'Data: ',
                        'widget' => 'single_text',
                        'input' => 'datetime',
                        'attr' => array('max' => $maxDate->format('Y-m-d'), 'value' => $maxDate->format('Y-m-d'))
                    ))
                    ->add('rest_devices', TextareaType::class, array('label' => 'Dodatkowe urz??dzenia: ', 'required' => false, 'attr' => array('maxlength' => 255)))
                    ->add('submit', SubmitType::class, array('label' => 'Dodaj protok????'))->getForm();
                    
        $formSend->handleRequest($request); //obsluzyc form jesli cos i bedzie git
        if($formSend->isSubmitted() && $formSend->isValid()){
            if($formSend->getData()->getDestinationLoc()->getId()==1){
                return $this->render('addprotocol.html.twig', array('form_add' => $formAdd->createView(), 'form_send' => $formSend->createView(), 'error_text' => 'Nie mo??na stworzy?? protoko??u przekazania na lokalizacj?? Magazyn IT'));
            }
            else{
                $devices = $request->request->all('rem_checkbox');
                if(sizeof($devices)==0) return $this->render('addprotocol.html.twig', array('form_add' => $formAdd->createView(), 'form_send' => $formSend->createView(), 'error_text' => 'Nie dodano ??adnego urz??dzenia na protok????'));
                //$other = $request->request->all('form');
                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try{
                    //$newLocation = $this->getDoctrine()->getRepository(Location::class)->find($other['destination_loc']);
                    $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('login' => $request->getSession()->get(Security::LAST_USERNAME)))[0];
                    $protocol = new Protocol();
                    $protocol->setLocation($formSend->getData()->getDestinationLoc());
                    //$protocol->setPerson($other['person']);
                    //$protocol->setPrincipalOld($other['principal']);
                    $protocol->setDate($formSend->getData()->getDate());
                    $protocol->setRestDevices($formSend->getData()->getRestDevices());
                    $protocol->setReturned(false);
                    $protocol->setUser($user);
                    $protocol->setType('P');
                    $protocol->setSender($this->getDoctrine()->getRepository(Person::class)->find(1));
                    //zlecajacy i odbierajacy bo beda z listy zamiast starych person i principal
                    $protocol->setPrincipal($formSend->getData()->getPrincipal());
                    $protocol->setReceiver($formSend->getData()->getReceiver());
                    $collection = new ArrayCollection();
                    foreach($devices as $id){
                        $dev = $em->find('App\Entity\Device', $id, LockMode::PESSIMISTIC_WRITE);
                        $dev->setLocation($formSend->getData()->getDestinationLoc());
                        $dev->setPerson($formSend->getData()->getReceiver());
                        $em->persist($dev);
                        $collection->add($dev); //usun???? urzadzenia z rezerwacji!!!
                    }
                    $protocol->setDevices($collection);
                    $em->persist($protocol);      
                    foreach($this->getDoctrine()->getRepository(Reservation::class)->findBy(array('user' => $user)) as $res){
                        $em->remove($res);
                    }
                    $em->flush();
                    $em->getConnection()->commit();
                    //return $this->render('addprotocol.html.twig', array('form_add' => $formAdd->createView()));
                    return $this->redirectToRoute('protocol', array('id' => $protocol->getId()));    //tutaj b??dzie redirect response
                }
                catch(Exception $ex){
                    $em->getConnection()->rollback();
                    throw $ex;  //na razie zamiast return
                }
            }
        }
        else return $this->render('addprotocol.html.twig', array('form_add' => $formAdd->createView(), 'form_send' => $formSend->createView()));
        return $this->render('addprotocol.html.twig', array('form_add' => $formAdd->createView()));
    }
    
    /**
     * @Route("/gen_prot_ret", name="gen_prot_ret")
     */
    public function genProtRet(Request $request){       
        $this->denyAccessUnlessGranted('ROLE_USER');
        $maxDate = new \DateTime('now');
        $addProtoclZform = new AddProtocolZForm();
        $formGetDev = $this->createFormBuilder($addProtoclZform)
                        ->add('sender', EntityType::class, array(
                            'label' => 'Przekazuj??cy: ',
                            'class' => Person::class,
                            'choice_label' => function($person){
                                return $person->getName()." ".$person->getSurname();
                            },
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                            }
                        ))
                        ->add('rest_devices', TextareaType::class, array('label' => 'Pozosta??e urzadzenia: ', 'required' => false))
                        ->add('principal', EntityType::class, array(     //zmieni?? na pricnipal i mozna dziedziczyc
                            'label' => 'Po??rednicz??cy: ',
                            'class' => Person::class,
                            'choice_label' => function($person){
                                return $person->getName()." ".$person->getSurname();
                            },
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                            }
                            ))
                        ->add('receiver', EntityType::class, array(
                            'label' => 'Przyjmuj??cy: ',
                            'class' => Person::class,
                            'choice_label' => function($person){
                                return $person->getName()." ".$person->getSurname();
                            },
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                            }
                            ))
                        ->add('destination_loc', EntityType::class, array(
                            'label' => 'Lokalizacja docelowa: ',
                            'query_builder' => function(EntityRepository $er){
                                return $er->createQueryBuilder('l')->orderBy('l.name','ASC');
                            },
                            'choice_label' => function($loc){
                                return $loc->getName().' - '.$loc->getShortName();
                            },
                            'class' => Location::class//,
                            //'disabled' => true
                            ))
                        ->add('date', DateType::class, array(
                            'label' => 'Data: ',
                            'widget' => 'single_text',
                            'input' => 'datetime',
                            'attr' => array('max' => $maxDate->format('Y-m-d'), 'value' => $maxDate->format('Y-m-d'))
                        ))
                        ->add('enable_change_loc', ButtonType::class, array('label' => 'Zmie?? lokalizacj??'))
                        ->add('submit', SubmitType::class, array('label' => 'Wygeneruj protok????'))->getForm();
        $formGetDev->handleRequest($request);
        if($formGetDev->isSubmitted() && $formGetDev->isValid()) {    //sprobowac z validatorem bo te checkboxy ida osobno :D
            //dd($request);
            //$formData = $request->request->all('form');
            $devices = $request->request->all('dev_checkbox');
            $doctrine = $this->getDoctrine();
            if($formGetDev->getData()->getSender()===$formGetDev->getData()->getReceiver()) return $this->render('genprotret.html.twig', array('get_dev' => $formGetDev->createView(),'error_text' => 'Nadawca i odbiorca to ta sama osoba. Nie mozna doda?? protoko??u'));
            else if($formGetDev->getData()->getSender()===$formGetDev->getData()->getPrincipal()) return $this->render('genprotret.html.twig', array('get_dev' => $formGetDev->createView(),'error_text' => 'Nadawca i zlecaj??cy to ta sama osoba. Nie mozna doda?? protoko??u'));
            else if($formGetDev->getData()->getReceiver()===$formGetDev->getData()->getPrincipal()) return $this->render('genprotret.html.twig', array('get_dev' => $formGetDev->createView(),'error_text' => 'Odbiorca i zlecaj??cy to ta sama osoba. Nie mozna doda?? protoko??u'));
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{    
                //$personRepo = $doctrine->getRepository(Person::class);
                $changing = false;
                //dd($sender, intval($formData['destination_loc']));    tak mo??e by?? ale sprz??t mo??e by?? na innej lokalizacji i co wtedy??? 
                //przyklad: kierownik KB, sprzet KB, 2 kierownik OX, 2 kieorniwk zostaje tez kier. KB (trzeba bedzie recznie zmienic lok na KB), sprzet musi zosta?? i potem kier OX glowna i KB, sprzet na KB, nowy kier KB - przeanalizowac 
                if($formGetDev->getData()->getSender()->getId()!=$formGetDev->getData()->getDestinationLoc()->getId()/*$receiver->getLocation()->getId()*/){ 
                    $changing = true;
                }
                $protocol = new Protocol();
                $protocol->setLocation($formGetDev->getData()->getDestinationLoc());
                $protocol->setUser($doctrine->getRepository(User::class)->findBy(array('login' => $request->getSession()->get(Security::LAST_USERNAME)))[0]);
                $protocol->setRestDevices($formGetDev->getData()->getRestDevices());
                $protocol->setDate($formGetDev->getData()->getDate());
                $protocol->setReturned(false);
                $protocol->setType('Z');                    
                $protocol->setSender($formGetDev->getData()->getSender());
                $protocol->setPrincipal($formGetDev->getData()->getPrincipal());
                $protocol->setReceiver($formGetDev->getData()->getReceiver());
                $collection = new ArrayCollection();
                foreach($devices as $id){
                    $dev = $em->find('App\Entity\Device', $id, LockMode::PESSIMISTIC_WRITE);
                    $dev->setPerson($formGetDev->getData()->getReceiver());
                    if($changing){                            
                        $dev->setLocation($formGetDev->getData()->getReceiver()->getLocation());
                        $em->persist($dev);
                    }
                    $collection->add($dev);
                }
                $protocol->setDevices($collection);
                $em->persist($protocol);
                $em->flush();
                $em->getConnection()->commit();
                return $this->redirectToRoute('protocol', array('id' => $protocol->getId()));
            }
            catch(Exception $ex){
                $em->getConnection()->rollback();
                throw $ex;
            }
        }
        return $this->render('genprotret.html.twig', array('get_dev' => $formGetDev->createView()));
    }
    
    /**
     * @Route("/man_prot_ret", name="man_prot_ret")
     */
    public function manProtRet(Request $request){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $maxDate = new \DateTime('now');
        $formManProt = $this->createFormBuilder(null, array('allow_extra_fields' => true))
            ->add('sender', EntityType::class, array(
                'label' => 'Przekazuj??cy: ',
                'class' => Person::class,
                'choice_label' => function($person){
                    return $person->getName()." ".$person->getSurname();
                },
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                }
            ))
            ->add('rest_devices', TextareaType::class, array('label' => 'Pozosta??e urzadzenia: ', 'required' => false))
            ->add('intermediary', EntityType::class, array(
                'label' => 'Po??rednicz??cy: ',
                'class' => Person::class,
                'choice_label' => function($person){
                    return $person->getName()." ".$person->getSurname();
                },
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                }
                ))
            ->add('receiver', EntityType::class, array(
                'label' => 'Przyjmuj??cy: ',
                'class' => Person::class,
                'choice_label' => function($person){
                    return $person->getName()." ".$person->getSurname();
                },
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')->orderBy('p.name, p.surname','ASC');
                }
                ))
            ->add('destination_loc', EntityType::class, array(
                'label' => 'Lokalizacja docelowa: ',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('l')->orderBy('l.name','ASC');
                },
                'choice_label' => function($loc){
                    return $loc->getName().' - '.$loc->getShortName();
                },
                'class' => Location::class,
                'disabled' => true
                ))
            ->add('date', DateType::class, array(
                'label' => 'Data: ',
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => array('max' => $maxDate->format('Y-m-d'), 'value' => $maxDate->format('Y-m-d'))
            ))
            ->add('enable_change_loc', ButtonType::class, array('label' => 'Zmie?? lokalizacj??'))
            ->add('new_dev_type', EntityType::class, array(
                'label' => 'Typ urz??dzenia: ',
                'class' => Type::class,
                'choice_label' => 'name'
            ))
            /*->add('new_dev_model', EntityType::class, array(
                'label' => 'Model urz??dzenia: ',
                'class' => Model::class,
                'choice_label' => 'name'
            ))*/
            ->add('new_dev_sn', TextType::class, array('label' => 'Numer seryjny: ', 'required' => false, 'attr' => array('maxlength' => 30)))
            ->add('new_dev_sn2', TextType::class, array('label' => 'Numer seryjny 2: ', 'required' => false, 'attr' => array('maxlength' => 30)))
            ->add('add_new_dev', ButtonType::class, array('label' => 'Dodaj nowe urz??dzenie'))
            ->add('submit', SubmitType::class, array('label' => 'Wygeneruj protok????'))->getForm();
        $formManProt->handleRequest($request);
        if($formManProt->isSubmitted()){
            $formRequest = $request->request->all('form');
            $types = $request->request->all('type');
            $models = $request->request->all('model');
            $sns = $request->request->all('sn');
            $second_sn2 = $request->request->all('second_sn');
            if($formRequest['sender']===$formRequest['receiver']) return $this->render('manprotret.html.twig', array('man_prot_form' => $formManProt->createView(), 'error_text' => 'Nadawca i odbiorca to ta sama osoba. Nie mozna doda?? protoko??u'));
            else if($formRequest['sender']===$formRequest['intermediary']) return $this->render('manprotret.html.twig', array('man_prot_form' => $formManProt->createView(), 'error_text' => 'Nadawca i po??rednicz??cy to ta sama osoba. Nie mozna doda?? protoko??u'));
            else if($formRequest['receiver']===$formRequest['intermediary']) return $this->render('manprotret.html.twig', array('man_prot_form' => $formManProt->createView(), 'error_text' => 'Po??rednicz??cy i odbiorca to ta sama osoba. Nie mozna doda?? protoko??u'));            
            if(sizeof($types)==0) return $this->render('manprotret.html.twig', array('man_prot_form' => $formManProt->createView(), 'error_text' => 'Nie dodano ??adnego urz??dzenia. Nie mo??na doda?? protoko??u.')); ;
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try{
                $devices = new ArrayCollection(); 
                $i=1;
                for($i;$i<=sizeof($types);$i++){
                    $device = new Device();
                    $device->setType($em->find('App\Entity\Type', $types[$i]));
                    $device->setModel($em->find('App\Entity\Model', $models[$i]));
                    $device->setSN($sns[$i]);
                    if($second_sn2[$i]!=='') $device->setSN2($second_sn2[$i]);
                    $device->setDesc(null);
                    $device->setFV(true);
                    $device->setInvoicing(true);
                    $device->setLocation($em->find('App\Entity\Location', $formRequest['destination_loc']));
                    $device->setOperationTime(null);
                    $device->setService(false);
                    $device->setState('S');
                    $device->setUtilization(false);
                    $device->setPerson($em->find('App\Entity\Person', $formRequest['receiver']));
                    $em->persist($device);
                    $devices->add($device);
                }
                $em->flush();
                $protocol = new Protocol();
                $protocol->setLocation($em->find('App\Entity\Location',$formRequest['destination_loc']));
                $protocol->setUser($this->getDoctrine()->getRepository(User::class)->findBy(array('login' => $request->getSession()->get(Security::LAST_USERNAME)))[0]);
                $protocol->setRestDevices($formRequest['rest_devices']);
                $protocol->setDate(new \DateTime($formRequest['date']));
                $protocol->setReturned(false);
                $protocol->setType('Z');
                $protocol->setSender($em->find('App\Entity\Person',$formRequest['sender']));
                $protocol->setPrincipal($em->find('App\Entity\Person',$formRequest['intermediary']));
                $protocol->setReceiver($em->find('App\Entity\Person',$formRequest['receiver']));
                $protocol->setDevices($devices);
                $em->persist($protocol);
                $em->flush();
                $em->getConnection()->commit();
                return new RedirectResponse($this->generateUrl('protocol', array('id' => $protocol->getId())));
            }
            catch(Exception $ex){
                $em->getConnection()->rollback();
                return $this->render('manprotret.html.twig', array('man_prot_form' => $formManProt->createView(), 'error_text' => 'Urz??dzenie o podanym numerze seryjnym juz istnieje<br>'.$ex->getMessage()));
            }
        }
        return $this->render('manprotret.html.twig', array('man_prot_form' => $formManProt->createView()));
    }
    
    /**
     * @Route("/get_person_loc", name="get_person_loc")
     */
    public function getPersonLoc(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $person = $this->getDoctrine()->getRepository(Person::class)->find($request->request->get('receiver'));
                return new Response($person->getLocation()->getId());
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
     * @Route("/confirm_protocol", name="confirm_protocol")
     */
    public function confirmProtocol(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                if($request->request->get('name')==='confirm'){
                    $protocol = $this->getDoctrine()->getRepository(Protocol::class)->find($request->request->get('id'));
                    if($protocol!=null){
                        $protocol->setReturned(true);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($protocol);
                        $em->flush();
                        return new JsonResponse(array('id' => "#".$protocol->getId(), 'response' => 'Zwr??cony'));
                    }
                    else{
                        return new Response("B????d zatwierdzania protoko??u.");
                    }
                }
                elseif($request->request->get('name')==='show'){
                    
                }
                else{ return new Response($request->request->get('id')." ".$request->get('name'));}
            }    
        }  
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
    * @Route("/get_efficient_devices", name="get_efficient_devices")
    */
    public function getEfficientDevices(Request $request){
        try{
            $this->denyAccessUnlessGranted("ROLE_USER");
            if($request->isXmlHttpRequest()){     
                $devices = $this->getDoctrine()->getRepository(Device::class)->getEfficientDevices($request->request->get('type'));
                $builder = new HtmlBuilder();
                $html = $builder->createTable(
                    array('Model','Stan','Numer seryjny','Numer seryjny 2','Opis','Zarezerwowa??'),
                    array(
                        new ArrayCell(array('name')),
                        new ArrayCell(array('state'), array('S' => 'td-font-green', 'R' => 'td-font-red')),
                        new ArrayCell(array('sn')),
                        new ArrayCell(array('sn2')),
                        new ArrayCell(array('desc')),
                        new ArrayCell(array('id'), null, new InputSpec('checkbox', 'res_checkbox', false))            
                    ),
                    $devices, false
                );
                $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('login' => $request->getSession()->get(Security::LAST_USERNAME)));
                $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findBy(array('user' => $user));
                $html2 = "<h2 class='col-4'>Urz??dzenia w rezerwacji</h2><button id='del' onclick='unreserveClick(); return false;'>Usu?? rezerwacj??</button>";
                $html2 .= $builder->createTable(
                    array('Typ','Model','Stan','Numer seryjny','Numer seryjny 2','Opis','Usun???? rezerwacj??'),
                    array(
                        new ArrayCell(array('deviceTypeName')),
                        new ArrayCell(array('deviceModelName')),
                        new ArrayCell(array('deviceState'), array('S' => 'td-font-green', 'R' => 'td-font-red')),
                        new ArrayCell(array('deviceSN')),
                        new ArrayCell(array('deviceSN2')),
                        new ArrayCell(array('deviceDesc')),
                        new ArrayCell(array('deviceId'), null, new InputSpec('checkbox', 'rem_checkbox', true))
                    ),
                    $reservations, true
                );
                return new JsonResponse(array('devices' => $html, 'reserved' => $html2));
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
     * @Route("/reserve_devices", name="reserve_devices")
     */
    public function reserveDevices(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $em = $this->getDoctrine()->getManager();
                $devToRes = $request->request->all('checkboxes');
                foreach($devToRes as $id){
                    $reservation = new Reservation();
                    $reservation->setDevice($this->getDoctrine()->getRepository(Device::class)->find($id));
                    $reservation->setUser($this->getDoctrine()->getRepository(User::class)->findBy(array('login' => $request->getSession()->get(Security::LAST_USERNAME)))[0]);
                    $em->persist($reservation);
                }
                $em->flush();
            return new Response("true");
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
     * @Route("/unreserve_devices", name="unreserve_devices")
     */
    public function unreserveDevices(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $em = $this->getDoctrine()->getManager();
                $devToUnRes = $request->request->all('unreserve');
                foreach($devToUnRes as $id){
                    $reservation = new Reservation();
                    $reservation->setDevice($this->getDoctrine()->getRepository(Device::class)->find($id));
                    $reservation->setUser($this->getDoctrine()->getRepository(User::class)->findBy(array('login' => $request->getSession()->get(Security::LAST_USERNAME)))[0]);                  
                    $em->remove($em->merge($reservation));
                }
                $em->flush();
            return new Response("true");
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }

    /**
     * @Route("/get_person_devices", name="get_person_devices")
     */
    public function getPersonDevices(Request $request){
        try{
            $this->denyAccessUnlessGranted('ROLE_USER');
            if($request->isXmlHttpRequest()){
                $person = $request->request->get('sender');
                $devices = $this->getDoctrine()->getRepository(Device::class)->findBy(array('person' => $person));
                $builder = new HtmlBuilder();
                $html = $builder->createTable(
                    array('Typ','Model','Numer seryjny','Numer seryjny 2','Stan','Lokalizacja','Opis','W serwisie','Czy zwr??ci??'),
                    array(
                        new ArrayCell(array('typeName')),
                        new ArrayCell(array('modelName')),
                        new ArrayCell(array('SN')),
                        new ArrayCell(array('SN2')),
                        new ArrayCell(array('state'), array('S' => 'td-font-green', 'N' => 'td-font-red')),
                        new ArrayCell(array('locationName','locationShortName')),
                        new ArrayCell(array('desc')),
                        new ArrayCell(array('service'), null, null, null, array('1' => "Tak", '0' => 'Nie')),
                        new ArrayCell(array('id'), null, new InputSpec('checkbox', 'dev_checkbox', true, array('checked' => 'checked')))
                    ),
                    $devices, true
                );
                return new Response($html);
            }
        }
        catch(AccessDeniedException $ex){
            return new Response("unauthorized", 404);
        }
    }
}