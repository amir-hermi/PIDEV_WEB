<?php

namespace App\Form;

use App\Entity\Commandestock;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use App\Repository\CommandestockRepository;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Query\Expr\Select;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandestockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class)


            ->add('fournisseur',EntityType::class,['class'=>Fournisseur::class,'choice_label'=>'nom_fournisseur'])
            ->add('produit',EntityType::class,[
                'class'=>Produit::class,
                'choice_label'=>'nom',
                'multiple'=>true,
                'mapped'=>false,
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commandestock::class,
        ]);
    }
}
