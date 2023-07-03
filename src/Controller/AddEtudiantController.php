<?php

namespace App\Controller;

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
            $entityManager->persist($etudiant);
            $entityManager->flush();
            return $this->redirectToRoute('app_register', ['id' => $id,
            "filiere"=>$filiere,
            "Utilisateur"=>"etudiant"]);
            
        }

        return $this->render('registration/addEtudiant.html.twig', [
            'PersonneForm' => $form->createView(),
        ]);

    }
}
