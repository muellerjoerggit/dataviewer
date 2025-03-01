<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class TestTimeoutType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
			->add('time', NumberType::class,[
				'label' => 'seconds to sleep',
				'required' => true,
				'attr' => [
					'class' => 'border border-input ml-2'
				]
			])
			->add('submit', SubmitType::class,[
					'label' => 'Start',
					'attr' => [
						'class' => 'border border-input p-1 mt-2'
					]
			]);
		;
	}

}
