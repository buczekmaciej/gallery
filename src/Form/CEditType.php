<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Tags;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CEditType extends AbstractType
{
    public function __construct(CategoriesRepository $cr)
    {
        $this->cr = $cr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $this->cr->findOneBy(['id' => $options['id']]);
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Category name',
                'data' => $data->getName()

            ])
            ->add('Tags', EntityType::class, [
                'class' => Tags::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'Name'
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Update'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('id');
        $resolver->setDefaults([]);
    }

    public function getBlockPrefix()
    {
        return 'editCategory';
    }
}
