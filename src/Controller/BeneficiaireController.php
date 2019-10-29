<?php

namespace App\Controller;

use App\Entity\Beneficiaire;
use App\Entity\User;
use App\Form\BeneficiaireType;
use App\Form\UserType;
use App\Repository\BeneficiaireRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Component\Pager\PaginatorInterface;
/**
 * @Route("/beneficiaire")
 */
class BeneficiaireController extends AbstractController
{
    /**
     * @Route("/", name="beneficiaire_index", methods={"GET"})
     */
    public function index(Request $request ,BeneficiaireRepository $beneficiaireRepository, PaginatorInterface $paginator): Response
    {
        $benflist = $paginator->paginate(
                $beneficiaireRepository->findAllVisibleQuery(),$request->query->getInt('page',1), 4
            );
        return $this->render('beneficiaire/index.html.twig', [
            'beneficiaires' => $benflist,
        ]);
    }


    /**
     * @Route("/profil", name="profil", methods={"GET","POST"})
     */
    public function profil(Request $request , BeneficiaireRepository $beneficiaireRepository ): Response
    {
        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneById($id);


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $brochureFile = $form['image']->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = 'profilpic';
                $str =strval($user->getId());
                
                $newFilename = $safeFilename.'-'.$str.'.'.$brochureFile->guessExtension();

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
                $user->setImage($newFilename);
            }



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profil', [
                'id' => $user->getId()
                ]);
        }


        return $this->render('profile/profiluser.html.twig', [
            'beneficiaire' => $beneficiaireRepository->findOneByEmail($this->getUser()->getEmail()),
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/b", name="beneficiaire_index_ben", methods={"GET"})
     */
    public function indexben(Request $request , BeneficiaireRepository $beneficiaireRepository, PaginatorInterface $paginator): Response
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $id=$request->query->get('id');
        $user = $repo->findOneById($id);
        if($id == $this->getUser()->getId()){
            $benef = $beneficiaireRepository->findOneByEmail($user->getEmail());
            
            if($benef->getDemandeFormateur() == null ){
                $stat = 0;
            }else{
                if( $user->getState() == 0){
                    $stat = 1;
                }
                else{
                    $stat = 2;
                }
            }
            $benflist = $paginator->paginate(
                $beneficiaireRepository->findAllVisibleQuery(),$request->query->getInt('page',1), 4
            );
        
        return $this->render('beneficiaire/index.html.twig', [
            'beneficiaires' => $benflist,
            'stat' => $stat
        ]);
        }else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
            $benef = $beneficiaireRepository->findOneByEmail($user->getEmail());
            
            if($benef->getDemandeFormateur() == null ){
                $stat = 0;
            }else{
                if( $user->getState() == 0){
                    $stat = 1;
                }
                else{
                    $stat = 2;
                }
            }
        $benflist = $paginator->paginate(
            $beneficiaireRepository->findAllVisibleQuery(),$request->query->getInt('page',1), 4
        );
        
        return $this->render('beneficiaire/index.html.twig', [
            'beneficiaires' => $benflist,
            'stat' => $stat
        ]);
        }
           
    }



    /**
    * @Route("/new", name="beneficiaire_new", methods={"GET","POST"})
    */
    public function new(Request $request): Response
    {

        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneById($id);
        $beneficiaire = new Beneficiaire();
        $beneficiaire->setEmail($user->getEmail());
        $beneficiaire->setNom($user->getNom());
        $beneficiaire->setPrenom($user->getPrenom());
        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($beneficiaire);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        

    }

    /**
     * @Route("/newad", name="beneficiaire_newad", methods={"GET","POST"})
     * Security("is_granted('ROLE_ADMIN')")
     */
    public function newad(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $beneficiaire = new Beneficiaire();
        $form = $this->createForm(BeneficiaireType::class, $beneficiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($beneficiaire);
            $entityManager->flush();

            return $this->redirectToRoute('beneficiaire_index');
        }

        return $this->render('beneficiaire/new.html.twig', [
            'beneficiaire' => $beneficiaire,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="beneficiaire_show", methods={"GET"})
     */
    public function show(Beneficiaire $beneficiaire): Response
    {
        return $this->render('beneficiaire/show.html.twig', [
            'beneficiaire' => $beneficiaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="beneficiaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Beneficiaire $beneficiaire): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repo = $this->getDoctrine()->getRepository(User::class);
        $iduser=$request->query->get('user');
        $user=$repo->findOneById($iduser);
        if($beneficiaire->getEmail() == $user->getEmail()){
            $form = $this->createForm(BeneficiaireType::class, $beneficiaire);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $user->setEmail($beneficiaire->getEmail());
                $user->setNom($beneficiaire->getNom());
                $user->setPrenom($beneficiaire->getPrenom());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('beneficiaire_index_ben', [
                    'id' => $user->getId(),
                ]);
            }
    
            return $this->render('beneficiaire/edit.html.twig', [
                'beneficiaire' => $beneficiaire,
                'form' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('logout');
        }
       
    }

    /**
     * @Route("/{id}", name="beneficiaire_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Beneficiaire $beneficiaire): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$beneficiaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($beneficiaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('beneficiaire_index');
    }
}
