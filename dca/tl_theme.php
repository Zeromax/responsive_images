<?php

/**
 * responsive_images
 *
 * Copyright (C) 2013 Andreas Nölke
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013-2014
 * @license LGPL-3.0+
 */

/**
 * Table tl_theme
 */
//layout
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = "tl_responsive_images";
array_insert($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], array_search('layout',array_keys($GLOBALS['TL_DCA']['tl_theme']['list']['operations']))+1, array
(
	'responsive_images' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_theme']['responsive_images'],
		'href'                => 'table=tl_responsive_images',
		'icon'                => 'system/modules/responsive_images/assets/images.png',
		'button_callback'     => array('tl_responsive_images_theme', 'editResponsiveImages')
	)
));


/**
 * Class tl_responsive_images_theme
 *
 * @package   tl_responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013-2014
 */
class tl_responsive_images_theme extends tl_theme
{

	/**
	 * Return the edit page layouts button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function editResponsiveImages($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('responsive_images', 'themes')) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.png/i', '_.png', $icon)).' ';
	}
}

?>