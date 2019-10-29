<?php

namespace App\Controller;

use App\Entity\DemandeFormateur;
use App\Entity\Beneficiaire;
use App\Entity\user;
use App\Entity\Formateur;

use App\Form\DemandeFormateurType;
use App\Repository\DemandeFormateurRepository;
use App\Repository\BeneficiaireRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demande/formateur")
 */
class DemandeFormateurController extends AbstractController
{
    /**
     * @Route("/", name="demande_formateur_index", methods={"GET"})
     */
    public function index(DemandeFormateurRepository $demandeFormateurRepository): Response
    {

        return $this->render('demande_formateur/index.html.twig', [
            'demande_formateurs' => $demandeFormateurRepository->findAll(),
        ]);
    }

    
    /**
     * @Route("/accept", name="accept", methods={"GET"})
     */
    public function accept(Request $request , DemandeFormateurRepository $demandeFormateurRepository): Response
    {
        $id=$request->query->get('id');
        $repo2 = $this->getDoctrine()->getRepository(User::class);
        $demandeFormateur = $demandeFormateurRepository->findOneById($id);
        $benef = $demandeFormateur->getBeneficiaire();
        $user = $repo2->findOneByEmail($benef->getEmail());
        $user->setState(1);
        // $roles[] = 'ROLE_FORMATEUR';
        // $user->setRoles($roles);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        $demandeFormateur->setState(1);
        
        $entityManager->persist($demandeFormateur);
        $entityManager->flush();

        $formateur = new Formateur();
        $formateur->setNom($benef->getNom());
        $formateur->setPrenom($benef->getPrenom());
        $formateur->setEmail($benef->getEmail());
        $formateur->setProfession($demandeFormateur->getProfession());

        $entityManager->persist($formateur);
        $entityManager->flush();
        
        return $this->redirectToRoute('demande_formateur_index');
    }


   

    /**
     * @Route("/new", name="demande_formateur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $demandeFormateur = new DemandeFormateur();
        $id=$request->query->get('id');
        $iduser=$request->query->get('user');
        $repo = $this->getDoctrine()->getRepository(Beneficiaire::class);
        $repo2 = $this->getDoctrine()->getRepository(User::class);
        
        $beneficiaire = $repo->findOneById($id);
        $user = $repo2->findOneById($iduser);
        
        $demandeFormateur->setState(0);
        $demandeFormateur->setBeneficiaire($beneficiaire);
        $form = $this->createForm(DemandeFormateurType::class, $demandeFormateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
               
            $brochureFile = $form['brochureFileName']->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $demandeFormateur->setBrochureFileName($newFilename);
            }




            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demandeFormateur);
            $entityManager->flush();

            return $this->redirectToRoute('beneficiaire_index_ben' , [ 'id' => $user->getId()]);
        }

        return $this->render('demande_formateur/new.html.twig', [
            'demande_formateur' => $demandeFormateur,
            'form' => $form->createView(),
        ]);
    }




    /**
     * @Route("/{id}", name="demande_formateur_show", methods={"GET"})
     */
    public function show(DemandeFormateur $demandeFormateur): Response
    {
        return $this->render('demande_formateur/show.html.twig', [
            'demande_formateur' => $demandeFormateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="demande_formateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DemandeFormateur $demandeFormateur): Response
    {
        $form = $this->createForm(DemandeFormateurType::class, $demandeFormateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('demande_formateur_index', [
                'id' => $demandeFormateur->getId(),
            ]);
        }

        return $this->render('demande_formateur/edit.html.twig', [
            'demande_formateur' => $demandeFormateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="demande_formateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DemandeFormateur $demandeFormateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demandeFormateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($demandeFormateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('demande_formateur_index');
    }
}
