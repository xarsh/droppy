<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType as BaseType;

class GenderFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    }
    
    public function getName()
    {
        return 'gender_selector';
    }
    
    public function getParent(array $options)
    {
        return 'choice';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'label' => 'infos.gender.gender',
                'choices' => array(
                        '0' => 'infos.gender.male',
                        '1' => 'infos.gender.female',
                        '2' => 'infos.gender.company',
                        '3' => 'infos.gender.group'),
                'expanded' => true,
                'multiple' => false,
                'required' => true
                );
    }
    
}