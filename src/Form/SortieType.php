<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom'
            ])
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
                'label'=>'Date et heure de début'
            ])
            ->add('duree', null, [
                'label'=>'duree'
            ])
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
                'label'=>'Date limite inscription'
            ])
            ->add('nbInscriptionsMax', null, [
                'label'=>'Nombre de places'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Informations sortie'
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label'=>'Campus'
            ])
            ->add('lieu', LieuType::class, [
                'label' => '',
                'required' => true,
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
