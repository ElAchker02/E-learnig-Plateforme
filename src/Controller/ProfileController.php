<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Enseignant;
use App\Entity\Etudiant;
use App\Entity\Personne;
use App\Entity\User;
use App\Form\PersonneFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function DetailEtudiant(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $roles = $session->get('roles');
        if( in_array('ETUDIANT',$roles) ){
            $query = $entityManager->createQueryBuilder()
            ->select(' p.id','u.id as userId','e.id as id_etudiant ','p.nom', 'p.prenom','p.dateNaiss','p.email','p.telephone', 'e.filiere')
            ->from(Personne::class, 'p')
            ->join(Etudiant::class, 'e', 'WITH', 'e.id_personne = p.id')
            ->join(User::class, 'u', 'WITH', 'u.id_Personne = p.id')
            ->Where('e.id = :idEtudiant')
            ->setParameter('idEtudiant', reset($roles))
            ->getQuery();
            
            $results = $query->getResult();
            
            return $this->render('profile/profile.html.twig', [
                'etudiants' => $results,
            ]);
            
        }
        if( in_array('ENSEIGNANT',$roles) ){
            $query2 = $entityManager->createQueryBuilder()
            ->select('p.id','u.id as userId','e.id AS enseignant_id','p.nom', 'p.prenom','p.dateNaiss','p.email','p.telephone', 'e.specialite')
            ->from(Personne::class, 'p')
            ->join(Enseignant::class, 'e', 'WITH', 'e.id_personne = p.id')
            ->join(User::class, 'u', 'WITH', 'u.id_Personne = p.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();
            $results2 = $query2->getResult();
            
            return $this->render('profile/profile.html.twig', [
                'enseignants' => $results2,
            ]);
            
        }
        if(in_array('ADMIN',$roles)  ){ 
            $query3 = $entityManager->createQueryBuilder()
            ->select('p.id','u.id as userId','e.id AS enseignant_id','p.id as idPersonne','u.roles', 'u.nomUtilisateur','p.dateNaiss','p.nom','p.prenom','u.email' ,'e.specialite','p.telephone')
            ->from(User::class, 'u')
            ->join(Personne::class, 'p', 'WITH', 'u.id_Personne = p.id')
            ->join(Enseignant::class, 'e', 'WITH', 'e.id_personne = p.id')
            ->where('e.id = :idEnseignant') 
            ->setParameter('idEnseignant', reset($roles))
            ->getQuery();   
            $results3 = $query3->getResult();

            return $this->render('profile/profile.html.twig', [
                'admins' => $results3,
            ]);
            
        }
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);


        
    }
    #[Route('/modifier/profile/etudiant/{id}/{id2}', name: 'edit_profile_etudiant')]
    public function ModifierEtudiant(SessionInterface $session,EntityManagerInterface $entityManager,Personne $personne,Request $request,$id,$id2){

        $roles = $session->get('roles');
        if( in_array('ETUDIANT',$roles) ){
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
                return $this->redirectToRoute('profile');
            }
            return $this->render('registration/addEnseignant.html.twig', [
                'PersonneForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
            
    }
    
    #[Route('/modifier/profile/enseignant/{id}/{id2}', name: 'edit_profile_enseignant')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,Personne $personne,Request $request,$id,$id2){

        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) || in_array('ADMIN',$roles) ) {
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
                return $this->redirectToRoute('profile');
            }
            return $this->render('registration/addEnseignant.html.twig', [
                'PersonneForm' => $form->createView(),
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/modifier/Utilisateur/{id}}', name: 'edit_user')]
        public function ModifierUtilisateur(SessionInterface $session,EntityManagerInterface $entityManager,SluggerInterface $slugger,User $user,Request $request,$id,UserPasswordHasherInterface $userPasswordHasher){
            $roles = $session->get('roles');
            if(in_array('ADMIN',$roles)  || in_array('ENSEIGNANT',$roles) || in_array('ETUDIANT',$roles)){
                $entity = $entityManager->getRepository(User::class)->find($id);
                $form = $this->createForm(RegistrationFormType::class, $user);
                $session = $request->getSession();
                $session->set('TypeUtilisateur', "enseignant");
                $form->get('NomUtilisateur')->setData($entity->getNomUtilisateur());
                $form->get('email')->setData($entity->getEmail());
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    
                    $user = $form->getData(); 
                    $entity->setEmail($form->get('email')->getData());
                    if($form->get('plainPassword')->getData() != null){
                        $entity->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            )
                        );
                    }   
                    if($form->get('image')->getData() != null){
                        $image = $form->get('image')->getData();
                        if ($image) {
                            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                            try {
                                $image->move(
                                    $this->getParameter('image_directory'),
                                    $newFilename);
                            } catch (FileException $e) {

                            }
                            $entity->setImage($newFilename);
                        }
                    }  
                    $entity->setNomUtilisateur($form->get('NomUtilisateur')->getData());

                    $entityManager->flush();

                    $this->addFlash('success', 'La modification a été effectué avec succès.');
                    return $this->redirectToRoute('profile');
                    // dd($etudiant);
                }
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
            return $this->render('home/index.html.twig', [
                'controller_name' => 'AddCoursController',
            ]);
                
        }

}
