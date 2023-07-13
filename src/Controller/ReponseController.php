<?php

namespace App\Controller;

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
        if(in_array('ENSEIGNANT',$roles) || in_array('SUPER-ADMIN',$roles )){

            $query = $entityManager->createQueryBuilder()
            ->select('r.id','r.description','r.valide','t.nomTest','q.description')
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
            }
            
            return $this->render('reponse/addReponse.html.twig', [
                'reponseForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
}
