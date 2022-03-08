<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Livreur;
use App\Entity\Mission;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityRepository;
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
            ->add("utilisateur",EntityType::class, ['class'=>Utilisateur::class , 'choice_label' => 'username','query_builder' => function (EntityRepository $er){
                return $er->createQueryBuilder('utilisateur')
                    ->where('utilisateur.roles like :v')
                    ->setParameter('v', '%'.'ROLE_LIVREUR'.'%');
            }])
            ->add('commandes', EntityType::class, [
                // looks for choices from this entity
                'class' => Commande::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'reference',

                // used to render a select box, check boxes or radios
                 'multiple' => true,
                 'expanded' => true,
            ])
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
