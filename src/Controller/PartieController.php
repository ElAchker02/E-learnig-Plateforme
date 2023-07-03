<?php

namespace App\Controller;

use App\Repository\PartieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PartieController extends AbstractController
{
    #[Route('/partie', name: 'app_partie')]
    public function afficherPartie(PartieRepository $partieRepository,SessionInterface $session): Response
    {
        $roles = $session->get('roles');
        if(in_array('ENSEIGNANT',$roles) ){

            $data = $partieRepository->findAll(); 

            return $this->render('partie/afficherPartie.html.twig', [
                'partie' => $data,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AddCoursController',
        ]);;
    }
}
