<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    #[Route('/Acceuil', name: 'app_home')]
    public function index(Security $security,SessionInterface $session): Response
    {
        $user = $security->getUser();
        
        if ($user) {
            $roles = $user->getRoles();
            $session->set('roles', $roles);
        }
        
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',

            ]);
        } else {
            return $this->render('acceuil/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        
    }
}
