<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\Type\UserListType;
use App\Form\Type\UserType;
use App\Form\UserForm;
use App\Form\UserListSubformValidator;
use App\Form\UserListValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController{

    public function addUser(Request $request, UserPasswordHasherInterface $hasher){
        try{
            $this->denyAccessUnlessGranted('ROLE_ADMIN');   //obsluzyc ten wyjatek
            $userForm = new UserForm();
            $form = $this->createForm(UserType::class, $userForm);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $user = new User();
                $user->setLogin($form->getData()->getLogin());
                $user->setPass($hasher->hashPassword($user, $form->getData()->getPassword()));
                $user->setRoles($form->getData()->getRoles());
                $em->persist($user);
                $em->flush();
                return $this->render('adduser.html.twig', array('userForm' => $form->createView(), 'communicate_text' => 'Dodano użytkownika do systemu'));
            }
            else return $this->render('adduser.html.twig', array('userForm' => $form->createView()));
        }
        catch(UniqueConstraintViolationException $ex){
            return $this->render('adduser.html.twig', array('userForm' => $form->createView(), 'error_text' => 'Użytkownik o podanym loginie już istnieje'));
        }
    }

    public function usersList(Request $request){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $validator = new UserListValidator();
        $users = new ArrayCollection($this->getDoctrine()->getRepository(User::class)->findAll());
        $userForms = new ArrayCollection();     //walidacja subform działa, zrobić przekierowanie na inną stronę
        foreach($users as $user){
            $smallForm = new UserListSubformValidator();
            $smallForm->setLogin($user->getLogin());
            $smallForm->setId($user->getId());
            $smallForm->setRoles($user->getRoles());
            $userForms->add($smallForm);
        }
        $validator->setUsers($userForms);
        $userListForm = $this->createForm(UserListType::class, $validator);
        $userListForm->handleRequest($request);
        if($userListForm->isSubmitted() && $userListForm->isValid()){
            return $this->redirect($this->generateUrl('modifyuser', array('user_id' => $userListForm->getClickedButton()->getParent()->getData()->getId())));
        }
        if($request->getSession()->has('communicate')){
            $communicate_text = $request->getSession()->get('communicate');
            $request->getSession()->remove('communicate');
            return $this->render('userslist.html.twig', array('userListForm' => $userListForm->createView(), 'communicate_text' => $communicate_text));
        }
        else if($request->getSession()->has('error')){
            $error_text = $request->getSession()->get('error');
            $request->getSession()->remove('error');
            return $this->render('userslist.html.twig', array('userListForm' => $userListForm->createView(), 'error_text' => $error_text));
        }
        else{
            return $this->render('userslist.html.twig', array('userListForm' => $userListForm->createView()));
        }
    }

    public function modifyUser(Request $request, UserPasswordHasherInterface $hasher, $user_id){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $userValidator = new UserForm();
        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
        $userValidator->setLogin($user->getLogin());
        $userValidator->setRoles($user->getCollectionRoles());
        $form = $this->createForm(UserType::class, $userValidator, array(
            'validation_groups' => array('edit_user'), 
            'submit_text' => 'Modyfikuj',
            'remove_button' => true,
            'login_disabled' => true
        ));
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('login' => $form->getData()->getLogin()))[0];
            $em = $this->getDoctrine()->getManager();
            if($form->getClickedButton()===$form->get('submit')){             
                if($form->getData()->getPassword()!==null){
                    $user->setPass($hasher->hashPassword($user, $form->getData()->getPassword()));
                }
                $user->setRoles($form->getData()->getRoles());
                $em->persist($user);
                $em->flush();
                $request->getSession()->set('communicate', 'Zmodyfikowano użytkownika');
                return $this->redirectToRoute('userslist'); //zrobić jakoś komunikat że zmodyfikowano uzytkownika
            }
            else if($form->getClickedButton()===$form->get('remove')){
                try{
                    $em->remove($user);
                    $em->flush();
                    $request->getSession()->set('communicate', 'Usunięto użytkownika');
                    return $this->redirectToRoute('userslist');
                }
                catch(ForeignKeyConstraintViolationException $ex){
                    $request->getSession()->set('error', 'Nie można usunąć użytkownika. Użytkownik ma już historię!');
                    return $this->redirectToRoute('userslist'); //z komunikatem że nie mozna usunąć uzytkownika bo ma historię
                }
            }
        }        
        else{
            return $this->render('modifyuser.html.twig', array('userEditForm' => $form->createView()));
        }
    }
}