<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType as BaseType;

class PersonalDatasFormType extends BaseType
{
	protected $full;
	
	public function __construct($full=true)
	{
		$this->full = $full;
	}
	
    public function buildForm(FormBuilder $builder, array $options)
    {
    	$builder->add('displayedName', 'text', array('label' => 'user.datas.name', 'required' => true));
    	if($this->full)
    	{
    		$builder->add('birthDate', 'birth_date_form_type', array('required' => false))
    				->add('birthPlace', 'birth_place_form_type', array('required' => false))
    				->add('currentLocation', 'current_location_form_type', array('required' => false))
    				->add('iconSet', 'icon_set_form_type', array('required' => false))
    				->add('introduction', 'textarea', array('label' => 'user.datas.introduction', 'required' => false));
    	}
    }
    
    public function getName()
    {
        return 'personal_datas_form_type';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
                'class' => 'Droppy\UserBundle\Entity\PersonalDatas'
                );
    }
}