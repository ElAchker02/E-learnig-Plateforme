<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevoirController extends AbstractController
{
    #[Route('/devoir', name: 'app_devoir')]
    public function index(): Response
    {
        return $this->render('devoir/index.html.twig', [
            'controller_name' => 'DevoirController',
        ]);
    }
}
