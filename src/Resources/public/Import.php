<?php
//ini_set('display_errors', '1');
//set_time_limit(0);

/**
 * Contao Open Source CMS, Copyright (C) 2005-2013 Leo Feyer
 *
 */

/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
define('TL_SCRIPT', 'bundles/contaowortliste/Import.php');
require($_SERVER['DOCUMENT_ROOT'].'/../system/initialize.php');

/**
 * Run in a custom namespace, so the class can be replaced
 */
use Contao\Controller;

/**
 * Klasse Import
 * ============================================================================================
 * Import die Schiedsrichter-Daten aus der Tabelle sr-person der Datenbank des Ergebnisdienstes
 *
 */
class Import
{
	public function __construct()
	{
	}

	public function run()
	{

	}

}

/**
 * Instantiate controller
 */
$objClick = new Import();
$objClick->run();
