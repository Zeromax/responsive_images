<?php

/**
 * responsive_images
 *
 * Copyright (C) 2013 - 2014 Andreas Nölke
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013 - 2014
 */
/**
 * Run in a custom namespace, so the class can be replaced
 */

namespace ResponsiveImages;

/**
 * Class PictureFill
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013 - 2014
 */
class PictureFill
{

	/**
	 * Add the picture fill to the template
	 *
	 * @param \FrontendTemplate $objTemplate
	 */
	public function parseTemplate($objTemplate)
	{
		$this->createPictureFill($objTemplate);
	}

	/**
	 * Create picture polyfill
	 *
	 * @param \FrontendTemplate $objTemplate
	 */
	public function createPictureFill($objTemplate)
	{
		$arrImageFields = $this->getImageFields($objTemplate);
		if (!is_array($arrImageFields))
		{
			return;
		}
		$arrBreakPoints = $this->getBreakPoints();
		if (!is_array($arrBreakPoints))
		{
			return;
		}

		$arrItem = $this->createItemArray($objTemplate, $arrImageFields);

		// create Picture Fill Array
		$arrPictureFill = array();
		foreach ($arrBreakPoints as $breakPoint)
		{
			$objImage = $this->addImageToPictureFill($arrItem, $breakPoint);
			if ($objImage)
			{
				$arrPictureFill[] = $objImage;
			}
		}
		$objTemplate->pictureFill = $arrPictureFill;
	}

	/**
	 * Get image fields as Array
	 *
	 * @param \FrontendTemplate $objTemplate
	 * @param boolean $checkMandatory
	 *
	 * @return array
	 */
	protected function getImageFields($objTemplate, $checkMandatory = true)
	{
		if ($objTemplate->type != '' && is_array($GLOBALS['TL_CONFIG']['hasImage'][$objTemplate->type]))
		{
			$arrImageFields = $GLOBALS['TL_CONFIG']['hasImage'][$objTemplate->type];
			if (($this->checkMandatoryFields($objTemplate, $arrImageFields) && $checkMandatory) || !$checkMandatory)
			{
				return $arrImageFields;
			}
		}
		return null;
	}

	/**
	 * Check if one of the mandatory fields is empty
	 *
	 * @param array $arrImageFields
	 * @param \FrontendTemplate $objTemplate
	 *
	 * @return boolean
	 */
	protected function checkMandatoryFields($objTemplate, $arrImageFields)
	{
		if (!is_array($arrImageFields['mandatory']))
		{
			return true;
		}
		foreach ($arrImageFields['mandatory'] as $field)
		{
			if ($objTemplate->$field == '')
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Crate the picture and return it as object
	 *
	 * @param \FrontendTemplate $objTemplate
	 * @param array $arrImageFields
	 * @param int $breakPoint
	 *
	 * @return object
	 */
	protected function addImageToPictureFill($arrItem, $breakPoint)
	{
		if (is_array($breakPoint) && $breakPoint['breakPoint'] < 1 && $breakPoint['breakPoint'] == "")
		{
			return null;
		}

		$objImage = new \stdClass;
		if (isset ($breakPoint['size']) && $breakPoint['size'] != "")
		{
			$size = deserialize($arrItem['size']);
			// set the normal width to the image
			$breakPoint['size'][0] = $size[0];
			$arrItem['size'] = $breakPoint['size'];
		}
		\Controller::addImageToTemplate($objImage, $arrItem, $breakPoint['breakPoint'], '');
		$objImage->breakPoint = $breakPoint['breakPoint'];
		return $objImage;
	}

	/**
	 * Create Item Array from image fields
	 *
	 * @param \FrontendTemplate $objTemplate
	 * @param array $arrImageFields
	 * @return array
	 */
	protected function createItemArray($objTemplate, $arrImageFields)
	{
		$arrItem = array();
		foreach ($arrImageFields as $key => $field)
		{
			$arrItem[$key] = $objTemplate->$field;
		}
		return $arrItem;
	}

	/**
	 * Read the global break points and return it as array
	 *
	 * @return array
	 */
	public static function getBreakPoints()
	{
		$arrBreakPoints = trimsplit(',', $GLOBALS['TL_CONFIG']['breakPoints']);

		$arrReturn = array();
		foreach ($arrBreakPoints as $key => $breakPointConfig)
		{
			$arrBreakPointConfig = trimsplit('|', $breakPointConfig);

			if (isset($arrBreakPointConfig[0]))
			{
				$arrReturn[$key]['breakPoint'] = (int)$arrBreakPointConfig[0];
			}
			if (isset($arrBreakPointConfig[0]) && (isset($arrBreakPointConfig[1]) || isset($arrBreakPointConfig[2])))
			{
				$arrSize = array();
				// height
				$arrSize[] = (int)$arrBreakPointConfig[0];
				// width
				$arrSize[] = (isset($arrBreakPointConfig[1]) && (int)$arrBreakPointConfig[1] > 0) ? (int)$arrBreakPointConfig[1] : 0;
				// mode
				$arrSize[] = (isset($arrBreakPointConfig[2]) && $arrBreakPointConfig[2] != "") ? $arrBreakPointConfig[2] : 'proportional';
				$arrReturn[$key]['size'] = $arrSize;
			}
		}

		// @TODO: Sort Breakpoints
		return $arrReturn;
	}

}
