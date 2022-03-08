<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('lastname')
            ->add('email')
            ->add('tel')
            // suppression du role qui sera défini par défaut
            ->add('password', PasswordType::class)
            //->add('newpassword',PasswordType::class)
            ->add('image',FileType::class,['data_class'=>null,'label'=>'Image']
           );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
