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

$GLOBALS['TL_DCA']['tl_files']['palettes']['default'] = str_replace('meta', 'breakPointCropping;meta', $GLOBALS['TL_DCA']['tl_files']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_files']['fields']['breakPointCropping'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_files']['breakPointCropping'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);