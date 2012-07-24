<?php

namespace Droppy\WebApplicationBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserUnique extends Constraint
{
	public $message = 'schedule.creation.name_already_used'; 
	
	public function targets()
	{
		return self::PROPERTY_CONSTRAINT;
	}
}