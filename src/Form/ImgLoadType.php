<?php

namespace App\Form;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('Image', FileType::class, [
                'required'=>true,
                'constraints'=>[
                    new File([
                        'maxSize'=>'1024k',
                        'mimeTypes'=>[
                            'image/png',
                            'image/jpg',
                            'image/jpeg'
                        ]
                    ])
                ]
            ])
            ->add('title', TextType::class, ['attr'=>['placeholder'=>'Title...', 'class'=>'imgload-input']])
            ->add('category', EntityType::class, [
                'class'=>Categories::class,
                'choice_label'=>'Name',
                'expanded'=>false,
                'multiple'=>false
            ])
            ->add('Submit', SubmitType::class, ['label'=>'Upload','attr'=>['imgLoad-btn']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
        ]);
    }
    public function getPrefix()
    {
        return 'loadImage';
    }
}
