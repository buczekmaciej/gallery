<?php

namespace App\Form;

use App\Repository\TagsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TEditType extends AbstractType
{
    public function __construct(TagsRepository $tr)
    {
        $this->tr = $tr;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $this->tr->findOneBy(['id' => $options['id']]);
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Tag name',
                'data' => $data->getName()
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

    public function getPrefix()
    {
        return 'tags';
    }
}
