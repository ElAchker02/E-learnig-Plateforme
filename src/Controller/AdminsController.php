<?php

namespace App\Controller;

use App\Entity\Enseignant;
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

class AdminsController extends AbstractController
{
    #[Route('/ajouter/administrateur', name: 'add_admins')]
    public function index(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles)){
            $session = $request->getSession();
            $session->set('TypeUtilisateur', "admin");
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
    
            return $this->render('registration/addAdmins.html.twig', [
                'PersonneForm' => $form->createView(),
            ]);
            
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    #[Route('/administrateurs', name: 'show_admins')]
    public function afficherAdmins(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles)  ){
            $query = $entityManager->createQueryBuilder()
            ->select('u.id','p.id as idPersonne','u.roles', 'u.nomUtilisateur','p.nom','p.prenom','u.email' ,'e.specialite')
            ->from(User::class, 'u')
            ->join(Personne::class, 'p', 'WITH', 'u.id_Personne = p.id')
            ->join(Enseignant::class, 'e', 'WITH', 'e.id_personne = p.id')
            ->getQuery();
    
            $results = $query->getResult();

            return $this->render('admins/afficheradmins.html.twig', [
                'enseignants' => $results,
            ]);
            
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
    }

    
    #[Route('/modifier/admin/{id}}', name: 'edit_admin')]
    public function ModifierCours(SessionInterface $session,EntityManagerInterface $entityManager,SluggerInterface $slugger,User $user,Request $request,$id,UserPasswordHasherInterface $userPasswordHasher){
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles)  ){
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
                return $this->redirectToRoute('show_admins');
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
    #[Route('/delete/admin/{id}', name: 'delete_admin')]
    public function delete(SessionInterface $session,EntityManagerInterface $entityManager, $id): Response
    {
        $roles = $session->get('roles');
        if(in_array('SUPER-ADMIN',$roles)  ){
            $entity2 = $entityManager->getRepository(Personne::class)->find($id);
        $entityManager->remove($entity2);
        $entityManager->flush();
        $this->addFlash('success', 'La supression a été effectué avec succès.');
        return $this->redirectToRoute('show_admins');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);
        
    }
}
