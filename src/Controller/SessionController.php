<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Beneficiaire;
use App\Entity\Formation;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Repository\BeneficiaireRepository;
use App\Repository\FormmationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/session")
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/", name="session_index", methods={"GET"})
     */
    public function index(SessionRepository $sessionRepository): Response
    {
        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="session_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(Formation::class);
        $formation = $repo->findOneById($id);
        if($formation->getFormateur()->getEmail() == $this->getUser()->getEmail()){
            $session = new Session();
            $session->setFormation($formation);
            $session->setState(0);
            $form = $this->createForm(SessionType::class, $session);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($session);
                $entityManager->flush();
    
                return $this->redirectToRoute('session_index');
            }
    
            return $this->render('session/new.html.twig', [
                'session' => $session,
                'form' => $form->createView(),
            ]);
        }else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
            $session = new Session();
            $session->setFormation($formation);
            $form = $this->createForm(SessionType::class, $session);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($session);
                $entityManager->flush();
    
                return $this->redirectToRoute('session_index');
            }
    
            return $this->render('session/new.html.twig', [
                'session' => $session,
                'form' => $form->createView(),
            ]);
        }
        
    }

    

    /**
     * @Route("/addben", name="reg_session", methods={"GET","POST"})
     */
    public function addben(Request $request): Response
    {
        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(Session::class);
        $session=$repo->findOneById($id);
        $repo2 = $this->getDoctrine()->getRepository(Beneficiaire::class);
        $beneficiaire=$repo2->findOneByEmail($this->getUser()->getEmail());
        $session->addBeneficiaire($beneficiaire);
        $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($session);
                $entityManager->flush();
        return $this->redirectToRoute('formation_show' , ['id' => $session->getFormation()->getId() , 'benef' => $beneficiaire]);
    }

    /**
     * @Route("/{id}", name="session_show", methods={"GET"})
     */
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Session $session): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('session_index', [
                'id' => $session->getId(),
            ]);
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Session $session): Response
    {
        if ($this->isCsrfTokenValid('delete'.$session->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }
}
