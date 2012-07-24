<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType as BaseType;

class BirthDateFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
    	$builder->add('date', 'date_selector', array('label' => 'user.datas.birth_date'))
				->add('privacySettings', 'visibility_selector');
    }
    
    public function getName()
    {
        return 'birth_date_form_type';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'class' => 'Droppy\UserBundle\Entity\BirthDate'
                );
    }
}
