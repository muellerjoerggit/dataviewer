<?php

namespace App\Form;

use App\SymfonyEntity\Client;
use App\SymfonyEntity\Version;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options): void {
    $builder
      ->add('client_id', TextType::class, [
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('name', TextType::class, [
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('database_name', TextType::class, [
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('url', TextType::class, [
        'required' => FALSE,
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('version', EntityType::class, [
        'class' => Version::class,
        'required' => false,
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('submit', SubmitType::class, [
        'label' => 'Client erstellen/speichern',
        'attr' => [
          'class' => 'border border-input p-1 mt-2',
        ],
      ]);;
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      'data_class' => Client::class,
    ]);
  }

}
