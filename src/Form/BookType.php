<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookCondition;
use App\Entity\BookStatus;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('author')
            ->add('depositAmount')
            ->add('isArchived')
            ->add('status', EntityType::class, [
                'class' => BookStatus::class,
                'choice_label' => 'name',
                'expanded' => true,
                'required' => false,
            ])
            ->add('condition', EntityType::class, [
                'class' => BookCondition::class,
                'choice_label' => 'name',
                'expanded' => true,
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
