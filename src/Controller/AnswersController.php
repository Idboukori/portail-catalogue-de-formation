<?php

namespace App\Controller;

use App\Entity\Answers;
use App\Form\AnswersType;
use App\Repository\AnswersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/answers")
 */
class AnswersController extends AbstractController
{
    /**
     * @Route("/", name="answers_index", methods={"GET"})
     */
    public function index(AnswersRepository $answersRepository): Response
    {
        return $this->render('answers/index.html.twig', [
            'answers' => $answersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="answers_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $answer = new Answers();
        $form = $this->createForm(AnswersType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($answer);
            $entityManager->flush();

            return $this->redirectToRoute('answers_index');
        }

        return $this->render('answers/new.html.twig', [
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="answers_show", methods={"GET"})
     */
    public function show(Answers $answer): Response
    {
        return $this->render('answers/show.html.twig', [
            'answer' => $answer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="answers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Answers $answer): Response
    {
        $form = $this->createForm(AnswersType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('answers_index', [
                'id' => $answer->getId(),
            ]);
        }

        return $this->render('answers/edit.html.twig', [
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="answers_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Answers $answer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$answer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($answer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('answers_index');
    }
}
