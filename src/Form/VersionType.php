<?php

namespace App\Form;

use App\SymfonyEntity\Version;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VersionType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options): void {
    $builder
      ->add('id', TextType::class, [
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('label', TextType::class, [
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('predecessor', EntityType::class, [
        'class' => Version::class,
        'required' => false,
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('successor', EntityType::class, [
        'class' => Version::class,
        'required' => false,
        'attr' => [
          'class' => 'border border-input ml-2 mt-2',
        ],
      ])
      ->add('submit', SubmitType::class, [
        'label' => 'Version erstellen/speichern',
        'attr' => [
          'class' => 'border border-input p-1 mt-2',
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      'data_class' => Version::class,
    ]);
  }

}
