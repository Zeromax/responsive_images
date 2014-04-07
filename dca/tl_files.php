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

// load language file
\Controller::loadLanguageFile('tl_responsive_images');

// palettes
$GLOBALS['TL_DCA']['tl_files']['palettes']['default'] = str_replace('meta', 'addBreakpoints,breakpoints;meta', $GLOBALS['TL_DCA']['tl_files']['palettes']['default']);

// Fields
$GLOBALS['TL_DCA']['tl_files']['fields']['addBreakpoints'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['addBreakpoints'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_files']['fields']['breakpoints'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['breakpoints'],
	'inputType'               => 'multiColumnWizard',
	'exclude'                 => true,
	'sql'                     => "blob NULL",
	'eval'                    => array
	(
		'columnFields' => array
		(
			'bp_breakpoint' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_breakpoint'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval'					=> array('style'=>'width:70px;margin-bottom:8px;', 'tl_class'=>'m12')
			),
			'bp_width' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_width'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval'                  => array('style'=>'margin-bottom:8px;', 'tl_class'=>'bp_width', 'rgxp'=>'digit')
			),
			'bp_height' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_height'],
				'exclude'               => true,
				'inputType'             => 'text',
				'eval'                  => array('style'=>'margin-bottom:8px;', 'tl_class'=>'bp_height', 'rgxp'=>'digit')
			),
			'bp_crop' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_crop'],
				'exclude'               => true,
				'inputType'             => 'select',
				'options'               => $GLOBALS['TL_CROP'],
				'reference'             => &$GLOBALS['TL_LANG']['MSC'],
				'eval'                  => array('style'=>'width:154px;margin-bottom:8px;')
			),
			'bp_ratio' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_ratio'],
				'exclude'               => true,
				'inputType'             => 'text',
				'reference'             => &$GLOBALS['TL_LANG']['MSC'],
				'eval'                  => array('style'=>'width:30px;margin-bottom:8px;', 'rgxp'=>'digit', 'maxlength'=>3),
			)
		)
	)
);