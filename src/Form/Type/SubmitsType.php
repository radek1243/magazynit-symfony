<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitsType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['submits'] as $submit){
            $builder->add($submit['name'], SubmitType::class, ['label' => $submit['label']]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['submits' => [
                ['name' => 'submit_return', 'label' => 'Powrót'],
                ['name' => 'submit_utilization', 'label' => 'Utylizacja']
            ]]
        );
        $resolver->setAllowedTypes('submits',['array', 'array', 'string']);
    }
}