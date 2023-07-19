<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Partie;
use App\Form\ChapitreFormType;
use App\Repository\ChapitreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ChapitreController extends AbstractController
{
    #[Route('/chapitres', name: 'app_chapitre')]
    public function AfficherChapitres(ChapitreRepository $chapitreRepository,SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles)){

            $query = $entityManager->createQueryBuilder()
            ->select('ch.id','co.id as idCours','ch.nomChap', 'ch.description','ch.video','ch.documents','co.nomCours')
            ->from(Chapitre::class, 'ch')
            ->join(Cours::class, 'co', 'WITH', 'ch.id_Cours = co.id')
            ->join(Enseignant::class, 'e', 'WITH', 'co.id_Enseignant = e.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();
    
            $results = $query->getResult(); 
            return $this->render('chapitre/afficherChapitre.html.twig', [
                'chapitres' => $results,
            ]);
        }
        elseif( in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $query = $entityManager->createQueryBuilder()
            ->select('ch.id','co.id as idCours','ch.nomChap', 'ch.description','ch.video','ch.documents','co.nomCours')
            ->from(Chapitre::class, 'ch')
            ->join(Cours::class, 'co', 'WITH', 'ch.id_Cours = co.id')
            ->getQuery();
    
            $results = $query->getResult(); 
            return $this->render('chapitre/afficherChapitre.html.twig', [
                'chapitres' => $results,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);;
    }

    #[Route('/ajouter/chapitre', name: 'add_chapitre')]
    public function AddChapitre(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $chapitre = new Chapitre();
            $form = $this->createForm(ChapitreFormType::class, $chapitre);
            $form->handleRequest($request);

            
            if ($form->isSubmitted() && $form->isValid()){

                $idCours = $form->get('id_Cours')->getData();
                $cours = $entityManager->getRepository(Cours::class)->find($idCours);
                $chapitre->setIdCours($cours);

                $video = $form->get('video')->getData();
                if ($video) {
                    $originalFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$video->guessExtension();
                    try {
                        $video->move(
                            $this->getParameter('video_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $chapitre->setVideo($newFilename);
                }

                $pdf = $form->get('documents')->getData();
                if ($pdf) {
                    $originalFilename = pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pdf->guessExtension();
                    try {
                        $pdf->move(
                            $this->getParameter('documents_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $chapitre->setDocuments($newFilename);
                }



                $entityManager->persist($chapitre);
                $entityManager->flush();
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            }
            
            return $this->render('chapitre/addChapitre.html.twig', [
                'ChapitreForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
    #[Route('/modifier/chapitre/{id}', name: 'edit_chapitre')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,Chapitre $chapitre,Request $request,$id){
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){

        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
            $entity = $entityManager->getRepository(Chapitre::class)->find($id);
            $form = $this->createForm(ChapitreFormType::class, $chapitre);
            
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $chapitre = $form->getData();
                $entity->setNomChap($form->get('nomChap')->getData());
                $entity->setDescription($form->get('description')->getData());
                $entity->setVideo($form->get('video')->getData());
                $entity->setDocuments($form->get('documents')->getData());
                $cours = $entityManager->getRepository(Cours::class)->find($form->get('id_Cours')->getData());
                $entity->setIdCours($cours);
                $entityManager->flush();
                $this->addFlash('success', 'La modification a été effectué avec succès.');

                return $this->redirectToRoute('app_chapitre');
                // dd($etudiant);
            }
            return $this->render('chapitre/addChapitre.html.twig', [
                'ChapitreForm' => $form->createView(),
            ]);
    }

    #[Route('/delete/chapitre/{id}', name: 'delete_chapitre')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Chapitre::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();
        $this->addFlash('success', 'La supression a été effectué avec succès.');
        return $this->redirectToRoute('app_chapitre');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }

    #[Route('/ajouter/chapitre/{id}', name: 'add_chapitre2')]
    public function AddChapitre2($id,SessionInterface $session,Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Cours::class)->find($id);
            $chapitre = new Chapitre();
            $form = $this->createForm(ChapitreFormType::class, $chapitre);
            $form->remove('id_Cours');
            $form->handleRequest($request);

            
            if ($form->isSubmitted() && $form->isValid()){
                $chapitre->setIdCours($entity);

                $video = $form->get('video')->getData();
                if ($video) {
                    $originalFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$video->guessExtension();
                    try {
                        $video->move(
                            $this->getParameter('video_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $chapitre->setVideo($newFilename);
                }

                $pdf = $form->get('documents')->getData();
                if ($pdf) {
                    $originalFilename = pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pdf->guessExtension();
                    try {
                        $pdf->move(
                            $this->getParameter('documents_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $chapitre->setDocuments($newFilename);
                }



                $entityManager->persist($chapitre);
                $entityManager->flush();
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            }
            
            return $this->render('chapitre/addChapitre.html.twig', [
                'ChapitreForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
}
