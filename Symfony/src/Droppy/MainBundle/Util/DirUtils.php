<?php

namespace Droppy\MainBundle\Util;

class DirUtils
{
	public static function recursiveDirRemove($directory)
	{
		if(substr($directory, -1) == '/')
		{
			$directory = substr($directory, 0, -1);
		}
		if(!is_dir($directory))
		{
			return;
		}
		$objects = scandir($directory);
		foreach($objects as $object)
		{
			if($object != '.' && $object != '..')
			{
				if(is_dir($object))
				{
					self::recursiveDirRemove($directory);
				}
				else
				{
					unlink($object);
				}
			}
		}
		rmdir($directory);
	}
}
