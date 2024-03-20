<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    private $villeRepository;

    public function __construct(VilleRepository $villeRepository)
    {
        $this->villeRepository = $villeRepository;
    }

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
            //On récupère la liste des villes en BDD
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner une ville',
                'mapped' => false,
            ]);





        // En fonction de la ville sélectionnée on veut adapter le select des lieux
        $formModifier = function (FormInterface $form, ?Ville $ville = null) : void {
            $lieux = null === $ville ? [] : $ville->getLieux();//En principe, les lieux st récup ici



            // On veut ajouter les lieux récup au select lieux
            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un lieu',
                'choices' => $lieux,
            ]);


        };

        //Tentative
        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier): void {
                $ville = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(),  $ville);
            }
        );


        // On ajoute un écouteur d'événements pour mettre à jour le select 'lieu' en fonction la ville choisie
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {//PASSER VILLE DANS USE POUR LUTILISER dans addeventlistener

                $data = $event->getData();


                //$ville = $data->getLieu()->getVille(); manquait ?null self operator
                $ville = $data->getLieu()?->getVille();
                //Il faut récup lieu avant getLieu->getVille sur $sata (Sortie)
                $formModifier($event->getForm(), $ville);//ICI PB car $data=$sortie et pas de getVille dansSortie donc
                //il faut récupérer lieu avant de faire $data->getLieu->getVille car lieu est nul

            } );




        /*
                $builder->get('ville')->addEventListener(
                    FormEvents::POST_SUBMIT,
                    function (FormEvent $event) use ($formModifier): void {
                        $ville = $event->getForm()->getData();
                        $formModifier($event->getForm()->getParent(), null, $ville);
                    }
                );
        */
        $builder->setAction($options['action']);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
