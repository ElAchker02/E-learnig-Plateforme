<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Partie;
use App\Form\PartieFormType;
use App\Repository\PartieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PartieController extends AbstractController
{
    #[Route('/parties', name: 'app_partie')]
    public function afficherPartie(SessionInterface $session,PartieRepository $partieRepository,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) || in_array('SUPER-ADMIN',$roles)){

            $query = $entityManager->createQueryBuilder()
            ->select('p.id','co.id as idCours','p.description', 'p.images','p.info','p.avertissement','co.nomCours','ch.nomChap','co.nomCours')
            ->from(Partie::class, 'p')
            ->join(Chapitre::class, 'ch', 'WITH', 'ch.id = p.id_Chapitre')
            ->join(Cours::class, 'co', 'WITH', 'co.id = p.id_cours')
            ->getQuery();
            $results = $query->getResult(); 
            return $this->render('partie/afficherPartie.html.twig', [
                'parties' => $results,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);;
    }

    #[Route('/ajouter/partie', name: 'add_partie')]
    public function AddChapitre(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $partie = new Partie();
            $form = $this->createForm(PartieFormType::class, $partie);
            $form->handleRequest($request);

            
            if ($form->isSubmitted() && $form->isValid()){

                $idCours = $form->get('id_cours')->getData();
                $cours = $entityManager->getRepository(Cours::class)->find($idCours);
                $partie->setIdCours($cours);
                $idChap = $form->get('id_Chapitre')->getData();
                $chapitre = $entityManager->getRepository(Chapitre::class)->find($idChap);
                $partie->setIdChapitre($chapitre);

                $image = $form->get('images')->getData();
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('image_directory2'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $partie->setImages($newFilename);
                }

                $entityManager->persist($partie);
                $entityManager->flush();
            }
            
            return $this->render('partie/addPartie.html.twig', [
                'PartieForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/modifier/partie/{id}', name: 'edit_partie')]
    public function ModifierCours(EntityManagerInterface $entityManager,Partie $partie,Request $request,$id){

            $entity = $entityManager->getRepository(Partie::class)->find($id);
            $form = $this->createForm(PartieFormType::class, $partie);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $partie = $form->getData();
                $entity->setDescription($form->get('description')->getData());
                // $entity->setImages($form->get('images')->getData());
                $entity->setInfo($form->get('info')->getData());
                $entity->getAvertissement($form->get('avertissement')->getData());
                $chapitre = $entityManager->getRepository(Chapitre::class)->find($form->get('id_Chapitre')->getData());
                $entity->setIdChapitre($chapitre);
                $cours = $entityManager->getRepository(Cours::class)->find($form->get('id_cours')->getData());
                $entity->setIdCours($cours);
                $entityManager->flush();

                // $this->FlashMessage->add("success","Cours Modifié");
                return $this->redirectToRoute('app_partie');
                // dd($etudiant);
            }
            return $this->render('partie/addPartie.html.twig', [
                'PartieForm' => $form->createView(),
            ]);
    }
    
    #[Route('/delete/partie/{id}', name: 'delete_partie')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $entity = $entityManager->getRepository(Partie::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();
        return $this->redirectToRoute('app_partie');
    }

    #[Route('/ajouter/partie/{id}/{id2}', name: 'add_partie2')]
    public function AddChapitre2($id,$id2,SessionInterface $session,Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $chapitre = $entityManager->getRepository(Chapitre::class)->find($id);
            $cours = $entityManager->getRepository(Cours::class)->find($id2);
            $partie = new Partie();
            $form = $this->createForm(PartieFormType::class, $partie);
            $form->remove('id_cours');
            $form->remove('id_Chapitre');
            $form->handleRequest($request);

            
            if ($form->isSubmitted() && $form->isValid()){

                // $idCours = $form->get('id_cours')->getData();
                // $cours = $entityManager->getRepository(Cours::class)->find($idCours);
                $partie->setIdCours($cours);
                // $idChap = $form->get('id_Chapitre')->getData();
                // $chapitre = $entityManager->getRepository(Chapitre::class)->find($idChap);
                $partie->setIdChapitre($chapitre);

                $image = $form->get('images')->getData();
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('image_directory2'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $partie->setImages($newFilename);
                }

                $entityManager->persist($partie);
                $entityManager->flush();
            }
            
            return $this->render('partie/addPartie.html.twig', [
                'PartieForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
}
