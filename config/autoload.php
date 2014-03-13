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
 * Register the namespace
 */
ClassLoader::addNamespace('ResponsiveImages');

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'ResponsiveImages\PictureFill'				=> 'system/modules/responsive_images/classes/PictureFill.php',

	// Classes
	'ResponsiveImages\ResponsiveImagesModel'	=> 'system/modules/responsive_images/model/ResponsiveImagesModel.php'
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_accordion'      => 'system/modules/responsive_images/templates/elements',
	'ce_image'			=> 'system/modules/responsive_images/templates/elements',
	'ce_text'			=> 'system/modules/responsive_images/templates/elements',
));
