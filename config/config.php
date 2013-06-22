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

/**
 * Backend Modules
 */
$GLOBALS['TL_HEAD'][] = "<script>document.cookie='resolution='+Math.max(screen.width,screen.height)+'; path=/';</script>";
$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('ResponsiveImages', 'overrideImageSize');
$GLOBALS['TL_HOOKS']['getCacheKey'][] = array('ResponsiveImages', 'overrideCacheKey');