<?php

namespace App\Form;

use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Tags;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', TextType::class, ['attr'=>['placeholder'=>'Name...']])
            ->add('Tags', EntityType::class, [
                'class'=>Tags::class,
                'multiple'=>true,
                'expanded'=>true,
                'choice_label'=>'Name'
            ])
            ->add('Submit', SubmitType::class, ['label'=>'Create'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
    public function getPrefix()
    {
        return 'categories';
    }
}
