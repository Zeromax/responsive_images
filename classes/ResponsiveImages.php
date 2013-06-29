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
	 * Hook to override the max Image Width global
	 * @param \Template $objTemplate
	 */
	public function overrideImageSize($objTemplate)
	{
		if (TL_MODE == "FE")
		{
			$GLOBALS['TL_CONFIG']['maxImageWidth'] = self::getBreakpoint();
			$GLOBALS['TL_CONFIG']['elementToResizeImg'][] = array ('ce_text', 'text');

			if($GLOBALS['TL_CONFIG']['maxImageWidth'] > 0 && isset($GLOBALS['TL_CONFIG']['elementToResizeImg']) && is_array($GLOBALS['TL_CONFIG']['elementToResizeImg']))
			{
				foreach ($GLOBALS['TL_CONFIG']['elementToResizeImg'] as $element)
				{
					if($objTemplate->getName() == $element[0])
					{
						switch ($element[1])
						{
							case 'text':
								// pattern for IMG Tags
								$pattern = '/\<img[a-z0-9%#:&;\/\.\_\-\="\s]*>/i';
								$subject = $element[1];
								$matches = "";
								preg_match_all($pattern, $objTemplate->$subject, $matches);

								// Replace each file if necessary
								foreach($matches[0] as $file)
								{
									// Pattern for height| width | src
									$imgPattern = '/(src="(?<file>(files|tl_files)[a-z0-9%\/\s\.\-\_]{0,250})")|(width="(?<width>[0-9]{1,9})")|(height="(?<height>[0-9]{1,9})")/i';
									$fileMatches = "";
									preg_match_all($imgPattern, $file, $fileMatches);

									$tmpFile =  array_filter($fileMatches['file']);
									sort($tmpFile);
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
											$replaceFile = str_replace( 'src="'.$tmpFile[0], 'src="'.$path.$src, $file );
											$replaceFile = str_replace( rawurldecode($tmpFile[0]), $path.$src, $replaceFile );
											$tmpWidth =  array_filter($fileMatches['width']);
											sort($tmpWidth);
											$replaceFile = str_replace( 'width="'.$tmpWidth[0], 'width="'.$size[0], $replaceFile );
											$tmpHeight =  array_filter($fileMatches['height']);
											sort($tmpHeight);
											$replaceFile = str_replace( 'height="'.$tmpHeight[0], 'height="'.$size[1], $replaceFile );

											$objTemplate->$subject = str_replace( $file, $replaceFile, $objTemplate->$subject );
										}
									}
								}
								break;
							default:
								break;
						}
					}
				}
			}
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
				if($clientWidth <= $width) {
					return $width;
				}
			}

		}
		return 0;
	}
}