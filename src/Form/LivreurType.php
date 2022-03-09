<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            //->add('roles')

            //->add('etat')
            ->add('email')
            ->add('lastname')
            ->add('tel')
            ->add('password',PasswordType::class,['empty_data'=> ''])
            ->add('confirm_password',PasswordType::class)
            ->add('image',FileType::class)
            //->add('activation_token')
            //->add('reset_token')
            //->add('panier')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
     }
}
