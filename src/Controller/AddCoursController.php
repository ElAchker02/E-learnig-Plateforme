<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Personne;
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
            $successMessage = '';
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
                $successMessage = 'Cours '.$form->get('nomCours')->getData().' ajouté';
                $variables = ['successMessage' => $successMessage, ];
                return $this->redirectToRoute('show_cours',$variables);
                
            }
            
            return $this->render('cours/addCours.html.twig', [
                'CoursForm' => $form->createView(),
                'successMessage' => $successMessage,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/Cours', name: 'show_cours')]
    public function AfficherCours(EntityManagerInterface $entityManager ,SessionInterface $session,Request $request): Response
    {
        $roles = $session->get('roles');
        
        if(in_array('ENSEIGNANT',$roles) || in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $successMessage = $request->query->get('successMessage');
            $query = $entityManager->createQueryBuilder()
            ->select('c.id','c.nomCours', 'c.introduction','c.datePublication','c.nbChapitres','c.estPayant','c.prix', "CONCAT(p.nom, ' ', p.prenom) AS fullName","ca.nomCat")
            ->from(Cours::class, 'c')
            ->join(Enseignant::class, 'e', 'WITH', 'c.id_Enseignant = e.id')
            ->join(Personne::class, 'p', 'WITH', 'e.id_personne = p.id')
            ->join(Categorie::class, 'ca', 'WITH', 'c.id_Categorie = ca.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();
            $results = $query->getResult();

            return $this->render('cours/afficherCours.html.twig', [
                'cours' => $results,
                'successMessage' => $successMessage,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
            
        ]);
    }

    #[Route('/modifier/cours/{id}', name: 'edit_cours')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,Cours $cours,Request $request,$id){
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles)  ){
            $entity = $entityManager->getRepository(Cours::class)->find($id);
            $form = $this->createForm(CoursFormType::class, $cours);
            
            $form->handleRequest($request);
            $successMessage = '';
            if($form->isSubmitted() && $form->isValid()){
                
                $cours = $form->getData();
                
                $entity->setNomCours($form->get('nomCours')->getData());
                $entity->setIntroduction($form->get('introduction')->getData());
                $entity->setEstPayant($form->get('estPayant')->getData());
                $entity->setPrix($form->get('prix')->getData());
                $categorie = $entityManager->getRepository(Categorie::class)->find($form->get('id_categorie_id')->getData());
                $entity->setIdCategorie($categorie);
                 
                $entityManager->flush();
                $successMessage = 'Cours '.$form->get('nomCours')->getData().' Modifié';
                $variables = ['successMessage' => $successMessage, ];
                return $this->redirectToRoute('show_cours',$variables);
                
            }
            return $this->render('cours/addCours.html.twig', [
                'CoursForm' => $form->createView(),
                'successMessage' => $successMessage,
            ]);
        } 
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
           
    }

    #[Route('/delete/cours/{id}', name: 'delete_cours')]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $entity = $entityManager->getRepository(Cours::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();

        // Redirect or display a success message
        return $this->redirectToRoute('show_cours');
    }

    
}
