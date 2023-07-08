<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Cours;
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
        if(in_array('ENSEIGNANT',$roles) || in_array('SUPER-ADMIN',$roles )){

            $query = $entityManager->createQueryBuilder()
            ->select('ch.id','ch.nomChap', 'ch.description','ch.video','ch.documents','co.nomCours')
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
    public function ModifierCours(EntityManagerInterface $entityManager,Chapitre $chapitre,Request $request,$id){

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

                // $this->FlashMessage->add("success","Cours Modifié");
                return $this->redirectToRoute('app_chapitre');
                // dd($etudiant);
            }
            return $this->render('chapitre/addChapitre.html.twig', [
                'ChapitreForm' => $form->createView(),
            ]);
    }
}
