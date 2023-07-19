<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Test;
use App\Form\TestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

class TestController extends AbstractController
{
    
    #[Route('/tests', name: 'show_test' , methods:'GET')]
    public function afficherTests(SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){

            $query = $entityManager->createQueryBuilder()
            ->select('t.id','t.nomTest','t.duree')
            ->from(Test::class, 't')
            ->join(Enseignant::class, 'e', 'WITH', 't.id_Enseignant = e.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();

            $results = $query->getResult();
            return $this->render('test/afficherTest.html.twig', [
                'tests' => $results,
            ]);
        }
        elseif( in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $query = $entityManager->createQueryBuilder()
            ->select('t.id','t.nomTest','t.duree')
            ->from(Test::class, 't')
            ->getQuery();

            $results = $query->getResult();
            return $this->render('test/afficherTest.html.twig', [
                'tests' => $results,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/ajouter/test', name: 'add_test')]
    public function ajouterTests(NotifierInterface $notifier,SessionInterface $session,Request $request,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $test = new Test();
            $form = $this->createForm(TestFormType::class, $test);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()){

                $idCours = $form->get('id_Cours')->getData();
                $cours = $entityManager->getRepository(Cours::class)->find($idCours);
                $test->setIdCours($cours);
                $enseignant = $entityManager->getRepository(Enseignant::class)->find(reset($roles));
                $test->setIdEnseignant($enseignant);

                $entityManager->persist($test);
                $entityManager->flush();
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            }

            return $this->render('test/addTest.html.twig', [
                'testForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
    #[Route('/delete/test/{id}', name: 'delete_Test')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Test::class)->find($id);

            $entityManager->remove($entity);
            $entityManager->flush();
            $this->addFlash('success', 'La supression a été effectué avec succès.');
            return $this->redirectToRoute('show_test');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);

    }

    #[Route('/modifier/test/{id}', name: 'edit_test')]
    public function ModifierTest(SessionInterface $session,EntityManagerInterface $entityManager,Test $test,Request $request,$id){

        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Test::class)->find($id);
            $form = $this->createForm(TestFormType::class, $test);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){

                $test = $form->getData();
                $entity->setNomTest($form->get('nomTest')->getData());
                $entity->setDuree($form->get('duree')->getData());
                $cours = $entityManager->getRepository(Cours::class)->find($form->get('id_Cours')->getData());
                $entity->setIdCours($cours);
                $entityManager->flush();
                $this->addFlash('success', 'La modification a été effectué avec succès.');
                return $this->redirectToRoute('show_test');
            }
            return $this->render('test/addTest.html.twig', [
                'testForm' => $form->createView(),
            ]);
        }       
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
            
    }
}
