<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Test;
use App\Form\QuestionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    #[Route('/questions', name: 'show_questions')]
    public function afficherTests(SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) || in_array('SUPER-ADMIN',$roles )){

            $query = $entityManager->createQueryBuilder()
            ->select('q.id','q.description','t.nomTest')
            ->from(Question::class, 'q')
            ->join(Test::class, 't', 'WITH', 't.id = q.id_Test')
            ->getQuery();
    
            $results = $query->getResult(); 
            return $this->render('question/afficherQuestion.html.twig', [
                'questions' => $results,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/ajouter/question', name: 'add_qst')]
    public function ajouterTests(SessionInterface $session,Request $request,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $question = new Question();
            $form = $this->createForm(QuestionFormType::class, $question);
            $form->handleRequest($request);

            
            if ($form->isSubmitted() && $form->isValid()){

                $idTest = $form->get('id_Test')->getData();
                $test = $entityManager->getRepository(Test::class)->find($idTest);
                $question->setIdTest($test);

                $entityManager->persist($question);
                $entityManager->flush();
            }
            
            return $this->render('question/addQuestion.html.twig', [
                'questionForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/delete/question/{id}', name: 'delete_qst')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $entity = $entityManager->getRepository(Question::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();
        return $this->redirectToRoute('show_questions');
    }

    #[Route('/modifier/question/{id}', name: 'edit_qst')]
    public function ModifierTest(EntityManagerInterface $entityManager,Question $question,Request $request,$id){

            $entity = $entityManager->getRepository(Question::class)->find($id);
            $form = $this->createForm(QuestionFormType::class, $question);
            
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $question = $form->getData();
                $entity->setDescription($form->get('description')->getData());
                $test = $entityManager->getRepository(Test::class)->find($form->get('id_Test')->getData());
                $entity->setIdTest($test);
                $entityManager->flush();
                return $this->redirectToRoute('show_questions');
            }
            return $this->render('question/addQuestion.html.twig', [
                'questionForm' => $form->createView(),
            ]);
    }

    #[Route('/ajouter/question/{id}', name: 'add_qst2')]
    public function ajouterTests2(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,$id): Response
    {
        
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $question = new Question();
            $form = $this->createForm(QuestionFormType::class, $question);
            $form->remove('id_Test');
            $form->handleRequest($request);

            $entity = $entityManager->getRepository(Test::class)->find($id);
            
            
            if ($form->isSubmitted() && $form->isValid())
            {
                $question->setIdTest($entity);
                $entityManager->persist($question);
                $entityManager->flush();
            }
            
            return $this->render('question/addQuestion.html.twig', [
                'questionForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
}
