<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Personne;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class RegistrationController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    #[Route('/register/{id}', name: 'app_register')]
    public function register($id,Request $request, UserPasswordHasherInterface $userPasswordHasher,SluggerInterface $slugger,UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $idE = $request->query->get('idE');
        $idEtu = $request->query->get('idEtu');
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
                       /** @var UploadedFile $brochureFile */
                    $image = $form->get('image')->getData();
                    if ($image) {
                        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                        try {
                            $image->move(
                                $this->getParameter('image_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {

                        }
                        $user->setImage($newFilename);
                    }
                    $request = $this->requestStack->getCurrentRequest();
                    $session = $request->getSession();
            
                    $utilisateur = $session->get('TypeUtilisateur');
                    if($utilisateur == "etudiant"){
                        $user->setRoles(array($idEtu,"ETUDIANT"));    
                    }
                    elseif($utilisateur == "enseignant"){
                        
                        $user->setRoles(array($idE,"ENSEIGNANT"));
                     
                    }
                    elseif($utilisateur == "admin"){
                        
                        $user->setRoles(array("ADMIN"));
                     
                    }
                    $personne = $entityManager->getRepository(Personne::class)->find($id);
                    $user->setIdPersonne($personne);
                   
            $entityManager->persist($user);
            $entityManager->flush();
        

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator, 
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            
        ]);
    }
}
