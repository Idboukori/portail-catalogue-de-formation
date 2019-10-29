<?php

namespace App\Controller;

use App\Entity\Qa;
use App\Entity\Test;
use App\Form\QaType;
use App\Repository\QaRepository;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/qa")
 */
class QaController extends AbstractController
{
    /**
     * @Route("/", name="qa_index", methods={"GET"})
     */
    public function index(QaRepository $qaRepository): Response
    {
        return $this->render('qa/index.html.twig', [
            'qas' => $qaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="qa_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $qa = new Qa();
        $id = $request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(Test::class);
        $test = $repo->findOneById($id);
        $qa->setTest($test);
        $form = $this->createForm(QaType::class, $qa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($qa);
            $entityManager->flush();

            return $this->redirectToRoute('qa_index');
        }

        return $this->render('qa/new.html.twig', [
            'qa' => $qa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="qa_show", methods={"GET"})
     */
    public function show(Qa $qa): Response
    {
        return $this->render('qa/show.html.twig', [
            'qa' => $qa,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="qa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Qa $qa): Response
    {
        $form = $this->createForm(QaType::class, $qa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('qa_index', [
                'id' => $qa->getId(),
            ]);
        }

        return $this->render('qa/edit.html.twig', [
            'qa' => $qa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="qa_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Qa $qa): Response
    {
        if ($this->isCsrfTokenValid('delete'.$qa->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($qa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('qa_index');
    }
}
