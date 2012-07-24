<?php

namespace Droppy\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\Common\Persistence\ObjectManager;
use Droppy\EventBundle\Form\DataTransformer\LocationTransformer;

class LocationFormType extends AbstractType
{
    protected $om;
    
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
    
	public function buildForm(FormBuilder $builder, array $options)
	{
	    $transformer = new LocationTransformer($this->om);
		$builder->appendClientTransformer($transformer);
	}

	public function getName()
	{
		return 'location_form_type';
	}
	
	public function getParent(array $options)
	{
	    return 'text';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
				'data_class' => 'Droppy\EventBundle\Entity\Location',
		        'label' => 'location.place',
		        'required' => false
		);
	}
}