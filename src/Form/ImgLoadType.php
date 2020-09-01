<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Categories;

class ImgLoadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, [
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/svg',
                            'image/jpeg'
                        ]
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'Name',
                'required' => false,
                'placeholder' => 'No category'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Upload'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
    public function getPrefix()
    {
        return 'uploadImage';
    }
}
