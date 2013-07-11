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

$GLOBALS['TL_HOOKS']['initializeSystem'][] = array('ResponsiveImages', 'setCookie');
$GLOBALS['TL_HOOKS']['modifyFrontendPage'][] = array('ResponsiveImages', 'replaceImages');
$GLOBALS['TL_HOOKS']['getCacheKey'][] = array('ResponsiveImages', 'overrideCacheKey');