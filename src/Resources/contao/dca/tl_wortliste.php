<?php

/**
* Contao Open Source CMS
*
* Copyright (c) 2005-2014 Leo Feyer
*
 */


/**
 * Table tl_wortliste 
 */
$GLOBALS['TL_DCA']['tl_wortliste'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'switchToEdit'                => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'             => 'primary',
				'wort'           => 'index',
				'wort'           => 'unique',
				'char_65'        => 'index', // A
				'char_66'        => 'index', // B
				'char_67'        => 'index', // C
				'char_68'        => 'index', // D
				'char_69'        => 'index', // E
				'char_70'        => 'index', // F
				'char_71'        => 'index', // G
				'char_72'        => 'index', // H
				'char_73'        => 'index', // I
				'char_74'        => 'index', // J
				'char_75'        => 'index', // K
				'char_76'        => 'index', // L
				'char_77'        => 'index', // M
				'char_78'        => 'index', // N
				'char_79'        => 'index', // O
				'char_80'        => 'index', // P
				'char_81'        => 'index', // Q
				'char_82'        => 'index', // R
				'char_83'        => 'index', // S
				'char_84'        => 'index', // T
				'char_85'        => 'index', // U
				'char_86'        => 'index', // V
				'char_87'        => 'index', // W
				'char_88'        => 'index', // X
				'char_89'        => 'index', // Y
				'char_90'        => 'index', // Z
				'char_196'       => 'index', // Ä
				'char_214'       => 'index', // Ö
				'char_220'       => 'index', // Ü
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('wort ASC'),
			'flag'                    => 11,
			'panelLayout'             => 'filter;search,sort,limit',
		),
		'label' => array
		(
			'fields'                  => array('wort'),
			'showColumns'             => true
		),
		'global_operations' => array
		(
			'import' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wortliste']['import'],
				'href'                => 'key=import',
				'icon'                => 'bundles/contaowortliste/images/import.png'
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wortliste']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wortliste']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wortliste']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wortliste']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_wortliste', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_wortliste']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),
	// Palettes
	'palettes' => array
	(
		'default'                     => '{wort_legend},wort,anzahl;{publish_legend},published'
	),

	// Base fields in table tl_wortliste
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Wort
		'wort' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wortliste']['wort'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => false,
			'load_callback'           => array(array('tl_wortliste','loadWord')),
			'save_callback'           => array(array('tl_wortliste','saveWord')),
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 60,
				'nospace'             => true,
				'rgxp'                => 'alpha',
				'unique'              => true,
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(60) NOT NULL default ''",
		),
		'anzahl' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wortliste']['anzahl'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => false,
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 3,
				'nospace'             => true,
				'rgxp'                => 'digit',
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(3) unsigned NOT NULL default '0'"
		),
		'wert' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wortliste']['wert'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => false,
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 4,
				'nospace'             => true,
				'rgxp'                => 'digit',
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_wortliste']['published'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => true,
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		// Anzahl der Buchstaben im Wort
		'char_65' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // A
		'char_66' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // B
		'char_67' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // C
		'char_68' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // D
		'char_69' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // E
		'char_70' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // F
		'char_71' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // G
		'char_72' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // H
		'char_73' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // I
		'char_74' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // J
		'char_75' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // K
		'char_76' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // L
		'char_77' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // M
		'char_78' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // N
		'char_79' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // O
		'char_80' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // P
		'char_81' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // Q
		'char_82' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // R
		'char_83' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // S
		'char_84' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // T
		'char_85' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // U
		'char_86' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // V
		'char_87' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // W
		'char_88' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // X
		'char_89' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // Y
		'char_90' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // Z
		'char_196' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // Ä
		'char_214' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // Ö
		'char_220' => array('sql'      => "int(1) unsigned NOT NULL default 0"), // Ü
	),
);

class tl_wortliste extends \Backend
{

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_wortliste::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}

	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_wortliste::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_wortliste toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
		
		$this->createInitialVersion('tl_wortliste', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_wortliste']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_wortliste']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_wortliste SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_wortliste', $intId);
	}

	public function loadWord($varValue, DataContainer $dc)
	{
		return $varValue;
	}

	public function saveWord($varValue, DataContainer $dc)
	{
		$varValue = str_replace(array('.', '-', 'ä', 'ö', 'ü', 'ß'), array('', '', 'Ä', 'Ö', 'Ü', 'SS'), $varValue);
		$varValue = strtoupper($varValue);
		
		return $varValue;
	}

}