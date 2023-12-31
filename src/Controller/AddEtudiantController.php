<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Personne;
use App\Entity\Etudiant;
use App\Form\PersonneFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AddEtudiantController extends AbstractController
{
    #[Route('/ajouter/etudiant', name: 'app_add_etudiant')]
    public function index(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $session = $request->getSession();
        $session->set('TypeUtilisateur', "etudiant");
        $personne = new Personne();
        $etudiant = new Etudiant();
        $form = $this->createForm(PersonneFormType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($personne);
            $entityManager->flush();
            $id = $personne->getId();
            $filiere = $form->get('filiere')->getData();
            $personne = $entityManager->getRepository(Personne::class)->find($id);
            $etudiant->setIdPersonne($personne);
            $etudiant->setFiliere($filiere);
            $idCat = $form->get('categorie')->getData();
            $categorie = $entityManager->getRepository(Categorie::class)->find($idCat);
            $etudiant->setCategorie($categorie);
            $entityManager->persist($etudiant);
            $entityManager->flush();
            $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
            return $this->redirectToRoute('app_register', ['id' => $id,
            "filiere"=>$filiere,
            "Utilisateur"=>"etudiant",
            "idEtu" => $etudiant->getId(),
        ]);
            
        }

    return $this->render('registration/addEtudiant.html.twig', [
        'PersonneForm' => $form->createView(),
    ]);

    }

  

    #[Route('/etudiants', name: 'show_etudiants')]
    public function AfficherEtudiant(SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles) ){
            $query = $entityManager->createQueryBuilder()
            ->select(' p.id','e.id as id_etudiant ','p.nom', 'p.prenom','p.dateNaiss','p.email','p.telephone', 'e.filiere')
            ->from(Personne::class, 'p')
            ->join(Etudiant::class, 'e', 'WITH', 'e.id_personne = p.id')
            ->getQuery();
            
            $results = $query->getResult();
            
            return $this->render('etudiants/afficherEtudiants.html.twig', [
                'etudiants' => $results,
            ]);
            
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);

    }



    #[Route('/modifier/etudiant/{id}/{id2}', name: 'edit_etudiant')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,Personne $personne,Request $request,$id,$id2){

        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles) || in_array('ETUDIANT',$roles) ){
            $entity = $entityManager->getRepository(Etudiant::class)->find($id2);
            $form = $this->createForm(PersonneFormType::class, $personne);
            $session = $request->getSession();
            $session->set('TypeUtilisateur', "etudiant");
            $form->get('filiere')->setData($entity->getFiliere());
            $form->get('categorie')->setData($entity->getCategorie());
            
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $personne = $form->getData(); 
                $entity2 = $entityManager->getRepository(Personne::class)->find($id);
                $entity2->setNom($form->get('nom')->getData());
                $entity2->setPrenom($form->get('prenom')->getData());
                $entity2->setDateNaiss($form->get('dateNaiss')->getData());
                $entity2->setEmail($form->get('email')->getData());
                $entity2->setTelephone($form->get('telephone')->getData());
                $entity->setFiliere($form->get('filiere')->getData());
                $categorie = $entityManager->getRepository(Categorie::class)->find($form->get('categorie')->getData());
                $entity->setCategorie($categorie);
                $entityManager->flush();

                $this->addFlash('success', 'La modification a été effectué avec succès.');
                return $this->redirectToRoute('show_etudiants');
                // dd($etudiant);
            }
            return $this->render('registration/addEnseignant.html.twig', [
                'PersonneForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
            
    }

    #[Route('/delete/etudiant/{id}', name: 'delete_etudiant')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles) ){
            $entity2 = $entityManager->getRepository(Personne::class)->find($id);
            $entityManager->remove($entity2);
            $entityManager->flush();
            $this->addFlash('success', 'La supression a été effectué avec succès.');
            return $this->redirectToRoute('show_etudiants');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);

    }


}
