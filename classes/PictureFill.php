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
	 * contains the breakpoint config
	 *
	 * @var array $arrBreakpointConfig
	 */
	protected $arrBreakpointConfig = array();

	/**
	 * contains the Responsive Images Model
	 *
	 * @var \ResponsiveImagesModel $objModel
	 */
	protected $objModel = array();

	/**
	 * Add the picture fill to the template
	 *
	 * @param \FrontendTemplate $objTemplate
	 */
	public function parseTemplate($objTemplate)
	{
		global $objPage;

		// @todo add mobile Layout detection
		$objLayout = $objPage->getRelated('layout');
		$this->objModel = \ResponsiveImagesModel::findByModuleAndPid($objTemplate->type, $objLayout->pid);

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

		foreach ($arrImageFields as $srcField => $arrFields)
		{
			if ($objTemplate->$srcField == "")
			{
				continue;
			}

			$arrBreakpointConfig = $this->arrBreakpointConfig;
			if (count($arrBreakpointConfig) < 1)
			{
				$arrBreakpointConfig[$objTemplate->type][$srcField] = $this->getBreakPoints();
			}
			$arrFields['singleSRC'] = $srcField;
			$arrItem = $this->createItemArray($objTemplate, $arrFields);

			// create Picture Fill Array
			$arrPictureFill = array();
			foreach ($arrBreakpointConfig[$objTemplate->type][$srcField] as $breakPoint)
			{
				$objImage = $this->addImageToPictureFill($arrItem, $breakPoint);
				if ($objImage)
				{
					$arrPictureFill[] = $objImage;
				}
			}
			$objTemplate->{'pictureFill' . ucfirst($srcField)} = $arrPictureFill;
		}
	}

	/**
	 * Get image fields as Array
	 *
	 * @param \FrontendTemplate $objTemplate
	 *
	 * @return array
	 */
	protected function getImageFields($objTemplate)
	{
		$this->getThemeImageFields($objTemplate->type);
		if ($objTemplate->type != '' && is_array($GLOBALS['TL_CONFIG']['hasImage'][$objTemplate->type]))
		{
			$arrImageFields = $GLOBALS['TL_CONFIG']['hasImage'][$objTemplate->type];
			foreach ($arrImageFields as $key => $arrFields)
			{
				if (!$this->checkMandatoryFields($objTemplate, $arrFields) || $objTemplate->$key == "")
				{
					unset($arrImageFields[$key]);
				}
			}
			if (count($arrImageFields) > 0)
			{
				return $arrImageFields;
			}
		}
		return null;
	}

	/**
	 * Get The Image field from a them and override the Globals config
	 *
	 * @param String $strType
	 */
	protected function getThemeImageFields($strType)
	{
		if ($this->objModel === null)
		{
			return;
		}
		$arrBreakPointConfig = array();
		while ($this->objModel->next())
		{
			$objResponsiveFields = $this->objModel->current();
			if ($objResponsiveFields->singleSRC == "")
			{
				continue;
			}
			$result = array();
			$singleSRC = $objResponsiveFields->singleSRC;
			foreach ($GLOBALS['TL_CONFIG']['imageFields'] as $field)
			{
				if ($objResponsiveFields->$field != "")
				{
					$result[$field] = $objResponsiveFields->$field;
				}
				if ($objResponsiveFields->{$field . 'Mandatory'})
				{
					$result['mandatory'][] = $fieldName;
				}
			}
			if ($objResponsiveFields->addBreakpoints)
			{
				$arrBreakpoints = deserialize($objResponsiveFields->breakpoints);
				$arrBreakPointConfig[$strType][$singleSRC] = $this->createBreakPointConfig($arrBreakpoints);
			}
			else
			{
				$arrBreakPointConfig[$strType][$singleSRC] = $this->getBreakPoints();
			}
			$GLOBALS['TL_CONFIG']['hasImage'][$strType][$singleSRC] = $result;
		}
		$this->arrBreakpointConfig = $arrBreakPointConfig;
	}

	/**
	 * Check if one of the mandatory fields is empty
	 *
	 * @param \FrontendTemplate $objTemplate
	 * @param array $arrImageFields
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
	 * Create the picture and return it as object
	 *
	 * @param array $arrItem
	 * @param array $breakPoint
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
		if (isset($breakPoint['size']) && $breakPoint['size'] != "")
		{
			$arrItem['size'] = $breakPoint['size'];
		}
		\Controller::addImageToTemplate($objImage, $arrItem, $breakPoint['breakPoint'], '');
		// @todo: create configureable field for breakpoint unit
		$objImage->breakPointUnit = 'px';
		$objImage->breakPoint = $breakPoint['breakPoint'] . $objImage->breakPointUnit;
		$objImage->breakPointInt = $breakPoint['breakPoint'];
		return $objImage;
	}

	/**
	 * Create Item Array from image fields
	 *
	 * @param \FrontendTemplate $objTemplate
	 * @param array $arrImageFields
	 *
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
	 * Create the Breakpoint config
	 *
	 * @param array $arrBreakpoints
	 */
	protected function createBreakPointConfig($arrBreakpoints)
	{
		if (!is_array($arrBreakpoints) || count($arrBreakpoints) < 1)
		{
			return $this->getBreakPoints();
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
			$arr['breakPoint'] = $arrConfig['bp_width'];
			// width
			$arr['size'][] = (isset($arrConfig['bp_width']) && (int)$arrConfig['bp_width'] > 0) ? (int)$arrConfig['bp_width'] : 0;
			// height
			$arr['size'][] = (isset($arrConfig['bp_height']) && (int)$arrConfig['bp_height'] > 0) ? (int)$arrConfig['bp_height'] : 0;
			// mode
			$arr['size'][] = (isset($arrConfig['bp_crop']) && $arrConfig['bp_crop'] != "") ? $arrConfig['bp_crop'] : 'proportional';
			$arrReturn[] = $arr;
		}
		if (count($arrReturn) < 1)
		{
			return $this->getBreakPoints();
		}
		return $arrReturn;
	}

	/**
	 * Read the break points from the globals to fallback
	 *
	 * @return array
	 */
	protected function getBreakPoints()
	{
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
		return $arrReturn;
	}

}
