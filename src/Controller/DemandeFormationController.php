<?php

namespace App\Controller;

use App\Entity\DemandeFormation;
use App\Entity\Formation;
use App\Entity\Formateur;
use App\Entity\Quiz;
use App\Entity\Beneficiaire;
use App\Form\DemandeFormationType;
use App\Repository\DemandeFormationRepository;
use App\Repository\FormationRepository;
use App\Repository\BeneficiaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demande/formation")
 */
class DemandeFormationController extends AbstractController
{
    /**
     * @Route("/", name="demande_formation_index", methods={"GET"})
     */
    public function index(Request $request ,DemandeFormationRepository $demandeFormationRepository): Response
    {
        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(Formation::class);
        $formation = $repo->findOneById($id);
        $demandeFormation = $formation->getQuiz()->getDemandeFormations();
        return $this->render('demande_formation/index.html.twig', [
            'demande_formations' => $demandeFormation,
        ]);
    }

    /**
     * @Route("/resultat/{id}", name="test_resultat", methods={"GET"})
     */
    public function resultat(DemandeFormation $demandeFormation): Response
    {
        if( $demandeFormation->getQuiz()->getFormation()->getFormateur()->getEmail() == $this->getUser()->getEmail())
        {
            return $this->render('quiz/resultat.html.twig', [
                'demande' => $demandeFormation,
            ]);
        }else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
            return $this->render('quiz/resultat.html.twig', [
                'demande' => $demandeFormation,
            ]);
        }
       
    }

     /**
     * @Route("/accept", name="demande_formation_accept", methods={"GET","POST"})
     */
    public function accept(Request $request, DemandeFormationRepository $demandeFormationRepository): Response
    {
        $id=$request->query->get('id');
        $demandeFormation = $demandeFormationRepository->findOneById($id);
        $demandeFormation->setState(1);
        $beneficiaire = $demandeFormation->getBeneficiaire();
        $beneficiaire->addFormation($demandeFormation->getQuiz()->getFormation());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($demandeFormation);
        $entityManager->flush();
        $entityManager->persist($beneficiaire);
        $entityManager->flush();

        return $this->redirectToRoute('demande_formation_index' , [
            'id' => $demandeFormation->getQuiz()->getFormation()->getId(),
        ]);

    }

    




    // /**
    //  * @Route("/new", name="demande_formation_new", methods={"GET","POST"})
    //  */
    // public function new(Request $request): Response
    // {
    //     $demandeFormation = new DemandeFormation();
    //     $form = $this->createForm(DemandeFormationType::class, $demandeFormation);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($demandeFormation);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('demande_formation_index');
    //     }

    //     return $this->render('demande_formation/new.html.twig', [
    //         'demande_formation' => $demandeFormation,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="demande_formation_show", methods={"GET"})
     */
    public function show(DemandeFormation $demandeFormation): Response
    {
        return $this->render('demande_formation/show.html.twig', [
            'demande_formation' => $demandeFormation,
        ]);
    }

    // /**
    //  * @Route("/{id}/edit", name="demande_formation_edit", methods={"GET","POST"})
    //  */
    // public function edit(Request $request, DemandeFormation $demandeFormation): Response
    // {
    //     $form = $this->createForm(DemandeFormationType::class, $demandeFormation);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();

    //         return $this->redirectToRoute('demande_formation_index', [
    //             'id' => $demandeFormation->getId(),
    //         ]);
    //     }

    //     return $this->render('demande_formation/edit.html.twig', [
    //         'demande_formation' => $demandeFormation,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="demande_formation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DemandeFormation $demandeFormation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeFormation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($demandeFormation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('demande_formation_index');
    }
}
