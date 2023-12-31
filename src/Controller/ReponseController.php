<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\Test;
use App\Form\ReponseFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ReponseController extends AbstractController
{
    #[Route('/reponses', name: 'show_reponse')]
    public function afficherTests(SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){

            $query = $entityManager->createQueryBuilder()
            ->select('q.id as idQ','r.id','r.description AS descriptionR ','r.valide','t.nomTest','q.description')
            ->from(Reponse::class, 'r')
            ->join(Question::class, 'q', 'WITH', 'r.id_Question = q.id')
            ->join(Test::class, 't', 'WITH', 't.id = q.id_Test')
            ->join(Enseignant::class, 'e', 'WITH', 't.id_Enseignant = e.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();
            
            $results = $query->getResult(); 
            return $this->render('reponse/afficherReponse.html.twig', [
                'reponses' => $results,
            ]);
        }
        elseif( in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $query = $entityManager->createQueryBuilder()
            ->select('q.id as idQ','r.id','r.description AS descriptionR ','r.valide','t.nomTest','q.description')
            ->from(Reponse::class, 'r')
            ->join(Question::class, 'q', 'WITH', 'r.id_Question = q.id')
            ->join(Test::class, 't', 'WITH', 't.id = q.id_Test')
            ->getQuery();
            
            $results = $query->getResult(); 
            return $this->render('reponse/afficherReponse.html.twig', [
                'reponses' => $results,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/ajouter/reponse', name: 'add_reponse')]
    public function ajouterTests(SessionInterface $session,Request $request,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $reponse = new Reponse();
            $form = $this->createForm(ReponseFormType::class, $reponse);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()){

                $idQuestion = $form->get('id_Question')->getData();
                $qst = $entityManager->getRepository(Question::class)->find($idQuestion);
                $reponse->setIdQuestion($qst);

                $entityManager->persist($reponse);
                $entityManager->flush();
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            }
            
            return $this->render('reponse/addReponse.html.twig', [
                'reponseForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/ajouter/reponse/{id}', name: 'add_reponse2')]
    public function ajouterTests2(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,$id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $reponse = new Reponse();
            $form = $this->createForm(ReponseFormType::class, $reponse);
            $form->remove('id_Question');
            $form->handleRequest($request);
            
            $entity = $entityManager->getRepository(Question::class)->find($id);
            
            if ($form->isSubmitted() && $form->isValid()){
                $reponse->setIdQuestion($entity);   
                $entityManager->persist($reponse);
                $entityManager->flush();
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            }
            
            return $this->render('reponse/addReponse.html.twig', [
                'reponseForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
    #[Route('/delete/reponse/{id}', name: 'delete_rep')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Reponse::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();
        $this->addFlash('success', 'La supression a été effectué avec succès.');
        return $this->redirectToRoute('show_reponse');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }

    #[Route('/modifier/reponse/{id}/{id2}', name: 'edit_rep')]
    public function ModifierTest(SessionInterface $session,EntityManagerInterface $entityManager,Reponse $reponse,Request $request,$id,$id2){
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Reponse::class)->find($id);
            $entity2 = $entityManager->getRepository(Question::class)->find($id2);
            $form = $this->createForm(ReponseFormType::class, $reponse);
            $form->get('id_Question')->setData($entity2);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $reponse = $form->getData();
                $entity->setDescription($form->get('description')->getData());
                $entity->setValide($form->get('valide')->getData());
                $question = $entityManager->getRepository(Question::class)->find($form->get('id_Question')->getData());
                $entity->setIdQuestion($question);
                $entityManager->flush();
                $this->addFlash('success', 'La modification a été effectué avec succès.');
                return $this->redirectToRoute('show_reponse');
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
