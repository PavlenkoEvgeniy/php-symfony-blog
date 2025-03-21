<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Содержание',
                'required' => true,
                'attr' => [
                    'rows' => 10,
                ]
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Опубликован',
                'required' => false,
            ])
            ->add('createdAt', null, [
                'label' => 'Дата создания',
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'label' => 'Дата обновления',
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
