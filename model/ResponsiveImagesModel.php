<?php

/**
 * responsive_images
 *
 * Copyright (C) 2013 - 2014 Andreas Nölke
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013 - 2014
 * @license LGPL-3.0+
 */
/**
 * Run in a custom namespace, so the class can be replaced
 */

namespace ResponsiveImages;

/**
 * Class ResponsiveImagesModel
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013 - 2014
 */
class ResponsiveImagesModel extends \Model
{

	/**
	 * Set Table name
	 */
	protected static $strTable = 'tl_responsive_images';

	public static function findByModuleAndPid($strType, $intPid)
	{
		if ($strType == "" || $intPid < 1)
		{
			return null;
		}

		$t = static::$strTable;
		$arrOptions['order'] = "$t.module";

		return static::findBy(array("$t.pid=? AND $t.module=? AND invisible=''"), array($intPid, $strType), $arrOptions);
	}
}
