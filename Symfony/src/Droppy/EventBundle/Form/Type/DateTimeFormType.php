<?php

namespace Droppy\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DateTimeFormType extends AbstractType 
{
    protected $showAll;
    
    public function __construct($showAll = true)
    {
        $this->showAll = $showAll;
    }
    
	public function buildForm(FormBuilder $builder, array $options) 
	{
		$builder->add('date', 'date_selector', array('required' => true))
				->add('time', 'time_selector');
		if($this->showAll)
		{
		    $builder//->add('timezone', 'timezone_selector')
				    ->add('allDay', 'checkbox');
		}
	}

	public function getName()
	{
		return 'date_time_selector';
	}


	public function getDefaultOptions(array $options) 
	{
		return array('data_class' => 'Droppy\EventBundle\Entity\EventDateTime');
	}
}
