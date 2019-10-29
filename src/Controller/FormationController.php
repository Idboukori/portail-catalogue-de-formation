<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Commentaire;
use App\Entity\Search;
use App\Entity\Session;
use App\Form\SessionType;
use App\Form\SearchType;
use App\Entity\Answers;
use App\Entity\DemandeFormation;
use App\Entity\Formateur;
use App\Entity\Quiz;
use App\Entity\User;
use App\Entity\Beneficiaire;
use App\Form\FormationType;
use App\Form\CommentaireType;
use App\Repository\FormationRepository;
use App\Repository\AnswersRepository;
use App\Repository\QuizRepository;
use App\Repository\FormateurRepository;
use App\Repository\UserRepository;
use App\Repository\BeneficiaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/formation")
 */
class FormationController extends AbstractController
{
    /**
     * @Route("/", name="formation_index", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(FormationRepository $formationRepository , Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $search= new Search();
        $form =$this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(Beneficiaire::class);
        $repouser = $this->getDoctrine()->getRepository(User::class);
        
        $user = $this->getUser();
        
        $beneficiaire = $repo->findOneByEmail($user->getEmail());
        $list = $beneficiaire->getFormations();
        $array = $beneficiaire->getDemandeFormations();
        $lista = null;
        foreach( $array as $dem){
            $lista[] = $dem->getQuiz()->getFormation();
        }
        $formation = $paginator->paginate(
            $formationRepository->findAllVisibleQuery($search),$request->query->getInt('page',1), 9
        );
        return $this->render('formation/index.html.twig', [
            'formations' => $formation,
            'list' => $list,
            'array' => $lista,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/myList", name="mes_formations", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function myList(FormationRepository $formationRepository , Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $search= new Search();
        $form =$this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getRepository(Beneficiaire::class);
        
        $user = $this->getUser();
        
        $beneficiaire = $repo->findOneByEmail($user->getEmail());
        $list = $beneficiaire->getFormations();
        
        $formation = $paginator->paginate(
            $list,$request->query->getInt('page',1), 4
        );
        return $this->render('formation/mylist.html.twig', [
            'formations' => $formation,
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/o", name="formation_index_out", methods={"GET"})
     */
    public function indexout(FormationRepository $formationRepository , Request $request, PaginatorInterface $paginator): Response
    {
        $search= new Search();
        $form =$this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        $formation = $paginator->paginate(
            $formationRepository->findAllVisibleQuery($search),$request->query->getInt('page',1), 9
        );
        return $this->render('formation/index.html.twig', [
            'formations' => $formation,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/test", name="test", methods={"GET","POST"})
     */
    public function test(Request $request): Response
    {
        $idformation=$request->query->get('idformation');
        $idbenef = $request->query->get('idbenef');
        
        $repo = $this->getDoctrine()->getRepository(Quiz::class);
        $repo2 = $this->getDoctrine()->getRepository(Beneficiaire::class);
        $repo3 = $this->getDoctrine()->getRepository(Formation::class);

        $formation =  $repo2->findOneById($idformation);     
        $beneficiaire = $repo2->findOneByEmail($idbenef);
        $quiz = $repo->findOneByFormation($formation);
        dump($request);
        
        if($request->request->count() > 0){
            $demandeFormation = new DemandeFormation();
            $demandeFormation->setQuiz($quiz);
            $demandeFormation->setBeneficiaire($beneficiaire);
            $demandeFormation->setState(0);
            $list = $quiz->getQuestions();
            foreach($list as $qu){
                $repoans = $this->getDoctrine()->getRepository(Answers::class);
                $answer = $repoans->findOneByAnswer($request->request->get(strval($qu->getId())));
                $demandeFormation->addAnswer($answer);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($demandeFormation);
                $entityManager->flush();
            }
            

            return $this->redirectToRoute('formation_index'
            , [
                 'email' => $idbenef,]
        );

        }

        return $this->render('quiz/test.html.twig', [
                'quiz' => $quiz 
            ]);
    }



    /**
     * @Route("/inscrire", name="formation_inscrire", methods={"GET"})
     */
    public function inscrire(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repo = $this->getDoctrine()->getRepository(Beneficiaire::class);
        $id=$request->query->get('id');
        $email=$request->query->get('id2');
        $repo2 = $this->getDoctrine()->getRepository(Formation::class);
        $formation = $repo2->findOneById($id);
        $beneficiaire = $repo->findOneByEmail($email);
            
        return $this->redirectToRoute('test'
        , [
             'idformation' => $id, 'idbenef' => $email]
    );
    }
    
    // /**
    //  * @Route("/inscrire", name="formation_inscrire", methods={"GET"})
    //  */
    // public function inscrire(Request $request): Response
    // {
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     $repo = $this->getDoctrine()->getRepository(Beneficiaire::class);
    //     $id=$request->query->get('id');
    //     $email=$request->query->get('id2');
    //     $repo2 = $this->getDoctrine()->getRepository(Formation::class);
    //     $formation = $repo2->findOneById($id);
    //     $beneficiaire = $repo->findOneByEmail($email);
    //     $formation->addBeneficiaire($beneficiaire);
        
    //     $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($beneficiaire);
    //         $entityManager->flush();
    //     return $this->redirectToRoute('formation_index'
    //     , [
    //          'email' => $email,]
    // );
    // }

    /**
     * @Route("/new", name="formation_new" , methods={"GET","POST"})
    */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $formation = new Formation();
        $repo = $this->getDoctrine()->getRepository(Formateur::class);
        $id=$request->query->get('id');
        $formateur = $repo->findOneByEmail($id);
        if($formateur->getEmail() === $this->getUser()->getEmail()){
        $formation->setFormateur($formateur);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        $quiz = new Quiz();
        $quiz->setFormation($formation);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();
            $brochureFile = $form['image']->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = 'formation';
                $str =strval($formation->getId());
                
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
                $formation->setImage($newFilename);
            }



            $entityManager->persist($formation);
            $entityManager->flush();
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('quiz_new', [ 'id' => $formation->getId()]);
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
        }

        else
        
        {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
        $formation->setFormateur($formateur);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('quiz_new', [ 'id' => $formation->getId()] );
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
        }
        
    }

    /**
     * @Route("/{id}", name="formation_show", methods={"GET" , "POST"})
     */
    public function show(Request $request, Formation $formation): Response
    {
        if( $this->getUser()){
            $repo = $this->getDoctrine()->getRepository(Beneficiaire::class);
            $benef = $repo->findOneByEmail($this->getUser()->getEmail());
            if( $formation->getFormateur()->getEmail() == $this->getUser()->getEmail()){
                $session = new Session();
                $session->setFormation($formation);
                $session->setState(0);
                $form = $this->createForm(SessionType::class, $session);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($session);
                    $entityManager->flush();
        
                    return $this->redirectToRoute('formation_show' , ['id' => $formation->getId() , 'benef' => $benef]);
                }
                $comment= new Commentaire();
                $comment->setFormation($formation);
                $comment->setBenef($benef);
                $form2 = $this->createForm(CommentaireType::class, $comment);
                $form2->handleRequest($request);
        
                if ($form2->isSubmitted() && $form2->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($comment);
                    $entityManager->flush();
        
                    return $this->redirectToRoute('formation_show' , ['id' => $formation->getId() , 'benef' => $benef]);
                }

                return $this->render('formation/show.html.twig', [
                    'session' => $session,
                    'form' => $form->createView(),
                    'formation' => $formation,
                    'benef' => $benef,
                    'form2' => $form2->createView()
                ]);
           
            }
            else{
                $comment= new Commentaire();
                $comment->setFormation($formation);
                $comment->setBenef($benef);
                $form2 = $this->createForm(CommentaireType::class, $comment);
                $form2->handleRequest($request);
        
                if ($form2->isSubmitted() && $form2->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($comment);
                    $entityManager->flush();
        
                    return $this->redirectToRoute('formation_show' , ['id' => $formation->getId() , 'benef' => $benef]);
                }
                return $this->render('formation/show.html.twig', [
                    'formation' => $formation,
                    'benef' => $benef,
                    'form2' => $form2->createView()

    ]);
            }
        }
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
]);
        
        
    }

    /**
     * @Route("/{id}/edit", name="formation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Formation $formation): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($formation->getFormateur()->getEmail() === $this->getUser()->getEmail()){
            $form = $this->createForm(FormationType::class, $formation);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $brochureFile = $form['image']->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = 'formation';
                $str =strval($formation->getId());
                
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
                $formation->setImage($newFilename);
            }
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('formation_index', [
                    'email' =>$this->getUser()->getEmail(),
                ]);
            }
    
            return $this->render('formation/edit.html.twig', [
                'formation' => $formation,
                'form' => $form->createView(),
            ]);
        }else{
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
            $form = $this->createForm(FormationType::class, $formation);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('formation_index', [
                    'email' => $this->getUser()->getEmail(),
                ]);
            }
    
            return $this->render('formation/edit.html.twig', [
                'formation' => $formation,
                'form' => $form->createView(),
            ]);
        }
        
    }

    /**
     * @Route("/{id}", name="formation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Formation $formation): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('formation_index', [
            'email' => $this->getUser()->getEmail(),
        ]);
    }
}
