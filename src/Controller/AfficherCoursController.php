<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AfficherCoursController extends AbstractController
{
    #[Route('/afficher/cours', name: 'app_afficher_cours')]
    public function index(): Response
    {
        return $this->render('cours/afficherCours.html.twig', [
            'controller_name' => 'AfficherCoursController',
        ]);
    }
}
