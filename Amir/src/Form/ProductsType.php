<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Marque;
use App\Entity\Produit;
use App\Entity\SousCategorie;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix',IntegerType::class)
            ->add('image',FileType::class,[
                'mapped' => false
            ])
                ->add('description',CKEditorType::class)
            ->add('taille',ChoiceType::class, [
                'choices' => [
                    'M' => 'M',
                    'S' => 'S',
                    'XS' => 'XS',
                    'L' =>'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                    'XXXL' => 'XXXL',

                ],
            ])
            ->add('nom',TextType::class)
            ->add('marque',EntityType::class , ['class'=>Marque::class , 'choice_label'=>'libelle'])
            ->add('sousCategire',EntityType::class , ['class'=>SousCategorie::class , 'choice_label'=>'libelle']
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
