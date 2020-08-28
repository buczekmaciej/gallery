<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Username', TextType::class, [
                'label' => 'Username'
            ])
            ->add('Password', PasswordType::class, [
                'label' => 'Password'
            ])
            ->add('Email', TextType::class, [
                'label' => 'Email'
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Board in'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function getPrefix()
    {
        return 'register';
    }
}
