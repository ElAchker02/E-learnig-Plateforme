<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Test;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomTest')
            ->add('duree')
            ->add('id_Cours', EntityType::class, [
                'mapped' => false,
                'class'=> Cours::class,
                'choice_value' => 'id',
                'choice_label' => 'nom_cours',
            ])
            ->add("Valider",SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}
