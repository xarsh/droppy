<?php

namespace Droppy\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Translation\Translator;

class PrivacySettingsFormType extends AbstractType
{
	protected $isExpanded;
	protected $translator;
	
	public function __construct(Translator $translator, $isExpanded=false) 
	{
		$this->isExpanded = $isExpanded;
		$this->translator = $translator;
	}

	protected function translate($message)
	{
		return $this->translator->trans($message, array(), 'DroppyMainBundle');
	}

	
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
		->add('visibility', 'choice', array(
				'label' => 'privacy_policy.settings',
				'choices' => array(
				            'public' => $this->translate('privacy_policy.public'),
				            'private' => $this->translate('privacy_policy.private'), 
//				            'protected' => $this->translate('privacy_policy.protected'), 
				        ),
				'preferred_choices' => array('private'),
				'expanded' => $this->isExpanded,
				'multiple' => false));
	}

	public function getName()
	{
		return 'visibility_selector';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
				'data_class' => 'Droppy\MainBundle\Entity\PrivacySettings');
	}
}
