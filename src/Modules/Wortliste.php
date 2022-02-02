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

namespace Schachbulle\ContaoWortlisteBundle\Modules;

class Wortliste extends \Module
{

	var $Alphabetindex = array();
	var $Alphabetwerte = array();
	var $Ergebnis = array();

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

			$objTemplate->wildcard = '### WORTLISTE ###';
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

		// Alphabet initialisieren
		for($x = 65; $x <= 90; $x++) // A-Z
		{
			$this->Alphabetindex[$x] = chr($x);
			$this->Alphabetwerte[chr($x)] = $x;
		}
		$this->Alphabetindex[196] = chr(196); // Ä 142
		$this->Alphabetindex[214] = chr(214); // Ö 153
		$this->Alphabetindex[220] = chr(220); // Ü 154
		$this->Alphabetwerte[chr(196)] = 196; // Ä 142
		$this->Alphabetwerte[chr(214)] = 214; // Ö 153
		$this->Alphabetwerte[chr(220)] = 220; // Ü 154

		//self::Convert();
		//$this->Template->debug = $this->Alphabetwerte;
		$this->Template->form = self::Formular();

	}

	protected function Formular()
	{

		// Der 1. Parameter ist die Formular-ID (hier "linkform")
		// Der 2. Parameter ist GET oder POST
		// Der 3. Parameter ist eine Funktion, die entscheidet wann das Formular gesendet wird (Third is a callable that decides when your form is submitted)
		// Der optionale 4. Parameter legt fest, ob das ausgegebene Formular auf Tabellen basiert (true)
		// oder nicht (false) (You can pass an optional fourth parameter (true by default) to turn the form into a table based one)
		$objForm = new \Haste\Form\Form('wortlisteform', 'POST', function($objHaste)
		{
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});
		
		// URL für action festlegen. Standard ist die Seite auf der das Formular eingebunden ist.
		// $objForm->setFormActionFromUri();
		
		$objForm->addFormField('buchstaben', array(
			'label'         => 'Buchstaben',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false, 'class'=>'form-control')
		));
		$objForm->addFormField('joker', array(
			'label'         => 'Jokerbuchstaben',
			'inputType'     => 'select',
			'selected'      => '0',
			'options'       => array('0' => '0', '1' => '1', '2' => '2'),
			'eval'          => array('mandatory'=>false, 'choosen'=>false, 'class'=>'form-control')
		));
		$objForm->addFormField('wort', array(
			'label'         => 'Wort das enthalten sein muß (* für einen der obigen Buchstaben)',
			'inputType'     => 'text',
			'eval'          => array('mandatory'=>false, 'class'=>'form-control')
		));
		// Submit-Button hinzufügen
		$objForm->addFormField('submit', array(
			'label'         => 'Absenden',
			'inputType'     => 'submit',
			'eval'          => array('class'=>'btn btn-primary')
		));
		$objForm->addCaptchaFormField('captcha');
		// Ausgeblendete Felder FORM_SUBMIT und REQUEST_TOKEN automatisch hinzufügen.
		// Nicht verwenden wenn generate() anschließend verwendet, da diese Felder dort standardmäßig bereitgestellt werden.
		// $objForm->addContaoHiddenFields();
		
		// validate() prüft auch, ob das Formular gesendet wurde
		if($objForm->validate())
		{
			// Alle gesendeten und analysierten Daten holen (funktioniert nur mit POST)
			$arrData = $objForm->fetchAll();
			self::Search($arrData); // Suche starten
			// Seite neu laden
			//\Controller::addToUrl('send=1'); // Hat keine Auswirkung, verhindert aber das das Formular ausgefüllt ist
			//\Controller::reload(); 
		}
		
		// Formular als String zurückgeben
		return $objForm->generate();

	}

	protected function Search($data)
	{
		$buchstaben = str_replace(array('ä', 'ö', 'ü'), array('Ä', 'Ö', 'Ü'), strtoupper($data['buchstaben']));
		$joker = $data['joker'];
		$wort = strtoupper($data['wort']);

		$this->Template->Buchstaben = $buchstaben;
		$this->Template->BuchstabenASCII = self::WortToASCII($buchstaben);

		if($buchstaben && !$joker && !$wort)
		{
			// Buchstaben übergeben, kein Joker, kein Vorgabewort
			// Zuerst SQL-Parameter anhand der Buchstaben zusammenbauen
			$sql = self::BaueBuchstabenAusschliessen($buchstaben);
			$objResult = \Database::getInstance()->prepare('SELECT * FROM tl_wortliste WHERE published = ? AND '.$sql.' ORDER BY wert DESC, anzahl DESC')
			                                     ->execute(1);
			self::Erfassen($objResult, $wort);
		}
		elseif($buchstaben && $joker == 1 && !$wort)
		{
			// Buchstaben übergeben, Joker ja, kein Vorgabewort

			foreach($this->Alphabetindex as $key => $value)
			{
				// Zuerst SQL-Parameter anhand der Buchstaben zusammenbauen
				$sql = self::BaueBuchstabenAusschliessen($value.$buchstaben);
				$objResult = \Database::getInstance()->prepare('SELECT * FROM tl_wortliste WHERE published = ? AND '.$sql.' ORDER BY wert DESC, anzahl DESC')
				                                     ->execute(1);
				self::Erfassen($objResult, $wort, $value);
			}

		}
		elseif(!$buchstaben && !$joker && $wort)
		{
			$objResult = \Database::getInstance()->prepare('SELECT * FROM tl_wortliste WHERE published = ? AND wort LIKE ? ORDER BY wort ASC')
			                                     ->execute(1, '%'.$wort.'%');
			self::Erfassen($objResult, $wort);
		}
		else
		{
			$objResult = \Database::getInstance()->prepare('SELECT * FROM tl_wortliste WHERE published = ?')
			                                     ->execute(1);
			self::Erfassen($objResult);
		}

		// Verbandsliste neu sortieren, vorher das Objekt mittels json in ein Array umwandeln
		$sorted = \Schachbulle\ContaoHelperBundle\Classes\Helper::sortArrayByFields(
			$this->Ergebnis,
			array(
				'wortwert'    => SORT_DESC,
				'anzahl'      => SORT_DESC
			)
		);

		$this->Template->result = $sorted;
	}

	public function Erfassen($objekt, $wort, $joker = false)
	{
		if($objekt->numRows)
		{
			// Ausgabe
			while($objekt->next())
			{
				$this->Ergebnis[] = array
				(
					'id'           => $objekt->id,
					'ergebniswort' => $objekt->wort,
					'anzeigewort'  => '<b>'.self::Hervorheben($wort, $joker, $objekt->wort).'</b>',
					'asciiwort'    => self::WortToASCII($objekt->wort),
					'wortwert'     => $objekt->wert,
					'anzahl'       => $objekt->anzahl,
				);
			}
		}
	}

	public function BaueBuchstabenAusschliessen($buchstaben)
	{
		// Zuerst Buchstaben in den vorgegebenen Buchstaben zählen
		//$buchstaben = utf8_decode($buchstaben);
		$Zaehler = array();
		for($x = 65; $x <= 90; $x++) // A-Z
		{
			$Zaehler[$x] = self::Anzahl(chr($x), $buchstaben);
		}
		$Zaehler[196] = self::Anzahl('Ä', $buchstaben); // Ä 142
		$Zaehler[214] = self::Anzahl('Ö', $buchstaben); // Ö 153
		$Zaehler[220] = self::Anzahl('Ü', $buchstaben); // Ü 154

		// SQL-Abfrage bauen
		foreach($Zaehler as $key => $value)
		{
			if($value == 0)
			{
				$sql[] = 'char_'.$key.' = 0';
			}
			else
			{
				$sql[] = 'char_'.$key.' <= '.$value;
			}
		}

		$string = implode(' AND ', $sql);
		//$this->Template->SQL = $string;
		return $string;
	}

	public function Hervorheben($hervorheben, $hervorheben2, $wort)
	{
		$string = str_replace($hervorheben, '<span style="color:red;"><b>'.$hervorheben.'</b></span>', $wort);
		$string = str_replace($hervorheben2, '<span style="color:red;"><b>'.$hervorheben2.'</b></span>', $string);
		return $string;
	}

	public function WortToASCII($wort)
	{
		$wort = utf8_decode($wort);
		$ascii = '';
		for($x = 0; $x < strlen($wort); $x++)
		{
			$ascii .= ord(substr($wort, $x, 1)). ' ';
		}
		return trim($ascii);
	}

	public function Anzahl($buchstabe, $wort)
	{
		$wort = utf8_decode($wort);
		$buchstabe = utf8_decode($buchstabe);
		return substr_count($wort, $buchstabe);
	}

}
