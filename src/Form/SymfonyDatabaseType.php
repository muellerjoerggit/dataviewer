<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SymfonyDatabaseType  extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {

		$builder
			->add('tableName', TextType::class, [
				'label' => 'Tabellenname',
				'required' => false,
				'attr' => [
					'class' => 'border border-input ml-2'
				]
			])
			->add('submit', SubmitType::class, [
						'attr' => [
						'class' => 'border border-input p-1 mt-2'
					]
				]);

	}

}
