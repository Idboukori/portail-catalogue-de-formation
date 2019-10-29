<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="security")
     */
    public function register(Request $request , ObjectManager $manager , UserPasswordEncoderInterface $encoder)
    {
       $user = new User();
       $filesystem = new Filesystem();

       try {
           $filesystem->mkdir('/public/upload', 0700);
           $filesystem->appendToFile('logs.txt', 'Email sent to user@example.com');
           $filesystem->appendToFile('logs.txt', ' oky');
       } catch (IOExceptionInterface $exception) {
           echo "An error occurred while creating your directory at ".$exception->getPath();
       }
       $form=$this->createForm(RegistrationType::class , $user);

       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
           $hash = $encoder->encodePassword($user , $user->getPassword());

           $user->setPassword($hash);
           $state = 0;
           $user->setState($state);
           $user->setImage("user.png");
           $manager->persist($user);
           $manager->flush();

           return $this->redirectToRoute('beneficiaire_new', [ 
               'id' => $user->getId()
               ]);
       }
    
       return $this->render('security/registration.html.twig' , [
            'form' => $form->createView()
       ]
       );
    }

    /**
     * @Route("/login" , name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils){
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig' , ['last_username' => $lastUsername,
        'error' => $error,]);
        
    
    }
    /**
     * @Route("/logout" , name="logout")
     */
    public function logout(){
        
        return $this->redirectToRoute('login');
    }
}
