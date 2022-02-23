<?php

namespace App\Form;

use App\Entity\Livreur;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse')
            ->add('date')
            ->add("livreur",EntityType::class, ['class'=>Livreur::class , 'choice_label' => 'nom'])
            ->add('ajouter',SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-sm btn-primary hvr-ripple-out mb-3'],
                    'label' => 'Ajouter'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}