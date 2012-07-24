<?php

namespace Droppy\EventBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Droppy\EventBundle\Form\Type\DateTimeFormType;

class EventCreationFormType extends AbstractType
{
	protected $displayAll;

	public function __construct($displayAll=true)
	{
		$this->displayAll = $displayAll;
	}
	
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'event.title', 'required' => true));
		if($this->displayAll)
		{
            $builder->add('startDateTime', 'date_time_selector', array('required' => true))
        	    	->add('endDateTime', new DateTimeFormType(false), array('required' => false))
                    ->add('details', 'textarea', array('label' => 'event.details', 'required' => false))
                    ->add('location', 'location_form_type')
                    ->add('address', 'text', array('label' => 'event.address', 'required' => false))
				    ->add('iconSet', 'icon_set_form_type', array(
					    'attr' => array ('class' => 'icon')
				))
                    ->add('privacySettings', 'visibility_selector')
                    ->add('color', 'color_selector')
                    ->add('tags', 'tag_selector')
                    ->add('url', 'text', array('label' => 'event.url', 'required' => false));
		}
    }

    public function getName()
    {
        return 'event_form_type';
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Droppy\EventBundle\Entity\Event');
    }
}
