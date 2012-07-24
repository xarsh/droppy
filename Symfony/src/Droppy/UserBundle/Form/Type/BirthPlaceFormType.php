<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType as BaseType;

class BirthPlaceFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    	$builder->add('name', 'text', array('label' => 'user.datas.birth_place'))
    			->add('privacySettings', 'visibility_selector');
    }
    
    public function getName()
    {
        return 'birth_place_form_type';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'class' => 'Droppy\UserBundle\Entity\BirthPlace'
                );
    }
}