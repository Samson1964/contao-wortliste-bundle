<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   DeWIS
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

namespace Schachbulle\ContaoWortlisteBundle\Module;

class Wortliste extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_wortliste';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### SCHIEDSRICHTERLISTE ###';
			$objTemplate->title = $this->name;
			$objTemplate->id = $this->id;

			return $objTemplate->parse();
		}

		return parent::generate(); // Weitermachen mit dem Modul
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$ausbildungsdatum = strtotime('-5 years');
		$pruefungsdatum = strtotime('-10 years');

		$objDB = \Database::getInstance()->prepare('SELECT * FROM tl_wortliste WHERE published = ? AND klasse <> ? AND (prue_datum = ? OR prue_datum > ?) AND (ausbdat = ? OR ausbdat > ?) ORDER BY name ASC, vorname ASC')
		                                 ->execute(1, '', 0, $pruefungsdatum, 0, $ausbildungsdatum);

		$daten = array();
		if($objDB->numRows)
		{
			while($objDB->next())
			{
				$daten[] = array
				(
					'nachname' => $objDB->name,
					'vorname'  => $objDB->vorname,
					'ort'      => $objDB->ort,
					'lizenz'   => $objDB->klasse,
					'datum'    => $objDB->edited ? date('d.m.Y', $objDB->edited) : '',
				);
			}
		}
		$this->Template->daten = $daten;

	}

}
