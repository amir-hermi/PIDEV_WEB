<?php

namespace App\Form;

use App\Entity\CategorieReclamation;
use App\Entity\Client;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("categorie_reclamation",EntityType::class, ['class'=>CategorieReclamation::class, 'choice_label' => 'type'])
            ->add('ajouter',SubmitType::class,
                [
                    'attr' => ['class' => 'btn btn-sm btn-warning hvr-ripple-out mb-3'],
                    'label' => 'Ajouter'
                ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
