<?php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\Personne;
use App\Form\PersonneFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use SessionIdInterface;
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
            ->select('p.nom', 'p.prenom','p.dateNaiss','p.email','p.telephone', 'e.specialite')
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
}
