<?php

/**
 * responsiveimages
 *
 * Copyright (C) 2013 Andreas Nölke
 *
 * @package   responsiveimages
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013
 */

/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;

/**
 * Class ResponsiveImages
 *
 * Hook and Function for Overriding the maxImageWidth
 * @package   responsiveimages
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013
 */
class ResponsiveImages
{
	/**
	 * Hook to override teh max Image Width global
	 * @param \Template $objTemplate
	 */
	public function overrideImageSize($objTemplate)
	{
		if (TL_MODE == "FE")
		{
			$GLOBALS['TL_CONFIG']['maxImageWidth'] = self::getBreakpoint();
		}
	}

	/**
	 * Add the Client resolution to the cache String
	 * @param string $cacheKey
	 * @return string
	 */
	public function overrideCacheKey($cacheKey)
	{
		return $cacheKey .= "." . self::getBreakpoint();
	}

	/**
	 * Read the Client resolution
	 * @return int
	 */
	static function getClientResolution()
	{
		$clientWidth = 0;
		if (\Input::cookie('resolution') != "")
		{

			$cookie_data = explode(",", \Input::cookie('resolution'));
			$clientWidth = (int) $cookie_data[0];
		}
		return $clientWidth;
	}

	/**
	 * Returns the Breakpoint
	 * @return int
	 */
	static function getBreakpoint()
	{
		$arrBreakPoints = trimsplit(",", $GLOBALS['TL_CONFIG']['breakPoints']);
		asort($arrBreakPoints);
		$clientWidth = self::getClientResolution();
		if ($clientWidth > 0)
		{
			foreach($arrBreakPoints as $width)
			{
				if($clientWidth <= $width) {
					return $width;
				}
			}

		}
		return 0;
	}
}