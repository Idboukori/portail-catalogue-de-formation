<?php

namespace App\Controller;

use App\Entity\Logo;
use App\Form\LogoType;
use App\Repository\LogoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/base")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class LogoController extends AbstractController
{
    /**
     * @Route("/", name="logo_index", methods={"GET"})
     */
    public function index(Request $request ,LogoRepository $logoRepository): Response
    {
        $logo = $logoRepository->findAll();
        $img = $logo[0];
        return $this->render('base.html.twig', [
            'logo' => $img
        ]);
    }

    /**
     * @Route("/new", name="logo_new", methods={"GET","POST"})
     */
    public function new(Request $request ,LogoRepository $logoRepository): Response
    {
        $logo = $logoRepository->findAll();
        
        $form = $this->createForm(LogoType::class, $logo[0]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form['image']->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = 'logo';
                $newFilename = $safeFilename.'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $logo[0]->setImage($newFilename);
            }



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($logo[0]);
            $entityManager->flush();

            return $this->redirectToRoute('logo_index');
        }

        return $this->render('logo/new.html.twig', [
            'logo' => $logo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="logo_show", methods={"GET"})
     */
    public function show(Logo $logo): Response
    {
        return $this->render('logo/show.html.twig', [
            'logo' => $logo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="logo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Logo $logo): Response
    {
        $form = $this->createForm(LogoType::class, $logo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('logo_index', [
                'id' => $logo->getId(),
            ]);
        }

        return $this->render('logo/edit.html.twig', [
            'logo' => $logo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="logo_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Logo $logo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logo->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($logo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('logo_index');
    }
}
