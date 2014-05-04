<?php

/**
 * responsive_images
 *
 * Copyright (C) 2013 - 2014 Andreas Nölke
 *
 * @package   responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013 - 2014
 * @license LGPL-3.0+
 */

/**
 * Table tl_responsive_images
 */
$GLOBALS['TL_DCA']['tl_responsive_images'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_theme',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('module'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('module', 'singleSRC'),
			'format'                  => '%s <span style="color:#b3b3b3;padding-left:3px">[%s]</span>',
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_responsive_images']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_responsive_images']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_responsive_images']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_responsive_images']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_responsive_images', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_responsive_images']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('addBreakpoints'),
		'default'                     => '{layout_legend},module;{breakpoint_legend},addBreakpoints;{fields_legend},singleSRC,alt,altMandatory,title,titleMandatory,size,sizeMandatory,imagemargin,imagemarginMandatory,imageUrl,imageUrlMandatory,fullsize,fullsizeMandatory,caption,captionMandatory,floating,floatingMandatory;{settings_legend},articleTpl,invisible',
	),
	'subpalettes' => array(
		'addBreakpoints'			  => 'breakpoints'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'module' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['module'],
			'filter'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true),
//			'save_callback'			  => array(array('tl_responsive_images', 'checkUniqueModule')),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'addBreakpoints' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['addBreakpoints'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'breakpoints' => array
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
						'eval'					=> array('style'=>'width:70px;margin-bottom:8px;', 'tl_class'=>'m12', 'mandatory'=>true, )
					),
					'bp_width' => array
					(
						'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_width'],
						'exclude'               => true,
						'inputType'             => 'text',
						'eval'                  => array('style'=>'margin-bottom:8px;', 'tl_class'=>'bp_width', 'rgxp'=>'digit'),
					),
					'bp_height' => array
					(
						'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_height'],
						'exclude'               => true,
						'inputType'             => 'text',
						'eval'                  => array('style'=>'margin-bottom:8px;', 'tl_class'=>'bp_height', 'rgxp'=>'digit'),
					),
					'bp_crop' => array
					(
						'label'                 => &$GLOBALS['TL_LANG']['tl_responsive_images']['bp_crop'],
						'exclude'               => true,
						'inputType'             => 'select',
						'options'               => $GLOBALS['TL_CROP'],
						'reference'             => &$GLOBALS['TL_LANG']['MSC'],
						'eval'                  => array('style'=>'width:154px;margin-bottom:8px;'),
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
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['singleSRC'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'clr', 'mandatory'=>true),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['alt'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'altMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['altMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['title'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'titleMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['titleMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'size' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['size'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'sizeMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['sizeMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'imagemargin' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['imagemargin'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'imagemarginMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['imagemarginMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'imageUrl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['imageUrl'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'imageUrlMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['imageUrlMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'fullsize' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['fullsize'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'fullsizeMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['fullsizeMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'caption' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['caption'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'captionMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['captionMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'floating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['floating'],
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'floatingMandatory' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['floatingMandatory'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'invisible' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_responsive_images']['invisible'],
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		)
	)
);

/**
 * Class tl_responsive_images
 *
 * @package   tl_responsive_images
 * @author    Andreas Nölke
 * @copyright Andreas Nölke 2013-2014
 */
class tl_responsive_images extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
		\System::loadLanguageFile('tl_content');
	}

	/**
	 * Check if module / element is already in use
	 *
	 * @param mixed
	 * @param DataContainer
	 * @return string
	 */
	public function checkUniqueModule($varValue, DataContainer $dc)
	{
		$objModule = $this->Database->prepare("SELECT id FROM tl_responsive_images WHERE module=? AND pid=?")
			->execute($varValue, $dc->activeRecord->pid);

		// Check whether the page alias exists
		if ($objModule->numRows > 1)
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['moduleExists'], $varValue));
		}

		return $varValue;
	}

	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$tid = Input::get('tid');
		$state = Input::get('state');
		$id = Input::get('id');

		if (strlen($tid))
		{
			$this->toggleVisibility($tid, ($state == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_responsive_images::invisible', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id=' . $id . '&amp;tid=' . $row['id'] . '&amp;state=' . $row['invisible'];

		if ($row['invisible'])
		{
			$icon = 'invisible.gif';
		}
		$image = "";
		if (version_compare(VERSION, '3.1', '>='))
		{
			$image = Image::getHtml($icon, $label);
		}
		else
		{
			$image = $this->generateImage($icon, $label);
		}
		return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $image . '</a> ';
	}

	/**
	 * Toggle the visibility of an element
	 *
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnVisible)
	{
		// Check permissions to edit
		Input::setGet('id', $intId);
		Input::setGet('act', 'toggle');

		// The onload_callbacks vary depending on the dynamic parent table (see #4894)
		if (is_array($GLOBALS['TL_DCA']['tl_responsive_images']['config']['onload_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_responsive_images']['config']['onload_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$this->$callback[0]->$callback[1]($this);
				}
			}
		}

		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_responsive_images::invisible', 'alexf'))
		{
			$this->log('Not enough permissions to show/hide content element ID "' . $intId . '"', 'tl_responsive_images toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_responsive_images', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_responsive_images']['fields']['invisible']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_responsive_images']['fields']['invisible']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_responsive_images SET tstamp=" . time() . ", invisible='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
			->execute($intId);

		$objVersions->create();

		$this->log('A new version of record "tl_responsive_images.id=' . $intId . '" has been created', 'tl_responsive_images toggleVisibility()', TL_GENERAL);
	}

}
