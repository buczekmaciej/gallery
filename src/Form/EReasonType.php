<?php

namespace App\Form;

use App\Repository\ReasonsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EReasonType extends AbstractType
{
    public function __construct(ReasonsRepository $rr)
    {
        $this->rr = $rr;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason', TextareaType::class, [
                'label' => 'Reason',
                'data' => $this->rr->findOneBy(['id' => $options['id']])->getReason()
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
        $resolver->setRequired('id');
    }

    public function getBlockPrefix()
    {
        return 'editReason';
    }
}
