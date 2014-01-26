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
// add needed picturefill JavaScript for the Frontend
if (TL_MODE == "FE")
{
	$GLOBALS['TL_JAVASCRIPT'][] = "system/modules/responsive_images/assets/picturefill-1.2.1/matchmedia.js|static";
	$GLOBALS['TL_JAVASCRIPT'][] = "system/modules/responsive_images/assets/picturefill-1.2.1/picturefill.js|static";
}