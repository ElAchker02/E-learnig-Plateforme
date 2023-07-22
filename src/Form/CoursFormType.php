<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;


class CoursFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCours',TextType::class)
            ->add('introduction',TextareaType::class)
            // ->add('datePublication',DateType::class)
            // ->add('nbChapitres',TextType::class)
            ->add('estPayant', ChoiceType::class, [
                'choices' => [
                    'Payant' => 1,
                    'Gratuit' => 0,
                ],
                'expanded' => true,
            ])
            ->add('prix',NumberType::class)
            ->add('id_categorie_id', EntityType::class, [
                'mapped' => false,
                'class'=> Categorie::class,
                'choice_value' => 'id',
                'choice_label' => 'nom_cat',
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false, 
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Uploader une image valid (JPEG ou PNG)',
                    ])
                ],
            ])
            ->add('Valider', SubmitType::class)
            // ->add('id_Enseignant')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
