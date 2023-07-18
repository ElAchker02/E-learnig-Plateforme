<?php

namespace App\Form;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Entity\Partie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PartieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description',TextareaType::class,['label' =>'Description',])
            ->add('images',FileType::class,[
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
                'label' =>'image',

            ])
            ->add('info',TextareaType::class,['required' => false, 'label' =>'Information'])
            ->add('avertissement',TextareaType::class,['required' => false, 'label' =>'Avertissement' ])
            ->add('id_Chapitre', EntityType::class, [
                'mapped' => false,
                'class'=> Chapitre::class,
                'choice_value' => 'id',
                'choice_label' => 'nom_chap',
                'label' =>'Chapitre'
            ])
            ->add('id_cours', EntityType::class, [
                'mapped' => false,
                'class'=> Cours::class,
                'choice_value' => 'id',
                'choice_label' => 'nom_cours',
                'label' =>'Cours'
            ])
            ->add('Valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partie::class,
        ]);
    }
}
