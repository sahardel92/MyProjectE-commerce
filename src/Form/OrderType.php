<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'required' => true,
                'label' => 'Prénom',
                'attr' => ['class' => 'form form-control']
            ])
            ->add('lastName', null, [
                'required' => true,
                'label' => 'Nom',
                'attr' => ['class' => 'form form-control']
            ])
            ->add('phone', null, [
                'required' => true,
                'label' => 'Téléphone',
                'attr' => ['class' => 'form form-control']
            ])
            ->add('adresse', null, [
                'required' => true,
                'label' => 'Adresse',
                'attr' => ['class' => 'form form-control']
            ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Ville',
                'attr' => ['class' => 'form-select']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Continuer vers le paiement',
                'attr' => ['class' => 'btn btn-primary btn-lg mt-3 float-end']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
