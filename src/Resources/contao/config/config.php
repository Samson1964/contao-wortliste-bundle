<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   bdf
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

$GLOBALS['BE_MOD']['content']['schiedsrichter'] = array
(
	'tables'         => array('tl_wortliste'),
	'icon'           => 'bundles/contaowortliste/images/icon.png',
	'import'         => array('Schachbulle\ContaoWortlisteBundle\Classes\Import', 'run'),
);

/**
 * Frontend-Module
 */

$GLOBALS['FE_MOD']['application']['wortliste'] = 'Schachbulle\ContaoWortlisteBundle\Modules\Wortliste';
