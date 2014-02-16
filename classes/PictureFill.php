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
		$arrBreakPoints = trimsplit(',', $GLOBALS['TL_CONFIG']['breakPoints']);
		$arrImageFields = $this->getImageFields($objTemplate);
		if (!is_array($arrBreakPoints) || !is_array($arrImageFields))
		{
			return;
		}

		$arrItem = $this->createItemArray($objTemplate, $arrImageFields);
		// @TODO: the Lightbox string should be the same every time!
		$strLightboxId = "";

		// create Picture Fill Array
		$arrPictureFill = array();
		foreach ($arrBreakPoints as $breakPoint)
		{
			$objImage = $this->addImageToPictureFill($arrItem, $breakPoint, $strLightboxId);
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
	 *
	 * @param \FrontendTemplate $objTemplate
	 * @param array $arrImageFields
	 * @param int $breakPoint
	 *
	 * @return object
	 */
	protected function addImageToPictureFill($arrItem, $breakPoint, $strLightboxId)
	{
		if ($breakPoint < 1 && $breakPoint == "")
		{
			return null;
		}

		$objImage = new \stdClass;
		\Controller::addImageToTemplate($objImage, $arrItem, $breakPoint, $strLightboxId);
		return $objImage;
	}

	/**
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

}
