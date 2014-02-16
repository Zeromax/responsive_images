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
	'ResponsiveImages\PictureFill' => 'system/modules/responsive_images/classes/PictureFill.php'
));
