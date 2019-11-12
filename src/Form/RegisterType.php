<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Login', TextType::class, ['attr'=>['placeholder'=>'Username...', 'class'=>'register-login-input']])
            ->add('Password', RepeatedType::class, [
                'type'=>PasswordType::class,
                'options'=>['attr'=>['class'=>'register-password-input']],
                'first_options'=>['attr'=>['placeholder'=>'Password...']],
                'second_options'=>['attr'=>['placeholder'=>'Repeat password...']]
            ])
            ->add('Email', TextType::class, ['attr'=>['placeholder'=>'E-mail...', 'class'=>'register-mail-input']])
            ->add('Submit', SubmitType::class, ['label'=>'Register', 'attr'=>['class'=>'register-submit']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getPrefix()
    {
        return 'register';
    }
}
