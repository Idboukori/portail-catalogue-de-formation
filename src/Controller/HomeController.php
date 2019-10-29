<?php

namespace App\Controller;
use App\Entity\Formation;
use App\Entity\User;
use App\Repository\FormationRepository;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request , PaginatorInterface $paginator)
    {

        $repo = $this->getDoctrine()->getRepository(Formation::class);
        $reposi = $this->getDoctrine()->getRepository(User::class);
        $formations = $repo->findAll();
        
        $user = $paginator->paginate(
            $reposi->findAll(),$request->query->getInt('page',1), 5
        );
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController', 'formations' => $formations , 'users' => $user
        ]);
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos()
    {

        
        return $this->render('home/apropos.html.twig', [
        ]);
    }

    /**
     * @Route("/contactus", name="contact")
     */
    public function contact()
    {

        
        return $this->render('home/contactus.html.twig', [
        ]);
    }
}
