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
 * Class BreakPoint
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013 - 2014
 */
class BreakPoint
{

	/**
	 * contains the breakpoint config
	 *
	 * @var array
	 */
	protected $arrGlobalBreakPoints = array();

	/**
	 * contains the Responsive Images Model
	 *
	 * @var \ResponsiveImagesModel
	 */
	protected $objModel = null;

	/**
	 * Create the Breakpoint config
	 *
	 * @param array $arrBreakpoints
	 */
	public function createBreakPointConfig($arrBreakpoints)
	{
		if (!is_array($arrBreakpoints) || count($arrBreakpoints) < 1)
		{
			return $this->getGlobalBreakPoints();
		}

		$arrReturn = array();
		foreach ($arrBreakpoints as $arrConfig)
		{
			if ($arrConfig['bp_breakpoint'] < 1)
			{
				continue;
			}
			$arr = array();
			// breakpoint
			$arr['breakPoint'] = $arrConfig['bp_breakpoint'];
			// width
			$arr['size'][] = (isset($arrConfig['bp_width']) && (int)$arrConfig['bp_width'] > 0) ? (int)$arrConfig['bp_width'] : 0;
			// height
			$arr['size'][] = (isset($arrConfig['bp_height']) && (int)$arrConfig['bp_height'] > 0) ? (int)$arrConfig['bp_height'] : 0;
			// mode
			$arr['size'][] = (isset($arrConfig['bp_crop']) && $arrConfig['bp_crop'] != "") ? $arrConfig['bp_crop'] : 'proportional';
			// set Ratio
			if (isset($arrConfig['bp_ratio']) && $arrConfig['bp_ratio'] != '')
			{
				$arr['ratio'] = $this->setRatio($arrConfig['bp_ratio']);
			}
			$arrReturn[] = $arr;
		}
		if (count($arrReturn) < 1)
		{
			return $this->getGlobalBreakPoints();
		}
		return $arrReturn;
	}

	/**
	 * Read the break points from the globals to fallback
	 *
	 * @return array
	 */
	public function getGlobalBreakPoints()
	{
		if (!empty($this->arrGlobalBreakPoints) && count($this->arrGlobalBreakPoints) > 0)
		{
			return $this->arrGlobalBreakPoints;
		}
		$arrBreakPoints = trimsplit(',', $GLOBALS['TL_CONFIG']['breakPoints']);

		// unique breakpoints
		$arrBreakPoints = array_unique($arrBreakPoints);
		// Sort Breakpoints -> smallest first
		sort($arrBreakPoints);

		$arrReturn = array();
		foreach ($arrBreakPoints as $key => $breakPoint)
		{
			$arr = array();
			$arr['breakPoint'] = (int)$breakPoint;
			// width
			$arr['size'][] = (int)$breakPoint;
			$arrReturn[] = $arr;
		}
		$this->arrGlobalBreakPoints = $arrReturn;
		return $this->arrGlobalBreakPoints;
	}

	/**
	 * Set ratio
	 *
	 * @param String $ratio
	 * @return string
	 */
	protected function setRatio($ratio)
	{
		$ratio = str_replace('-', '', $ratio);
		if (strpos($ratio, '.') === false )
		{
			$ratio .= '.0';
		}
		return $ratio;
	}

}
