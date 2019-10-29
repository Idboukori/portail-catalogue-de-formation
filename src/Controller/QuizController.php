<?php

namespace App\Controller;
use App\Entity\Formation;
use App\Entity\Quiz;use App\Entity\Question;use App\Entity\Answers;
use App\Form\QuizType;use App\Form\QuestionType;use App\Form\AnswersType;
use App\Repository\QuizRepository;use App\Repository\QuestionRepository;use App\Repository\AnswersRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quiz")
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/", name="quiz_index", methods={"GET"})
     */
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quiz_new", methods={"GET","POST"})
     */
    public function new(Request $request , QuizRepository $quizRepository): Response
    {
        // $quiz = new Quiz();
        // // $question = new Question();
        // // $answer1= new Answers();
        // // $answer2= new Answers();
        // // $answer3= new Answers();
        // $form = $this->createForm(QuizType::class, $quiz);
        // // $form1 = $this->createForm(QuestionType::class, $question);
        // // $form2 = $this->createForm(AnswersType::class, $answer1);
        // // $form3 = $this->createForm(AnswersType::class, $answer2);
        // // $form4 = $this->createForm(AnswersType::class, $answer3);
        // $form->handleRequest($request);
        // // $form1->handleRequest($request);
        // // $form2->handleRequest($request);
        // // $form3->handleRequest($request);
        // // $form4->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($quiz);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('quiz_index');
        // }

        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(Formation::class);
        $formation = $repo->findOneById($id);
        dump($request);
         $quiz = $quizRepository->findOneByFormation($formation);
        if( $quiz == null){
            $quiz = new Quiz();
            $quiz->setFormation($formation);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quiz);
            $entityManager->flush();
        }
        
        if($request->request->count() > 0){
            
            $entityManager = $this->getDoctrine()->getManager();
            $question1 = new Question();
            $question1->setQuiz($quiz);
            $question1->setQuestion($request->request->get("question1"));
            $entityManager->persist($question1);
            $entityManager->flush();

            $answer1 = new Answers();
            $answer1->setQuestion($question1);
            $answer1->setAnswer($request->request->get('answer11'));
            if($request->request->get('checkanswer1') =="on"){
                $answer1->setState(1);

            }else{
                $answer1->setState(0);
            }
            $entityManager->persist($answer1);
            $entityManager->flush();

            $answer2 = new Answers();
            $answer2->setQuestion($question1);
            $answer2->setAnswer($request->request->get('answer21'));
            if($request->request->get('checkanswer2') =="on"){
                $answer2->setState(1);

            }else{
                $answer2->setState(0);
            }
            $entityManager->persist($answer2);
            $entityManager->flush();

            $answer3 = new Answers();
            $answer3->setQuestion($question1);
            $answer3->setAnswer($request->request->get('answer31'));
            if($request->request->get('checkanswer3') =="on"){
                $answer3->setState(1);

            }else{
                $answer3->setState(0);
            }
            $answer3->setState(0);
            $entityManager->persist($answer3);
            $entityManager->flush();
            return $this->redirectToRoute('quiz_new', [
                'id' => $formation->getId(),
                'quiz' => $quiz,
            ]);
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
        ]);
    }


    /**
     * @Route("/dele", name="quest_del", methods={"GET","POST"})
     */
    public function dele(Request $request ): Response
    {
        
        $id=$request->query->get('id');
        $repo = $this->getDoctrine()->getRepository(Question::class);
        $question = $repo->findOneById($id);
        foreach( $question->getAnswers() as $answer)
        {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($answer);
        $entityManager->flush();
        }
        $entityManager->remove($question);
        $entityManager->flush();
        
            return $this->redirectToRoute('quiz_new', [
                'id' => $question->getQuiz()->getFormation()->getId(),
                'quiz' => $question->getQuiz(),
            ]);
        
    }

   
    

    /**
     * @Route("/{id}", name="quiz_show", methods={"GET"})
     */
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quiz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quiz_index', [
                'id' => $quiz->getId(),
            ]);
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quiz $quiz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quiz_index');
    }
}
