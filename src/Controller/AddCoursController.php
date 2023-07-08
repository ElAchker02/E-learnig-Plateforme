<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\User;
use App\Form\CoursFormType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class AddCoursController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/add/cours', name: 'app_add_cours')]
    public function index(SessionInterface $session,Request $request,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles)  ){
            $cours = new Cours();
            $form = $this->createForm(CoursFormType::class, $cours);
            $form->handleRequest($request);

            
            if ($form->isSubmitted() && $form->isValid()){
                $currentDate = new \DateTime(); 
                $cours->setDatePublication($currentDate);
                $cours->setNbChapitres(0);
                $idCat = $form->get('id_categorie_id')->getData();
                $categorie = $entityManager->getRepository(Categorie::class)->find($idCat);
                $cours->setIdCategorie($categorie);
                $user = $this->getUser();
                $enseignant = $entityManager->getRepository(Enseignant::class)->find(reset($roles));
                $cours->setIdEnseignant($enseignant);
                $entityManager->persist($cours);
                $entityManager->flush();
            }
            
            return $this->render('cours/addCours.html.twig', [
                'CoursForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/Cours', name: 'show_cours')]
    public function AfficherCours(CoursRepository $coursRepository,SessionInterface $session,Request $request): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) || in_array('SUPER-ADMIN',$roles )){

            $data = $coursRepository->findAll(); 

            return $this->render('cours/afficherCours.html.twig', [
                'cours' => $data,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/modifier/cours/{id}', name: 'edit_cours')]
    public function ModifierCours(EntityManagerInterface $entityManager,Cours $cours,Request $request,$id){
            $entity = $entityManager->getRepository(Cours::class)->find($id);
            $form = $this->createForm(CoursFormType::class, $cours);
            
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $cours = $form->getData();
                
                $entity->setNomCours($form->get('nomCours')->getData());
                $entity->setIntroduction($form->get('introduction')->getData());
                $entity->setEstPayant($form->get('estPayant')->getData());
                $entity->setPrix($form->get('prix')->getData());
                $categorie = $entityManager->getRepository(Categorie::class)->find($form->get('id_categorie_id')->getData());
                $entity->setIdCategorie($categorie);
                 
                $entityManager->flush();
                // $this->FlashMessage->add("success","Cours Modifié");
                return $this->redirectToRoute('show_cours');
                // dd($etudiant);
            }
            return $this->render('cours/addCours.html.twig', [
                'CoursForm' => $form->createView(),
            ]);
    }

    #[Route('/delete/{id}', name: 'delete_cours')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $entity = $entityManager->getRepository(Cours::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();

        // Redirect or display a success message
        return $this->redirectToRoute('show_cours');
    }
}
