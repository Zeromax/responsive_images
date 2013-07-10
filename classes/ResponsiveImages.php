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
class ResponsiveImages extends \Template
{
	/**
	 * Hook to override the global max Image Width
	 * @param \Template $objTemplate
	 */
	public function overrideImageSize($objTemplate)
	{
		if (TL_MODE == "FE")
		{
//			$GLOBALS['TL_CONFIG']['maxImageWidth'] = self::getBreakpoint();
//
//			if($GLOBALS['TL_CONFIG']['maxImageWidth'] > 0 && isset($GLOBALS['TL_CONFIG']['elementToResizeImg']) && is_array($GLOBALS['TL_CONFIG']['elementToResizeImg']))
//			{
//				if($objTemplate->fullsize)
//				{
//					$resizeFile = $objTemplate->singleSRC;
//					if (TL_FILES_URL != '')
//					{
//						$path = TL_FILES_URL . $GLOBALS['TL_CONFIG']['uploadPath'] . '/';
//						$resizeFile = str_replace($path, "", $objTemplate->singleSRC);
//					}
//					if (file_exists(TL_ROOT .'/'. rawurldecode($resizeFile)))
//					{
//						$intMaxWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
//						$imgSize = @getimagesize(TL_ROOT .'/'. rawurldecode($resizeFile));
//						if($imgSize[1] > $intMaxWidth || $imgSize[0] > $intMaxWidth)
//						{
//							$size[0] = $intMaxWidth;
//							// Adjust the image size
//							$ratio = $imgSize[1] / $imgSize[0];
//							$size[1] = floor($intMaxWidth * $ratio);
//							$src = \Image::get($resizeFile, $size[0], $size[1], '');
//
//							$objTemplate->singleSRC = $path.$src;
//							$objTemplate->href = $path.$src;
//						}
//					}
//					// Get the file entries from the database
//					$objFiles = \FilesModel::findMultipleByIds($objTemplate->multiSRC);
//
//					if ($objFiles === null)
//					{
//						return '';
//					}
//				}
//			}
		}
	}

	public function replaceImages($strContent, $strTemplate)
	{
		if (TL_MODE == "FE")
		{
			$GLOBALS['TL_CONFIG']['maxImageWidth'] = self::getBreakpoint();

			if($GLOBALS['TL_CONFIG']['maxImageWidth'] > 0 && strpos($strTemplate, 'fe_', 0) !== false)
			{
				if ($strContent != "")
				{
					// It's a Hack... Ther must be another solution
					$strContent = $this->replaceInsertTags($strContent);

					$arrPattern = array();
					// pattern for IMG Tags
					$arrPattern['img'] = '/\<img[a-zA-Z0-9%#_:&;\/\.\-\=\"\s\']*>/i';
					// pattern for links
					$arrPattern['link'] = '/\<a[a-zA-Z0-9%#_:&;\/\.\-\=\"\s\']*>/i';

					foreach ($arrPattern as $type=>$pattern)
					{
						switch ($type)
						{
							case "img":
								// Pattern for height| width | src
								$imgPattern = '/(src=("|\')(?<file>(files|tl_files)[a-zA-Z0-9%#_:&;\/\.\-\s]{0,850})")|(width=("|\')(?<width>[0-9]{1,9})("|\'))|(height=("|\')(?<height>[0-9]{1,9})("|\'))/i';
								$strContent = $this->getFileToReplace($pattern, $imgPattern, $strContent, $type);
								break;
							case "link":
								// Pattern for href
								$imgPattern = '/(href=("|\')(?<file>(files|tl_files)[a-zA-Z0-9%#_:&;\/\.\-\s]{0,850}\.(jpe?g|gif|png))("|\'))/i';
								$strContent = $this->getFileToReplace($pattern, $imgPattern, $strContent, $type);
								break;
							default:
								break;
						}
					}
				}
			}
		}
		return $strContent;
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
		else if($GLOBALS['TL_CONFIG']['mobileWidthFallback'] > 0 && \Environment::get('agent')->mobile)
		{
			$clientWidth = $GLOBALS['TL_CONFIG']['mobileWidthFallback'];
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
				if($clientWidth <= $width) return $width;
			}

		}
		return 0;
	}

	/**
	 * Replace alle images
	 *
	 * @param String $pattern
	 * @param String $imgPattern
	 * @param String $strContent
	 * @param String $matches
	 * @param String $type
	 * @return String
	 */
	protected function getFileToReplace($pattern, $imgPattern, $strContent, $type)
	{
		$matches = "";
		preg_match_all($pattern, $strContent, $matches);

		// Replace each image if necessary
		foreach($matches[0] as $file)
		{
			if ($file == "" || $file === null) continue;
			$fileMatches = "";
			$replaceFile = "";
			preg_match_all($imgPattern, $file, $fileMatches);

			$tmpFile =  array_filter($fileMatches['file']);
			sort($tmpFile);

			if (!isset($tmpFile[0])) continue;
			$resizeFile = $tmpFile[0];
			$path = "";
			if (TL_FILES_URL != '')
			{
				$path = TL_FILES_URL . $GLOBALS['TL_CONFIG']['uploadPath'] . '/';
				$resizeFile = str_replace($path, "", $resizeFile);
			}
			if (file_exists(TL_ROOT .'/'. rawurldecode($resizeFile)))
			{
				$intMaxWidth = $GLOBALS['TL_CONFIG']['maxImageWidth'];
				$imgSize = @getimagesize(TL_ROOT .'/'. rawurldecode($resizeFile));
				if($imgSize[1] > $intMaxWidth || $imgSize[0] > $intMaxWidth)
				{
					// Adjust the image size
					$ratio = $imgSize[1] / $imgSize[0];

					$size[0] = $intMaxWidth;
					$size[1] = floor($intMaxWidth * $ratio);
					$src = \Image::get($resizeFile, $size[0], $size[1], '');
					switch ($type)
					{
						case "img":
							$replaceFile = str_replace( 'src="'.$tmpFile[0], 'src="'.$path.$src, $file );
							$replaceFile = str_replace( rawurldecode($tmpFile[0]), $path.$src, $replaceFile );
							$tmpWidth =  array_filter($fileMatches['width']);
							sort($tmpWidth);
							$replaceFile = str_replace( 'width="'.$tmpWidth[0], 'width="'.$size[0], $replaceFile );
							$tmpHeight =  array_filter($fileMatches['height']);
							sort($tmpHeight);
							$replaceFile = str_replace( 'height="'.$tmpHeight[0], 'height="'.$size[1], $replaceFile );
							break;
						case "link":
							$replaceFile = str_replace( 'href="'.$tmpFile[0], 'href="'.$path.$src, $file );
							$replaceFile = str_replace( rawurldecode($tmpFile[0]), $path.$src, $replaceFile );
							break;
						default:
							break;
					}
					$strContent = str_replace( $file, $replaceFile, $strContent );
				}
			}
		}
		return $strContent;
	}
}