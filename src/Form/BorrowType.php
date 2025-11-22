<?php

namespace App\Form;

use App\Entity\Borrow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BorrowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('returnDueDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de retour prévue',
                'attr' => [
                    'min' => (new \DateTime('+1 day'))->format('Y-m-d'),
                    'max' => (new \DateTime('+30 days'))->format('Y-m-d'),
                ],
                'help' => 'Maximum 30 jours'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Borrow::class,
        ]);
    }
}
