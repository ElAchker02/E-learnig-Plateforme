<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\Personne;
use App\Form\EnseignantFormType;
use App\Form\PersonneFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use SessionIdInterface;
use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AddEnseignantController extends AbstractController
{
    #[Route('/ajouter/enseignant', name: 'app_add_enseignant')]
    public function index(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles)){
            $session = $request->getSession();
            $session->set('TypeUtilisateur', "enseignant");
            $personne = new Personne();
            $enseignant = new Enseignant();
            $form = $this->createForm(PersonneFormType::class, $personne);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($personne);
                $entityManager->flush();
                $id = $personne->getId();
                $specialite = $form->get('specialite')->getData();
                $personne = $entityManager->getRepository(Personne::class)->find($id);
                $enseignant->setIdPersonne($personne);
                $enseignant->setSpecialite($specialite);
                $entityManager->persist($enseignant);
                $entityManager->flush();
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
                return $this->redirectToRoute('app_register', ['id' => $id,
                "specialite"=>$specialite,
                "idE"=> $enseignant->getId()]);
                
            }
    
            return $this->render('registration/addEnseignant.html.twig', [
                'PersonneForm' => $form->createView(),
            ]);
            
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);


    }

    #[Route('/enseignants', name: 'show_enseignant')]
    public function AfficherEnseignant(SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles) ){
            $query = $entityManager->createQueryBuilder()
            ->select('p.id','e.id AS enseignant_id','p.nom', 'p.prenom','p.dateNaiss','p.email','p.telephone', 'e.specialite')
            ->from(Personne::class, 'p')
            ->join(Enseignant::class, 'e', 'WITH', 'e.id_personne = p.id')
            ->getQuery();
            $results = $query->getResult();

            return $this->render('enseignant/afficherEnseignant.html.twig', [
                'enseignants' => $results,
            ]);
            
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);

    }

    #[Route('/modifier/enseignant/{id}/{id2}', name: 'edit_enseignant')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,Personne $personne,Request $request,$id,$id2){

        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles) ){
            $entity = $entityManager->getRepository(Enseignant::class)->find($id2);
            $form = $this->createForm(PersonneFormType::class, $personne);
            $session = $request->getSession();
            $session->set('TypeUtilisateur', "enseignant");
            $form->get('specialite')->setData($entity->getSpecialite());
            
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $personne = $form->getData(); 
                $entity2 = $entityManager->getRepository(Personne::class)->find($id);
                $entity2->setNom($form->get('nom')->getData());
                $entity2->setPrenom($form->get('prenom')->getData());
                $entity2->setDateNaiss($form->get('dateNaiss')->getData());
                $entity2->setEmail($form->get('email')->getData());
                $entity2->setTelephone($form->get('telephone')->getData());
                $entity->setSpecialite($form->get('specialite')->getData());
                $entityManager->flush();

                $this->addFlash('success', 'La modification a été effectué avec succès.');
            }
            return $this->render('registration/addEnseignant.html.twig', [
                'PersonneForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }
    #[Route('/delete/enseignant/{id}', name: 'delete_enseignant')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles) || in_array('ADMIN',$roles) ){
            $entity2 = $entityManager->getRepository(Personne::class)->find($id);
            $entityManager->remove($entity2);
            $entityManager->flush();
            $this->addFlash('success', 'La supression a été effectué avec succès.');
            return $this->redirectToRoute('show_enseignant');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }
}
