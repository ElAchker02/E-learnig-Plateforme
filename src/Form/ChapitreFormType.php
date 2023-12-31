<?php

namespace App\Form;

use App\Entity\Chapitre;
use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ChapitreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomChap',TextType::class,['label' => 'Nom du chapitre',])
            ->add('description',TextareaType::class,['label' => 'Description',])
            ->add('video',FileType::class,[
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        // 'maxSize' => '500M',
                        'mimeTypes' => [
                            'video/mp4',
                            'video/mpeg',
                            'video/quicktime',
                        ],
                        'mimeTypesMessage' => 'Veuillez sélectionner un fichier vidéo valide.',
                    ]),
                ],
                'label' => 'Video',
            ])
            ->add('documents',FileType::class,[
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        // 'maxSize' => '500M',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez sélectionner un fichier PDF valide.',
                    ]),
                ],
                'label' => 'Document',
            ])
            // ->add('ord')
            ->add('id_Cours', EntityType::class, [
                'mapped' => false,
                'class'=> Cours::class,
                'choice_value' => 'id',
                'choice_label' => 'nom_cours',
                'label' => 'Cours ',
            ])
            ->add('Valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapitre::class,
        ]);
    }
}
