<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType as BaseType;

class CurrentLocationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    	$builder->add('name', 'text', array('label' => 'user.datas.current_location'))
    			->add('privacySettings', 'visibility_selector');
    }
    
    public function getName()
    {
        return 'current_location_form_type';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'class' => 'Droppy\UserBundle\Entity\CurrentLocation'
                );
    }
}