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

		try
		{
			//// Tabelle sr-person der Ergebnisdienst-Datenbank auslesen
			$objImportDB = \Database::getInstance(array
			(
				'dbHost'     => $GLOBALS['TL_CONFIG']['schiedsrichter_host'],
				'dbUser'     => $GLOBALS['TL_CONFIG']['schiedsrichter_user'],
				'dbPass'     => $GLOBALS['TL_CONFIG']['schiedsrichter_pass'],
				'dbDatabase' => $GLOBALS['TL_CONFIG']['schiedsrichter_db']
			));

			$objSchiedsrichter = $objImportDB->prepare('SELECT * FROM `sr-person`')
			                                 ->execute();

			// Alle alten Datensätze auf unveröffentlicht setzen
			$objDB = \Database::getInstance()->prepare('UPDATE tl_schiedsrichter SET published = ?')
			                                 ->execute(0);

			// Import beginnen
			if($objSchiedsrichter->numRows)
			{
				$zeit = time();
				while($objSchiedsrichter->next())
				{
					echo $objSchiedsrichter->Nachname.','.$objSchiedsrichter->Vorname;
					$found = false;
					// Suchen nach PKZ
					if($objSchiedsrichter->PKZ)
					{
						$objDB = \Database::getInstance()->prepare('SELECT * FROM tl_schiedsrichter WHERE pkz = ?')
						                                 ->limit(1)
						                                 ->execute($objSchiedsrichter->PKZ);
						if($objDB->numRows)
						{
							echo ' PKZ '.$objSchiedsrichter->PKZ.' gefunden';
							$found = true;
						}
					}
					// Noch nichts gefunden, deshalb Suchen nach FIDE-ID
					if($objSchiedsrichter->FIDE_ID && !$found)
					{
						$objDB = \Database::getInstance()->prepare('SELECT * FROM tl_schiedsrichter WHERE fide_id = ?')
						                                 ->limit(1)
						                                 ->execute($objSchiedsrichter->FIDE_ID);
						if($objDB->numRows)
						{
							echo ' FIDE-ID '.$objSchiedsrichter->FIDE_ID.' gefunden';
							$found = true;
						}
					}
					// Noch nichts gefunden, deshalb Suchen nach Lizenznummer
					if($objSchiedsrichter->Lizenz_Nr && !$found)
					{
						$objDB = \Database::getInstance()->prepare('SELECT * FROM tl_schiedsrichter WHERE nr = ?')
						                                 ->limit(1)
						                                 ->execute($objSchiedsrichter->Lizenz_Nr);
						if($objDB->numRows)
						{
							echo ' Lizenz-Nr. '.$objSchiedsrichter->Lizenz_Nr.' gefunden';
							$found = true;
						}
					}
                    
                    // Datensatz erstellen
					$set = array
					(
						'tstamp'     => $zeit,
						'edited'     => strtotime($objSchiedsrichter->Geaendert),
						'klasse'     => $objSchiedsrichter->Lizenz,
						'nr'         => $objSchiedsrichter->Lizenz_Nr,
						'anrede'     => $objSchiedsrichter->Anrede,
						'titel'      => $objSchiedsrichter->Titel,
						'name'       => $objSchiedsrichter->Nachname,
						'vorname'    => $objSchiedsrichter->Vorname,
						'strasse'    => $objSchiedsrichter->Strasse,
						'ort'        => $objSchiedsrichter->Ort,
						'plz'        => $objSchiedsrichter->PLZ,
						'telefon'    => $objSchiedsrichter->Telefon1,
						'telefon2'   => $objSchiedsrichter->Telefon2,
						'email'      => $objSchiedsrichter->E_Mail1,
						'ausbdat'    => strtotime($objSchiedsrichter->Ausbildungsdatum),
						'pkz'        => $objSchiedsrichter->PKZ,
						'rds_d'      => strtotime($objSchiedsrichter->RDS_D),
						'rds_k'      => $objSchiedsrichter->RDS_K,
						'dwz_d'      => strtotime($objSchiedsrichter->DWZ_D),
						'dwz_k'      => $objSchiedsrichter->DWZ_K,
						'verein_kur' => $objSchiedsrichter->Kuerzel,
						'prue_datum' => strtotime($objSchiedsrichter->Pruefungsdatum),
						'sel'        => $objSchiedsrichter->Selektion,
						'fide_id'    => $objSchiedsrichter->FIDE_ID,
						'country'    => $objSchiedsrichter->Land,
						'bemerkung'  => $objSchiedsrichter->Kommentar,
						'published'  => $objSchiedsrichter->Status == 'Aktiv' ? '1' : '0',
					);

					// Datensatz aktualisieren bzw. neu aufnehmen
					if($found)
					{
						// Datensatz aktualisieren
						$objDB = \Database::getInstance()->prepare('UPDATE tl_schiedsrichter %s WHERE id = ?')
						                                 ->set($set)
						                                 ->execute($objDB->id);
						echo ' ... aktualisiert';
					}
					else
					{
						// Neuen Datensatz anlegen
						$objDB = \Database::getInstance()->prepare('INSERT INTO tl_schiedsrichter %s')
						                                 ->set($set)
						                                 ->execute();
						echo ' ... neu erstellt';
					}
					echo '<br>';
				}
			}

			echo '<br>Fertig!';
		}


		catch(Exception $ex)
		{
			print_r($ex);
		}
	}

}

/**
 * Instantiate controller
 */
$objClick = new Import();
$objClick->run();
