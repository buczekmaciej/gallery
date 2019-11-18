<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('SearchBar', TextType::class, ['attr'=>['class'=>'search-input','placeholder'=>'Search query']])
            ->add('SearchSubmit', SubmitType::class, ['label'=>'', 'attr'=>['class'=>'search-submit']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
    public function getPrefix()
    {
        return 'search';
    }
}
