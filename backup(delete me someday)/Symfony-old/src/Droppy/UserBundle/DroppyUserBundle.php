<?php

namespace Droppy\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DroppyUserBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
