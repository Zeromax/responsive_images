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

if (TL_MODE == "FE")
{
	/*
	 * add needed picturefill JavaScript
	 */
	$GLOBALS['TL_JAVASCRIPT'][] = "system/modules/responsive_images/assets/picturefill-1.2.1/matchmedia.js|static";
	$GLOBALS['TL_JAVASCRIPT'][] = "system/modules/responsive_images/assets/picturefill-1.2.1/picturefill.js|static";

	/**
	 * add hooks for creating smaller images
	 */
	$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('PictureFill', 'parseTemplate');

	$GLOBALS['TL_CONFIG']['hasImage'] = array(
		// singleSRC,alt,title,size,imagemargin,imageUrl,fullsize,caption,floating
		'text' => array(
				'singleSRC'		=> 'singleSRC',
				'alt'			=> 'alt',
				'title'			=> 'title',
				'size'			=> 'size',
				'imagemargin'	=> 'imagemargin',
				'imageUrl'		=> 'imageUrl',
				'fullsize'		=> 'fullsize',
				'caption'		=> 'caption',
				'floating'		=> 'floating',
				'mandatory'		=> array('singleSRC', 'addImage')
		),
		'accordionSingle' => array(
				'singleSRC'			=> 'singleSRC',
				'alt'			=> 'alt',
				'title'			=> 'title',
				'size'			=> 'size',
				'imagemargin'	=> 'imagemargin',
				'imageUrl'		=> 'imageUrl',
				'fullsize'		=> 'fullsize',
				'caption'		=> 'caption',
				'floating'		=> 'floating',
				'mandatory'		=> array('singleSRC', 'addImage')
		),
		// singleSRC,alt,title,size,imagemargin,imageUrl,fullsize,caption
		'image' => array(
				'singleSRC'		=> 'singleSRC',
				'alt'			=> 'alt',
				'title'			=> 'title',
				'size'			=> 'size',
				'imagemargin'	=> 'imagemargin',
				'imageUrl'		=> 'imageUrl',
				'fullsize'		=> 'fullsize',
				'caption'		=> 'caption',
				'mandatory'		=> 'singleSRC'
		),
	);
}