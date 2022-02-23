<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Marque;
use App\Entity\Produit;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix',IntegerType::class)
            ->add('image',TextType::class)
            ->add('quantite',IntegerType::class)
            ->add('taille',TextType::class)
            ->add('nom',TextType::class)
            ->add('marque',EntityType::class , ['class'=>Marque::class , 'choice_label'=>'libelle'])
            ->add('Categorie',EntityType::class , ['class'=>Categorie::class , 'choice_label'=>'libelle'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
