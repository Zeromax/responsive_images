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

$GLOBALS['BE_MOD']['design']['themes']['tables'][] = "tl_responsive_images";

// Support extension easy_themes
$GLOBALS['TL_EASY_THEMES_MODULES']['responsive_images'] = array
(
	'label'         => $GLOBALS['TL_LANG']['tl_theme']['responsive_images'][0],
	'title'         => $GLOBALS['TL_LANG']['tl_theme']['responsive_images'][1],
	'href_fragment' => 'table=tl_responsive_images',
	'icon'          => 'system/modules/responsive_images/assets/image.png'
);

// only add this for the front end
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
}
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
			'addImage'		=> 'addImage',
			'mandatory'		=> array('singleSRC', 'addImage')
	),
	'accordionSingle' => array(
			'singleSRC'		=> 'singleSRC',
			'alt'			=> 'alt',
			'title'			=> 'title',
			'size'			=> 'size',
			'imagemargin'	=> 'imagemargin',
			'imageUrl'		=> 'imageUrl',
			'fullsize'		=> 'fullsize',
			'caption'		=> 'caption',
			'floating'		=> 'floating',
			'addImage'		=> 'addImage',
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
			'mandatory'		=> array('singleSRC')
	),
);