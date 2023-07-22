<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Cours;
use App\Entity\Enseignant;
use App\Entity\Etudiant;
use App\Entity\EtudiantCoursPayant;
use App\Entity\Personne;
use App\Entity\Progression;
use App\Entity\User;
use App\Form\CoursFormType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AddCoursController extends AbstractController
{

    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
        
    }
    #[Route('/add/cours', name: 'app_add_cours')]
    public function index(SessionInterface $session,Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
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
                $image = $form->get('image')->getData();
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('image_directory4'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $cours->setImage($newFilename);
                }
                $entityManager->persist($cours);
                $entityManager->flush();    
                $this->addFlash('success', 'L\'ajout a été effectué avec succès.');
                // return $this->redirectToRoute('show_cours');
                
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
    public function AfficherCours(EntityManagerInterface $entityManager ,SessionInterface $session,Request $request): Response
    {
        $roles = $session->get('roles');
        
        if(in_array('ENSEIGNANT',$roles)){
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
                
            ]);
        }
        elseif( in_array('SUPER-ADMIN',$roles ) || in_array('ADMIN',$roles)){
            $query = $entityManager->createQueryBuilder()
            ->select('c.id','c.nomCours', 'c.introduction','c.datePublication','c.nbChapitres','c.estPayant','c.prix', "CONCAT(p.nom, ' ', p.prenom) AS fullName","ca.nomCat")
            ->from(Cours::class, 'c')
            ->join(Enseignant::class, 'e', 'WITH', 'c.id_Enseignant = e.id')
            ->join(Personne::class, 'p', 'WITH', 'e.id_personne = p.id')
            ->join(Categorie::class, 'ca', 'WITH', 'c.id_Categorie = ca.id')
            ->getQuery();
            $results = $query->getResult();

            return $this->render('cours/afficherCours.html.twig', [
                'cours' => $results,
              
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
            
        ]);
    }

    #[Route('/modifier/cours/{id}', name: 'edit_cours')]
    public function ModifierCours(SluggerInterface $slugger,SessionInterface $session,EntityManagerInterface $entityManager,Cours $cours,Request $request,$id){
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles)  ){
            $entity = $entityManager->getRepository(Cours::class)->find($id);
            $form = $this->createForm(CoursFormType::class, $cours);
            $form->get('id_categorie_id')->setData($entity->getIdCategorie());
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $cours = $form->getData();
                
                $entity->setNomCours($form->get('nomCours')->getData());
                $entity->setIntroduction($form->get('introduction')->getData());
                $entity->setEstPayant($form->get('estPayant')->getData());
                $entity->setPrix($form->get('prix')->getData());
                $categorie = $entityManager->getRepository(Categorie::class)->find($form->get('id_categorie_id')->getData());
                $entity->setIdCategorie($categorie);

                $image = $form->get('image')->getData();
                if ($image!=null) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('image_directory4'),
                            $newFilename
                        );
                    } catch (FileException $e) {

                    }
                    $cours->setImage($newFilename);
                }
                $entityManager->flush();
                $this->addFlash('success', 'La modification a été effectué avec succès.');
                return $this->redirectToRoute('show_cours');
                
            }
            return $this->render('cours/addCours.html.twig', [
                'CoursForm' => $form->createView(),
            ]);
        } 
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }

    #[Route('/delete/cours/{id}', name: 'delete_cours')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles)  ){
            $entity = $entityManager->getRepository(Cours::class)->find($id);

        $entityManager->remove($entity);
        $entityManager->flush();
        $this->addFlash('success', 'La supression a été effectué avec succès.');
        return $this->redirectToRoute('show_cours');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }

    #[Route('/Cours2', name: 'show_cours2')]
    public function AfficherCoursClient(EntityManagerInterface $entityManager ,SessionInterface $session,Request $request): Response
    {
        $roles = $session->get('roles');
        
        if(in_array('ETUDIANT',$roles)){
            
            $query = $entityManager->createQueryBuilder()
            ->select('c.id','c.nomCours', 'c.introduction','c.datePublication','c.nbChapitres','c.estPayant','c.prix',"ca.nomCat","c.image")
            ->from(Cours::class, 'c')
            ->join(Categorie::class, 'ca', 'WITH', 'c.id_Categorie = ca.id')
            ->getQuery();
            $results = $query->getResult();

            return $this->render('cours/afficherCourClient.html.twig', [
                'cours' => $results,
            ]);
        }
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
            
        ]);
    }

    #[Route('/Cours/{id}', name: 'detail_cours')]
    public function AfficherDetailCours(EntityManagerInterface $entityManager ,SessionInterface $session,Request $request,$id): Response
    {
        $roles = $session->get('roles');
        
        if(in_array('ETUDIANT',$roles)){
            
            $query = $entityManager->createQueryBuilder()
            ->select('c.id','c.nomCours', 'c.introduction','c.datePublication','c.nbChapitres','c.estPayant','c.prix', "CONCAT(p.nom, ' ', p.prenom) AS fullName","ca.nomCat")
            ->from(Cours::class, 'c')
            ->join(Enseignant::class, 'e', 'WITH', 'c.id_Enseignant = e.id')
            ->join(Personne::class, 'p', 'WITH', 'e.id_personne = p.id')
            ->join(Categorie::class, 'ca', 'WITH', 'c.id_Categorie = ca.id')
            ->where('c.id = :idCours') 
            ->setParameter('idCours', $id)
            ->getQuery();
            $results = $query->getResult();

            return $this->render('cours/detailsCours.html.twig', [
                'cours' => $results,
                'idEtu'=> reset($roles),
            ]);
        }
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
            
        ]);
    }

    #[Route('/suivre/cours/{id1}/{id2}', name: 'suivre_cours')]
    public function SuivreCours($id1,$id2,EntityManagerInterface $entityManager ,SessionInterface $session,Request $request): Response
    {
        $roles = $session->get('roles');
        
        if(in_array('ETUDIANT',$roles)){

            $query = $entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Progression::class, 'p')
            ->where('p.id_Cours = :idCours') 
            ->andWhere('p.id_Etudiant = :idEtudiant')
            ->setParameter('idCours', $id1)
            ->setParameter('idEtudiant', $id2)
            ->getQuery();

        $nombreDeProgressions = $query->getSingleScalarResult();
        if($nombreDeProgressions == 0){
            $progression = new Progression();
            $etudiant = $entityManager->getRepository(Etudiant::class)->find($id2);
            $progression->setIdEtudiant($etudiant);
            $cours = $entityManager->getRepository(Cours::class)->find($id1);
            $progression->setIdCours($cours);
            $progression->setDone(0);
            $progression->setChapCourant(0);
            $entityManager->persist($progression);
            $entityManager->flush();
        }
                
        }
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
            
        ]);
    }
    #[Route('/unfollow/cours/{id1}/{id2}', name: 'unfollow_cours')]
    public function UnfollowCours(EntityManagerInterface $entityManager ,SessionInterface $session,Request $request,$id1,$id2): Response
    {
        $roles = $session->get('roles');
        
        if(in_array('ETUDIANT',$roles)){
            $progressionRepository = $entityManager->getRepository(Progression::class);
            $progression = $progressionRepository->findOneBy(['id_Cours' => $id1, 'id_Etudiant' => $id2]);
            if ($progression) {
                $entityManager->remove($progression);
                $entityManager->flush();
            } 
        }
        
        return $this->redirectToRoute('show_cours2');
    }
}
