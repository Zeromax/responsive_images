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
	 * @var array
	 */
	protected $arrBreakpointConfig = array();

	/**
	 * contains the Responsive Images Model
	 *
	 * @var \ResponsiveImagesModel
	 */
	protected $objResponsiveModel = null;

	/**
	 * contains the Break Point Class
	 *
	 * @var \BreakPoint
	 */
	protected $objBreakPoint = null;

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
		$this->objResponsiveModel = \ResponsiveImagesModel::findByModuleAndPid($objTemplate->type, $objLayout->pid);
		$this->objBreakPoint = new \BreakPoint();

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

		$arrBreakpointConfig = $this->arrBreakpointConfig;
		foreach ($arrImageFields as $srcField => $arrFields)
		{
			if ($objTemplate->$srcField == "")
			{
				continue;
			}
			$arrBreakPoint = $arrBreakpointConfig[$objTemplate->type][$srcField];
			$objTemplate->{'pictureFill' . ucfirst($srcField)} = $this->createPictureFillArray($arrBreakPoint, $srcField, $arrFields, $objTemplate);
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
		$this->getThemeImageFields($objTemplate->type, $objTemplate);
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
	 * Create the PictureFill array
	 *
	 * @param array $arrBreakpointConfig
	 * @param String $strSrcField
	 * @param array $arrFields
	 * @param \FrontendTemplate $objTemplate
	 *
	 * @return array
	 */
	protected function createPictureFillArray($arrBreakpointConfig, $strSrcField, $arrFields, $objTemplate)
	{
		if (count($arrBreakpointConfig) < 1)
		{
			$arrBreakpointConfig = $this->objBreakPoint->getGlobalBreakPoints();
		}
		$arrFields['singleSRC'] = $strSrcField;
		$arrItem = $this->createItemArray($objTemplate, $arrFields);

		if (isset($GLOBALS['TL_HOOKS']['createPictureFillArray']) && is_array($GLOBALS['TL_HOOKS']['createPictureFillArray']))
		{
			foreach ($GLOBALS['TL_HOOKS']['createPictureFillArray'] as $callback)
			{
				$this->import($callback[0]);
				$result = $this->$callback[0]->$callback[1]($arrBreakpointConfig, $strSrcField, $arrFields, $objTemplate, $arrItem);
				if (isset($result) && is_array($result) && count($result) > 0)
				{
					return $result;
				}
			}
		}

		// create Picture Fill Array
		$arrPictureFill = array();
		foreach ($arrBreakpointConfig as $breakPoint)
		{
			$objImage = $this->addImageToPictureFill($arrItem, $breakPoint);
			if ($objImage)
			{
				$arrPictureFill[] = $objImage;
			}
		}
		return $arrPictureFill;
	}

	/**
	 * Get The Image field from a them and override the Globals config
	 *
	 * @param String $strType
	 * @param \FrontendTemplate $objTemplate
	 */
	protected function getThemeImageFields($strType, $objTemplate)
	{
		if ($this->objResponsiveModel === null)
		{
			return;
		}
		$arrBreakPointConfig = array();
		while ($this->objResponsiveModel->next())
		{
			$objResponsiveFields = $this->objResponsiveModel->current();
			if ($objResponsiveFields->singleSRC == "")
			{
				continue;
			}
			$singleSRC = $objResponsiveFields->singleSRC;
			$arrBreakPointConfig[$strType][$singleSRC] = $this->createBreakPointArray($objTemplate->$singleSRC, $objResponsiveFields);
			$GLOBALS['TL_CONFIG']['hasImage'][$strType][$singleSRC] = $this->createImageFieldArray($objResponsiveFields);
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
		if (!is_array($arrImageFields['eval']['mandatory']))
		{
			return true;
		}
		foreach ($arrImageFields['eval']['mandatory'] as $field)
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
		if ($breakPoint['breakPoint'] < 1 && $breakPoint['breakPoint'] == "")
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
		if (isset($breakPoint['ratio']))
		{
			// @todo: think about -webkit or other prefixes
			$objImage->ratio = '(min-device-pixel-ratio: ' . $breakPoint['ratio'] . ')';
			$objImage->ratioInt = $breakPoint['breakPoint'];
		}
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
			if ($key == 'eval')
			{
				continue;
			}
			$arrItem[$key] = $objTemplate->$field;
		}
		return $arrItem;
	}

	/**
	 * Create the image field Array
	 *
	 * @param \ResponsiveImagesModel $objResponsiveFields
	 *
	 * @return array
	 */
	protected function createImageFieldArray($objResponsiveFields)
	{
		foreach ($GLOBALS['TL_CONFIG']['imageFields'] as $field)
		{
			if ($objResponsiveFields->$field != "")
			{
				$result[$field] = $objResponsiveFields->$field;
			}
			if ($objResponsiveFields->{$field . 'Mandatory'})
			{
				$result['eval']['mandatory'][] = $field;
			}
		}
		return $result;
	}

	/**
	 * Create the breakpoint array
	 * order: 1. file | 2. image set | 3. global config
	 *
	 * @param String $strImageField
	 * @param \ResponsiveImagesModel $objResponsiveFields
	 *
	 * @return array
	 */
	protected function createBreakPointArray($strImageField, $objResponsiveFields)
	{
		$objFile = \FilesModel::findOneByPath($strImageField);
		if ($objFile !== null && $objFile->addBreakpoints)
		{
			$arrBreakpoints = deserialize($objFile->breakpoints);
		}
		else if ($objResponsiveFields->addBreakpoints)
		{
			$arrBreakpoints = deserialize($objResponsiveFields->breakpoints);
		}
		if (is_array($arrBreakpoints) && count($arrBreakpoints) > 0)
		{
			return $this->objBreakPoint->createBreakPointConfig($arrBreakpoints);
		}
		else
		{
			return $this->objBreakPoint->getGlobalBreakPoints();
		}
	}

}
