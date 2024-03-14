<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminModifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'disabled' => true
            ])
            ->add('prenom', null, [
                'disabled' => true
            ])
            ->add('telephone', null, [
                'disabled' => true
            ])
            ->add('email', null, [
                'disabled' => true
            ])
            ->add('pseudo', null, [
                'disabled' => true
            ]);

        if ($options['isAdmin']) {
            $builder
                ->add('actif', ChoiceType::class, [
                    'label' => 'Actif',
                    'choices' => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'expanded' => true, // Utilise des boutons radio au lieu d'une liste déroulante
                    'required' => false, // Si le champ n'est pas obligatoire
                ])
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'Utilisateur' => 'ROLE_USER',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('campus', EntityType::class, [
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                ]);

        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'isAdmin' => false,
        ]);
    }
}
