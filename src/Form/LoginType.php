<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Login', TextType::class, ['attr'=>['placeholder'=>'Username...', 'class'=>'login-usrnm-input']])
            ->add('Password', PasswordType::class, ['attr'=>['placeholder'=>'Password...', 'class'=>'login-pass-input']])
            ->add('Submit', SubmitType::class, ['label'=>'Login', 'attr'=>['class'=>'login-submit']])
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
        return 'login';
    }
}
