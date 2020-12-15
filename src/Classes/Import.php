<?php

namespace Schachbulle\ContaoWortlisteBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Class dsb_trainerlizenzImport
  */
class Import extends \Backend
{

	function __construct()
	{
	}

	/**
	 * Exportiert alle noch nicht übertragenen Lizenzen zum DOSB
	 */
	public function run()
	{

		// jQuery einbinden
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaowortliste/js/jquery-3.5.1.min.js';

		$content .= '<div id="rating_import" style="margin:10px;"></div>';
		$content .= '<div id="rating_import_status" style="margin:10px;"><img src="bundles/contaowortliste/images/ajax-loader.gif"></div>';

		// Zurücklink generieren
		$backlink = str_replace('&key=import', '', \Environment::get('request'));
		$content .= '<div style="margin:10px;"><a href="'.$backlink.'">Zurück</a> | Alternativ: <a href="bundles/contaowortliste/Import.php" target="_blank">Import.php im neuen Fenster ausführen</div>';

		$content .= '<script>'."\n";
		$content .= '$.ajax({'."\n";
		$content .= '  url: "bundles/contaowortliste/Import.php",'."\n";
		$content .= '  cache: false,'."\n";
		$content .= '  success: function(response) {'."\n";
		$content .= '    $("#rating_import").append(response);'."\n";
		$content .= '    $("#rating_import_status").html("");'."\n";
		$content .= '  }'."\n";
		$content .= '});'."\n";
		$content .= '</script>'."\n";

		return $content;

	}
}
