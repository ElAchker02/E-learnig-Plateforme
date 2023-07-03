<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\EventListener\UnrelatedFieldListener;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;

class PersonneFormType extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $utilisateur = $session->get('TypeUtilisateur');
        if($utilisateur == "etudiant"){
            $builder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('dateNaiss',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('email',TextType::class)
            ->add('telephone',TextType::class)
            ->add('filiere', TextType::class, [
                'mapped' => false,
            ])
            ->add('Valider', SubmitType::class)
            ;
        }
        else{
            $builder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('dateNaiss',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('email',TextType::class)
            ->add('telephone',TextType::class)
            ->add('specialite', TextType::class, [
                'mapped' => false,
            ])
            ->add('Valider', SubmitType::class)
            ;
        }
       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);

    }
}
