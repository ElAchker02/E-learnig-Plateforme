<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Enseignant;
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
        if(in_array('ENSEIGNANT',$roles) ){

            $query = $entityManager->createQueryBuilder()
            ->select('t.id as idT','q.id','q.description','t.nomTest')
            ->from(Question::class, 'q')
            ->join(Test::class, 't', 'WITH', 't.id = q.id_Test')
            ->join(Enseignant::class, 'e', 'WITH', 't.id_Enseignant = e.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();
    
            $results = $query->getResult(); 
            return $this->render('question/afficherQuestion.html.twig', [
                'questions' => $results,
            ]);
        }
        elseif( in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $query = $entityManager->createQueryBuilder()
            ->select('t.id as idT','q.id','q.description','t.nomTest')
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
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
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
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Question::class)->find($id);

            $entityManager->remove($entity);
            $entityManager->flush();
            $this->addFlash('success', 'La supression a été effectué avec succès.');
            return $this->redirectToRoute('show_questions');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
       
    }

    #[Route('/modifier/question/{id}/{id2}', name: 'edit_qst')]
    public function ModifierTest(SessionInterface $session,EntityManagerInterface $entityManager,Question $question,Request $request,$id,$id2){

        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Question::class)->find($id);
            $entity2 = $entityManager->getRepository(Test::class)->find($id2);
            $form = $this->createForm(QuestionFormType::class, $question);
            $form->get('id_Test')->setData($entity2);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $question = $form->getData();
                $entity->setDescription($form->get('description')->getData());
                $test = $entityManager->getRepository(Test::class)->find($form->get('id_Test')->getData());
                $entity->setIdTest($test);
                $entityManager->flush();
                $this->addFlash('success', 'La modification a été effectué avec succès.');
                return $this->redirectToRoute('show_questions');
            }
            return $this->render('question/addQuestion.html.twig', [
                'questionForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
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
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
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
