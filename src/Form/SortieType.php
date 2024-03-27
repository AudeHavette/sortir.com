<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\EventListener\AddPlaceFieldSubscriber;

class SortieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder //composant formBuilder de symfony
            ->add('nom', TextType::class, [
                'label'=>'Nom',
                'required'=>true
            ])
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
                'label'=>'Date et heure de début',
                'required'=>true
            ])
            ->add('duree', null, [
                'label'=>'duree',
                'required'=>true
            ])
            ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
                'label'=>'Date limite inscription',
                'required'=>true
            ])
            ->add('nbInscriptionsMax', null, [
                'label'=>'Nombre de places',
                'required'=>true
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Informations sortie',
                'required'=>true
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label'=>'Campus',
                'required'=>true
            ])
            //On récupère la liste des villes en BDD via un champ EntityType
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner une ville',
                'mapped' => false, //pour ne pas associer la valeur sélectionnée par l'utilisateur à  une propriété de la table Sortie
            ]);


        //1ère étape :
        // F° callback pour modif form en f° de la ville choisie=>récup lieux associés à ville
        $formModifier = function (FormInterface $form, ?Ville $ville = null) : void { //$formModifier utilise l'interface FormInterface & ville est nullable
            $lieux = null === $ville ? [] : $ville->getLieux();//En principe, les lieux st récup ici
        //En fonction de la valeur de $ville, cette expression ternaire renvoie soit un tableau vide de lieux soit les lieux associés à la ville sélectionnée.

            // On veut ajouter les lieux récup au select lieux, on ajoute le select lieu de type entityType si $sortie non null
            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionner un lieu',
                'choices' => $lieux, //donc ici le résultat du getLieux() sur $ville ci-dessus
            ]);
        };


        //2ème étape :
        // On ajoute un écouteur d'événements de type preSetData (avant rendu form) pour extraire ttes villes de la bdd et les charger dans le form
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,//W en amont avant affichage=> Avant rendu form pour perso form =>accéder à l'objet sous-jacent à sortie qui est ville(entityType)
            function (FormEvent $event) use ($formModifier): void {//en f° de l'event on appelle le callback formmodifier

                $data = $event->getData();//$data est Sortie =>on récup données du form à l'instant presetdata donc on récup ville, objet ss-jacent


                //ici on cherche à récup $ville = $data->getLieu()->getVille(); manquait ?Null self operator (corr°)
                $ville = $data->getLieu()?->getVille(); //récup ville associée à la sortie avec un null self operator puisque par déf° aucun lieu ne peut etre associe
                //à une sortie non encore crééee (cela a été notre principale difficulté)
                //Il faut récup lieu avant getLieu->getVille sur $sata (Sortie)

               $formModifier($event->getForm(), $ville);//mise à jour du form en fonction de ville si non null


            } );


        //3ème étape :
        //Ici, on s'interesse à récupérer la ville choisie et générer le select lieu en consq de ce chx
        $builder->get('ville')->addEventListener( //écouteur d'évenement sur le select ville qui récup valeur selectionnée
            FormEvents::POST_SUBMIT,//l'écouteur va réagir post submit donc après la select de la ville
            //méthode adaptée pour l'écouteur car il faut préalablement connaitre la ville pour select lieux associés
            function (FormEvent $event) use ($formModifier): void { //intervention du callback formMofier à l'event
                $ville = $event->getForm()->getData();// l'écouteur se déclenche en post submit=> on récup la ville choisie
                $formModifier($event->getForm()->getParent(),  $ville);//Et on génére le form avec le parent (sortie) et la ville considérée
            }
        );

        $builder->setAction($options['action']);//action définit l'url de l'action à laquelle le form sera soumis
        // 'action'=>$this->generateUrl dans le contrôleur pour la methode CreateSortie


    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
