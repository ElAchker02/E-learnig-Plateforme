<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\Personne;
use App\Entity\User;
use App\Form\PersonneFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

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
            ->select('u.roles', 'u.nomUtilisateur','p.nom','p.prenom','p.dateNaiss','p.email','p.telephone', 'e.specialite')
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
}
