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

		foreach ($arrImageFields as $srcField => $arrFields)
		{
			$arrBreakPoints = $this->getBreakPoints($GLOBALS['TL_CONFIG']['breakPoints']);

			if (!is_array($arrBreakPoints))
			{
				return;
			}
			$arrFields['singleSRC'] = $srcField;
			$arrItem = $this->createItemArray($objTemplate, $arrFields);
			$arrBreakPointConfig = $this->createBreakPointConfigs($arrBreakPoints, $arrItem['singleSRC']);

			// create Picture Fill Array
			$arrPictureFill = array();
			foreach ($arrBreakPointConfig as $breakPoint)
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
	 * @global \PageModel $objPage
	 * @param String $strType
	 */
	protected function getThemeImageFields($strType)
	{
		global $objPage;

		if ($strType == "")
		{
			return;
		}
		// @todo add mobile Layout detection
		$objLayout = $objPage->getRelated('layout');

		$objResult = \Database::getInstance()->prepare('SELECT * FROM tl_responsive_images WHERE module=? AND pid=?')
			->execute($strType, $objLayout->pid);

		if ($objResult->numRows > 0)
		{
			$objResponsiveFields = $objResult->fetchAllAssoc();
			foreach ($objResponsiveFields as $fieldConfig)
			{
				$result = array();
				if ($fieldConfig['singleSRC'] == "")
				{
					continue;
				}
				$singleSRC = $fieldConfig['singleSRC'];
				foreach ($fieldConfig as $key => $fieldName)
				{
					if ($fieldName == "" || !in_array($key, $GLOBALS['TL_CONFIG']['imageFields']))
					{
						continue;
					}
					else if (strpos($key, 'Mandatory') > 0)
					{
						$result['mandatory'][] = $fieldName;
					}
					else
					{
						$result[$key] = $fieldName;
					}
				}
				$GLOBALS['TL_CONFIG']['hasImage'][$strType][$singleSRC] = $result;
			}
		}
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
	 * Create the Breakpoint config for an image
	 *
	 * @param array $arrBreakPoints
	 * @param String $strImagePath
	 */
	protected function createBreakPointConfigs($arrBreakPoints, $strImagePath)
	{
		$objFile = \FilesModel::findByPath($strImagePath);
		if (!$objFile)
		{
			return $arrBreakPoints;
		}

		$arrReturn = array();
		$arrBreakPointCropping = trimsplit(',', $objFile->breakPointCropping);
		foreach ($arrBreakPoints as $key => $breakPoint)
		{
			$arrReturn[$key] = $breakPoint;
			if (isset($arrBreakPointCropping[$key]))
			{
				$arrReturn[$key]['size'] = serialize($this->createBreakPointConfigArray($arrBreakPointCropping[$key]));
			}
		}

		return $arrReturn;
	}

	/**
	 * Read the break points from a given String and return it as array
	 *
	 * @param String $strBreakPoints
	 *
	 * @return array
	 */
	protected function getBreakPoints($strBreakPoints)
	{
		$arrBreakPoints = trimsplit(',', $strBreakPoints);

		// unique breakpoints
		$arrBreakPoints = array_unique($arrBreakPoints);
		// Sort Breakpoints -> smallest first
		sort($arrBreakPoints);

		$arrReturn = array();
		foreach ($arrBreakPoints as $key => $breakPoint)
		{
			$arrReturn[$key]['breakPoint'] = (int)$breakPoint;
		}
		return $arrReturn;
	}

	/**
	 * Create Config Array from config String
	 *
	 * @param type $breakPointConfig
	 *
	 * @return array
	 */
	protected function createBreakPointConfigArray($breakPointConfig)
	{
		$arrBreakPointConfig = trimsplit('|', $breakPointConfig);

		$arrReturn = array();
		if (isset($arrBreakPointConfig[0]) && (isset($arrBreakPointConfig[1]) || isset($arrBreakPointConfig[2])))
		{
			// width
			$arrReturn[] = (int)$arrBreakPointConfig[0];
			// height
			$arrReturn[] = (isset($arrBreakPointConfig[1]) && (int)$arrBreakPointConfig[1] > 0) ? (int)$arrBreakPointConfig[1] : 0;
			// mode
			$arrReturn[] = (isset($arrBreakPointConfig[2]) && $arrBreakPointConfig[2] != "") ? $arrBreakPointConfig[2] : 'proportional';
		}
		return $arrReturn;
	}

}
