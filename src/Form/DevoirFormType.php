<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Devoir;
use App\Entity\Enseignant;
use App\Entity\Personne;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevoirFormType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $choices = $this->getEnseignantChoices();ss
        $builder
            ->add('nomDevoir')
            ->add('description',TextareaType::class)
            // ->add('dateDepot')
            ->add('dateSoumission',DateType::class)
            ->add('images', FileType::class, [
                'mapped' => false,
                'label' => 'Image',
                'required' => false, 
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Uploader une image valid (JPEG ou PNG)',
                    ])
                ],
            ])
            ->add('fichier',FileType::class,[
                'required' => false,
                'mapped' => false,
            ])
            ->add('id_Cours', EntityType::class, [
                'mapped' => false,
                'class'=> Cours::class,
                'choice_value' => 'id',
                'choice_label' => 'nom_cours',
            ])
            // ->add('id_Enseignant', ChoiceType::class, [
            //     'choices' => $choices,
            //     'label' => 'Enseignant',
            // ])
            // ->add('id_Enseignant', EntityType::class, [
            //     'class' => Enseignant::class,
            //     'choice_label' => function ($enseignant) {
            //         return $enseignant->getIdPersonne()->getNom() . ' ' . $enseignant->getIdPersonne()->getPrenom();
            //     },
            //     'choice_value' => 'id', 
            // ])
            ->add('Valider',SubmitType::class)
        ;
    }

    // private function getEnseignantChoices()
    // {
    //     $query = $this->entityManager->createQueryBuilder()
    //     ->select("CONCAT(p.nom, ' ', p.prenom) AS fullName",'e.id AS idE')
    //     ->from(Personne::class, 'p')
    //     ->join(Enseignant::class, 'e', 'WITH', 'e.id_personne = p.id')
    //     ->getQuery();

    //     $results = $query->getResult();
    //     $choices = [];
    //     foreach ($results as $enseignant) {
    //         $choices[$enseignant['fullName']] = $enseignant['idE'];
    //     }
    //     return $choices;
    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devoir::class,
        ]);
    }
}
