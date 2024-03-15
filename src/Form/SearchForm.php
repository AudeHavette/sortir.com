<?php

namespace App\Form;


use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class  SearchForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus',
                'required' => false
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie contient :',
                'required' => false,
                'attr'=>[
                    'placeholder'=>'Soirée festive'
                ]
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('fin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('organisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'pseudo',
                'label' => 'Organisateur',
                'required' => false
            ])
        ->add('participants', EntityType::class, [
            'class'=>Utilisateur::class,
                'required'=>false
            ]

        );




    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method'=>'GET',
            'csrf_protection'=>false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}
