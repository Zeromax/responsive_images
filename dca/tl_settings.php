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
 * System configuration
 */

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('maxImageWidth', 'breakPoints,mobileWidthFallback', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_settings']['fields']['breakPoints'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['breakPoints'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['mobileWidthFallback'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['mobileWidthFallback'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50', 'rgxp'=>'digit')
);