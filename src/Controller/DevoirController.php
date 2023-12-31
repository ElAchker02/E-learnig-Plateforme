<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Devoir;
use App\Entity\Enseignant;
use App\Entity\Personne;
use App\Form\DevoirFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class DevoirController extends AbstractController
{
    #[Route('/add/devoir', name: 'add_devoir')]
    public function index(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles)  ){
            $devoir = new Devoir();
            $form = $this->createForm(DevoirFormType::class, $devoir);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()){
                $currentDate = new \DateTime(); 
                $devoir->setDateDepot($currentDate);

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
                    $devoir->setImages($newFilename);
                }

                $fichier = $form->get('fichier')->getData();
                if ($fichier) {
                    $originalFilename2 = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename2 = $slugger->slug($originalFilename2);
                    $newFilename2 = $safeFilename2.'-'.uniqid().'.'.$fichier->guessExtension();
                    try {
                        $fichier->move(
                            $this->getParameter('devoir_directory'),
                            $newFilename2
                        );
                    } catch (FileException $e) {

                    }
                    $devoir->setFichier($newFilename2);
                }

                $cours = $entityManager->getRepository(Cours::class)->find($form->get('id_Cours')->getData());
                $devoir->setIdCours($cours);

                // $enseignant = $entityManager->getRepository(Enseignant::class)->find($form->get('id_Enseignant')->getData());
                $enseignant = $entityManager->getRepository(Enseignant::class)->find(reset($roles));
                $devoir->setIdEnseignant($enseignant);

                $entityManager->persist($devoir);
                $entityManager->flush();    
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            }
            
            return $this->render('devoir/addDevoir.html.twig', [
                'DevoirForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/devoires', name: 'show_devoires')]
    public function AfficherChapitres(SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){

            $query = $entityManager->createQueryBuilder()
            ->select('co.id as idCours','d.id','d.nomDevoir','d.description','d.dateDepot','d.dateSoumission','d.images','d.fichier','co.nomCours',"CONCAT(p.nom, ' ', p.prenom) AS fullName")
            ->from(Devoir::class, 'd')
            ->join(Cours::class, 'co', 'WITH', 'd.id_Cours = co.id')
            ->join(Enseignant::class, 'e', 'WITH', 'd.id_Enseignant = e.id')
            ->join(Personne::class, 'p', 'WITH', 'e.id_personne = p.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();
    
            $results = $query->getResult(); 
            return $this->render('devoir/afficherDevoir.html.twig', [
                'devoires' => $results,
            ]);
        }
        elseif( in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $query = $entityManager->createQueryBuilder()
            ->select('co.id as idCours','d.id','d.nomDevoir','d.description','d.dateDepot','d.dateSoumission','d.images','d.fichier','co.nomCours',"CONCAT(p.nom, ' ', p.prenom) AS fullName")
            ->from(Devoir::class, 'd')
            ->join(Cours::class, 'co', 'WITH', 'd.id_Cours = co.id')
            ->join(Enseignant::class, 'e', 'WITH', 'd.id_Enseignant = e.id')
            ->join(Personne::class, 'p', 'WITH', 'e.id_personne = p.id')
            ->getQuery();
    
            $results = $query->getResult(); 
            return $this->render('devoir/afficherDevoir.html.twig', [
                'devoires' => $results,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);;
    }
    #[Route('/delete/devoir/{id}', name: 'delete_devoir')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Devoir::class)->find($id);

            $entityManager->remove($entity);
            $entityManager->flush();
            $this->addFlash('success', 'La supression a été effectué avec succès.');
            return $this->redirectToRoute('show_devoires');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }

    #[Route('/modifier/devoir/{id}/{id2}', name: 'edit_devoir')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,Devoir $devoir,Request $request,$id,$id2){

        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){
            $entity = $entityManager->getRepository(Devoir::class)->find($id);
            $entity2 = $entityManager->getRepository(Cours::class)->find($id2);
            $form = $this->createForm(DevoirFormType::class, $devoir);
            $form->get('id_Cours')->setData($entity2);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $devoir = $form->getData();
                $entity->setNomDevoir($form->get('nomDevoir')->getData());
                $entity->setDescription($form->get('description')->getData());
                $entity->setDateSoumission($form->get('dateSoumission')->getData());
                $entity->setImages($form->get('images')->getData());
                $entity->setFichier($form->get('fichier')->getData());
                $cours = $entityManager->getRepository(Cours::class)->find($form->get('id_Cours')->getData());
                $entity->setIdCours($cours);
                $enseignant = $entityManager->getRepository(Enseignant::class)->find(reset($roles));
                $entity->setIdEnseignant($enseignant);
                $entityManager->flush();
                $this->addFlash('success', 'La modification a été effectué avec succès.');
                // $this->FlashMessage->add("success","Cours Modifié");
                return $this->redirectToRoute('show_devoires');
                // dd($etudiant);
            }
            return $this->render('devoir/addDevoir.html.twig', [
                'DevoirForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
           
    }
}
