<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use DateTime;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'data' => isset($options['data']) &&
                    $options['data']->getPublishedAt() != null ? $options['data']->getPublishedAt() : new DateTime('now'),
                'label' => 'Date* :'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre* :',
                'attr' => [
                    'maxlength' => 100,
                    'minlenght' => 2
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description : ',
                'required' => false
            ])
            ->add('videoId', TextType::class, [
                'label' => 'URL de la vidéo* = www.youtube.com/',
                'attr' => [
                    'maxlength' => 12
                ]
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'label' => "Playlist(s)* :",
                'choice_label' => 'name',
            ])
            ->add('categories', EntityType::class, [
                'label' => "Catégorie(s)* :",
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
