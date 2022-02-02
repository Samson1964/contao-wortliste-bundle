<?php

class WordWithFriends
{
	var $Brett = array(); // Virtuelles zweidimensionales Brett mit den Buchstaben
	var $Feldwert = array(); // Virtuelles zweidimensionales Brett mit den Feldwerten
	var $Buchstabenwert = array(); // Enthält die Werte der Buchstaben
	var $Alphabet = array(); // Enthält alle möglichen Buchstaben
	
	var $Ergebnis = array(); // Nimmt alle Ergebnisse der Punktermittlung auf

	var $Spielfeld = array(); // Spielfeld aus dem Formular mit den Buchstaben

	var $Wortliste = array();
	
	var $WortlisteZaehler = array();
	var $Zeitmessungen = array();	
	var $Umlaute = array();

	var $SucheBuchstaben;
	var $ZaehlerSucheBuchstaben = array();
	var $db;
	
	function __construct($spielfeld)
	{
		$this->Spielfeld = $spielfeld;

		$this->db = mysqli_connect('mysql5.webagentur-hoppe.de', 'db71837_20', 'hnKufyms6xr', 'db71837_20');
		if(!$this->db)
		{
			exit("Verbindungsfehler: ".mysqli_connect_error());
		}

		// Buchstabenwerte setzen
		$this->Buchstabenwert = array
		(
			'A' => 1, // ok
			'B' => 3, // ok
			'C' => 4, // ok
			'D' => 2, // ok
			'E' => 1, // ok
			'F' => 4, // ok
			'G' => 3, // ok
			'H' => 2, // ok
			'I' => 1, // ok
			'J' => 8, // ok
			'K' => 4, // ok
			'L' => 3, // ok
			'M' => 3, // ok
			'N' => 1, // ok
			'O' => 2, // ok
			'P' => 4, // ok
			'Q' => 10, // ok
			'R' => 1, // ok
			'S' => 1, // ok
			'T' => 1, // ok
			'U' => 1, // ok
			'V' => 6, // ok
			'W' => 6, // ok
			'X' => 10, // ok
			'Y' => 10, // ok
			'Z' => 6, // ok
			'Ä' => 6, // ok
			'Ö' => 6, // ok
			'Ü' => 6  // ok
		);

		// Alphabet initialisieren
		for($x = 65; $x <= 90; $x++)
		{
			$this->Alphabet[] = chr($x);
		}
		$this->Alphabet[] = 'Ä';
		$this->Alphabet[] = 'Ö';
		$this->Alphabet[] = 'Ü';

		self::InitBrett();
		
	}

	function InitBrett()
	{
		$this->Brett = array();
		$this->Feldwert = array();
		$index = 1;
		for($zeile = 1; $zeile <= 15; $zeile++)
		{
			for($spalte = 1; $spalte <= 15; $spalte++)
			{
				$this->Brett[$zeile][$spalte] = $this->Spielfeld['felder'][$index]; // Buchstabe auf das Brett setzen
				// Feldwerte eintragen
				switch($zeile * 100 + $spalte)
				{
					case 104:
					case 112:
					case 401:
					case 415:
					case 1201:
					case 1215:
					case 1504:
					case 1512:
						$this->Feldwert[$zeile][$spalte] = '3W';
						break;
					case 206:
					case 210:
					case 408:
					case 602:
					case 614:
					case 804:
					case 812:
					case 1002:
					case 1014:
					case 1208:
					case 1406:
					case 1410:
						$this->Feldwert[$zeile][$spalte] = '2W';
						break;
					case 107:
					case 109:
					case 404:
					case 412:
					case 606:
					case 610:
					case 701:
					case 715:
					case 901:
					case 915:
					case 1006:
					case 1010:
					case 1204:
					case 1212:
					case 1507:
					case 1509:
						$this->Feldwert[$zeile][$spalte] = '3B';
						break;
					case 203:
					case 213:
					case 302:
					case 305:
					case 311:
					case 314:
					case 503:
					case 507:
					case 509:
					case 513:
					case 705:
					case 711:
					case 905:
					case 911:
					case 1103:
					case 1107:
					case 1109:
					case 1113:
					case 1302:
					case 1305:
					case 1311:
					case 1314:
					case 1403:
					case 1413:
						$this->Feldwert[$zeile][$spalte] = '2B';
						break;
					case 808:
						$this->Feldwert[$zeile][$spalte] = 'Plus';
						break;
					default:
						$this->Feldwert[$zeile][$spalte] = false;
				}
				
				$index++;
			}
		}

		//echo "<pre>";
		//print_r($this->Feldwert);
		//echo "</pre>";
		
	}

	/**
	 * Funktion Generator
	 * @param $messwert             string      Welcher Messwert soll getriggert werden
	 * @param $start                boolean     true = Timer starten, false = Timer stoppen
	 */
	function Generator()
	{
		// Neues Spiel? Dazu dürfen keine leeren Felder vorhanden sein
		$neu = true;
		foreach($this->Spielfeld['felder'] as $feld)
		{
			if($feld) 
			{
				$neu = false;
				break;
			}
		}

		if($neu)
		{
			// Wortliste generieren
			$this->Wortliste = self::getWortliste();

			// Wortwerte hinzufügen
			foreach($this->Wortliste as $wort)
			{
				self::generateStart($wort); // Generiert mit dem Wort die punktbeste Startposition
			}
			
			// Ergebnisse nach Wert sortieren
			$this->Ergebnis = self::sortArrayByFields
			(
				$this->Ergebnis,
				array('punkte' => SORT_DESC)
			);
		}
		else
		{
			// Es sind schon Wörter auf dem Feld
			$result = self::nextWort(1, 1); // Nächstes Wort suchen
			if($result)
			{
				if($result['laenge'] > 1)
				{
					// LIKE-Suche in Datenbank
				}
			}
		}

		//echo "<pre>";
		//print_r($result);
		//echo "</pre>";
		
		// Ergebnis aufbereiten
		$WortlisteMitWerten = array();
		for($x = 0; $x < count($this->Ergebnis); $x++)
		{
			$WortlisteMitWerten[] = '<b>'.$this->Ergebnis[$x]['wort'].'</b> ('.$this->Ergebnis[$x]['punkte'].') Z:'.$this->Ergebnis[$x]['zeile'].' S:'.$this->Ergebnis[$x]['spalte'].' '.$this->Ergebnis[$x]['richtung'];
			if($x == 19) break; // Nach 20 Wörtern abbrechen
		}

		return $WortlisteMitWerten;
	}

	/**
	 * =============================================================================================================
	 * Funktion nextWort
	 * Sucht das nächste Wort auf dem Brett und liefert es zurück
	 * @param $zeile           int     Zeile, wo die Suche beginnt
	 * @param $spalte          int     Spalte, wo die Suche beginnt
	 * =============================================================================================================
	 */
	function nextWort($zeile, $spalte)
	{
		// Brett nach dem ersten Buchstaben durchsuchen
		$fundwort = '';
		$fundachse = 0; // Achse, in der das Wort gefunden wurde
		for($ze = $zeile; $ze <= 15; $ze++)
		{
			for($sp = $spalte; $sp <= 15; $sp++)
			{
				if($this->Brett[$ze][$sp] && !$fundwort)
				{
					// Neuen Buchstaben gefunden
					$fundwort = $this->Brett[$ze][$sp]; // 1. Buchstaben sichern
					$fundspalte = $sp;
					$fundachse = $ze;
				}
				elseif($this->Brett[$ze][$sp] && $fundwort && $ze == $fundachse)
				{
					// Nächsten Buchstaben gefunden
					$fundwort .= $this->Brett[$ze][$sp]; // Nächsten Buchstaben sichern
				}
				elseif(!$this->Brett[$ze][$sp] && $fundwort && $ze == $fundachse)
				{
					// Kein nächster Buchstabe, Fundwort vorhanden und gleiche Zeile
					return array
					(
						'wort'   => $fundwort,
						'laenge' => strlen($fundwort),
						'zeile'  => $fundachse,
						'spalte' => $fundspalte
					);
				}
				elseif($this->Brett[$ze][$sp] && $fundwort && $ze != $fundachse)
				{
					// Nächster Buchstabe, Fundwort vorhanden und ungleiche Zeile
					return array
					(
						'wort'   => $fundwort,
						'laenge' => strlen($fundwort),
						'zeile'  => $fundachse,
						'spalte' => $fundspalte
					);
				}
			}
		}
	}

	function generateStart($wort)
	{
		$laenge = strlen($wort);
		$start = 9 - $laenge;
		$ende = 8;
		$maxpunkte = 0;
		$idealspalte = 0;
		$wortwert = self::getWortwert($wort); // Grundwert des Wortes ermitteln

		// Jetzt wird das Wort von links nach rechts über den Mittelpunkt geschoben und der maximale Punktwert ermittelt
		// Startspalte (4), Beispiel bei Wort mit 5 Buchstaben:
		// 1  2  3  4  5  6  7  8  9  10 11 12 13 14 15
		// -  -  -  X  X  X  X  X  -  -  -  -  -  -  -
		// Endespalte (12), Beispiel bei Wort mit 5 Buchstaben:
		// 1  2  3  4  5  6  7  8  9  10 11 12 13 14 15
		// -  -  -  -  -  -  -  X  X  X  X  X  -  -  - 
		for($spalte = $start; $spalte <= $ende; $spalte++)
		{
			// Von der aktuellen Spalte bis zum Wortende werden jetzt die Feldwerte ermittelt
			$punkte = $wortwert;
			for($col = $spalte; $col < $spalte + $laenge; $col++)
			{
				if($this->Feldwert[8][$col] == '2W')
				{
					$punkte = $punkte * 2;
					break; // Abbrechen, da es keinen weiteren Bonus gibt
				}
			}
			if($maxpunkte < $punkte)
			{
				$maxpunkte = $punkte;
				$this->Ergebnis[] = array
				(
					'wort'     => $wort,
					'joker'    => $joker,
					'punkte'   => $maxpunkte,
					'zeile'    => 8,
					'spalte'   => $spalte,
					'richtung' => 'waagerecht'
				);
			}
		}
		//echo "<pre>";
		//print_r($ergebnis);
		//echo "</pre>";
	}

	function getWortliste()
	{
		// Buchstaben übernehmen und dabei Joker zählen
		$joker = 0;
		$buchstaben = '';
		for($x = 0; $x < strlen($this->Spielfeld['buchstaben']); $x++)
		{
			$buchstabe = substr($this->Spielfeld['buchstaben'], $x, 1);
			if($buchstabe == '-') $joker++; // Joker gefunden
			else $buchstaben .= $buchstabe; // normaler Buchstabe
		}
		
		// Buchstaben inkl. Joker generieren und suchen
		// Zuerst ohne Joker
		$liste = self::Buchsuche($buchstaben);
		// Jetzt mit Joker
		if($joker > 0)
		{
			foreach($this->Alphabet as $Jokerbuchstabe)
			{
				$result = self::Buchsuche($buchstaben.$Jokerbuchstabe); // Erlaubte Buchstaben + Jokerbuchstabe
				$liste = array_merge($liste, $result);
			}
		}
		
		$liste = array_unique($liste);
		//echo "<pre>";
		//print_r($liste);
		//echo "</pre>";
		// Gefundene Wörter nach Länge sortieren
		//usort($liste, array($this, 'sort_strlen_desc'));
		return $liste;
	}

	/**
	 * =============================================================================================================
	 * Suchfunktion
	 * =============================================================================================================
	 */
	function Buchsuche($buchstaben)
	{
		$arrBuchstabenAnzahl = self::BuchstabenZaehlen($buchstaben);

		// Wortliste aus Datenbank laden
		$sql = "SELECT * FROM wortliste WHERE wortlaenge <= ".strlen($buchstaben);
		$result = mysqli_query($this->db, $sql);
		$anzahl = mysqli_num_rows($result);

		self::Stoppuhr('suche', true);

		$ergebnis = array();
		if($result)
		{
			while($row = $result->fetch_assoc())
			{
				$arrWortAnzahl = unserialize($row['buchstabenanzahl']);
				$ungleich = self::BuchstabenVergleich($arrWortAnzahl, $arrBuchstabenAnzahl);
				if(!$ungleich)
				{
					// Keine ungleichen Buchstaben
					if($row['wort']) $ergebnis[] = $row['wort'];
				}
			}
		} 

		self::Stoppuhr('suche', false);
		return $ergebnis;
	}

	function BuchstabenZaehlen($buchstaben)
	{
		$zaehler = count_chars($buchstaben); // Zählt die Anzahl der Buchstaben in gesuchten Buchstaben
		$ausgabe = array();
		foreach($zaehler as $key => $value)
		{
			if($value)
			{
				$ausgabe[chr($key)] = $value;
			}
		}
		return $ausgabe;
	}

	function BuchstabenVergleich($arrWort, $arrSuche)
	{
		//echo "<pre>";
		//print_r($arrWort);
		//echo "</pre>";
		if($arrWort)
		{
			foreach($arrWort as $key => $value)
			{
				if($arrWort[$key] > $arrSuche[$key])
				{
					return true;
				}
			}
		}
		else
		{
			return true;
		}
		return false;
	}

	function BuchstabenAnzahlVergleich($wort, $ZaehlerSuchbegriff)
	{
		//self::Stoppuhr('BuchstabenAnzahlVergleich', true);
		// Buchstaben zählen im Wort
		$zaehler_wort = count_chars($wort); // 12,03 sec für alles
		$ungleich = false; // Anzahl der ungleichen Buchstaben
		// Schleife allein: 0,04 sec

		// Anzahl dieser Buchstaben vergleichen: A-Z = 65-90
		for($x = 65; $x < 91; $x++)
		{
			// Dauer: 13,27 sec gegenüber 23,78 sec bei 0...255
			if($zaehler_wort[$x] > $ZaehlerSuchbegriff[$x])
			{
				// Im Wörterbuch-Wort sind zuviele Buchstaben
				$ungleich = true;
				break;
			}
		}

		// Anzahl der Umlaute vergleichen
		if(!$ungleich)
		{
			foreach($this->Umlaute as $umlaut)
			{
				if($zaehler_wort[$umlaut] > $ZaehlerSuchbegriff[$umlaut])
				{
					// Im Wörterbuch-Wort sind zuviele Buchstaben
					$ungleich = true;
					break;
				}
			}
		}

		// 14,35 und 12,81 sec bis hierher
		//self::Stoppuhr('BuchstabenAnzahlVergleich', false);

		return $ungleich;
	}

	function getWortwert($wort)
	{
		$punkte = 0;
		for($x = 0; $x < strlen($wort); $x++)
		{
			$zeichen = substr($wort, $x, 1);
			$punkte += $this->Buchstabenwert[$zeichen];
		}
		return $punkte;
	}

	function getWortanzahl()
	{
		return number_format(count($this->Wortliste), 0, ',', '.');
	}

	// Array sortieren nach Zeichenlänge
	// Aufsteigend
	function sort_strlen_asc($a, $b)
	{
		if (strlen($a) == strlen($b))
		{
			return 0;
		}
		return (strlen($a) < strlen($b)) ? -1 : 1;
	}
	
	// Absteigend
	function sort_strlen_desc($a, $b)
	{
		if (strlen($a) == strlen($b))
		{
			return 0;
		}
		return (strlen($a) > strlen($b)) ? -1 : 1;
	}

	function sortArrayByFields($arr, $fields)
	{
	    $sortFields = array();
	    $args       = array();
	
	    foreach ($arr as $key => $row) {
	        foreach ($fields as $field => $order) {
	            $sortFields[$field][$key] = $row[$field];
	        }
	    }
	
	    foreach ($fields as $field => $order) {
	        $args[] = $sortFields[$field];
	
	        if (is_array($order)) {
	            foreach ($order as $pt) {
	                $args[$pt];
	            }
	        } else {
	            $args[] = $order;
	        }
	    }
	
	    $args[] = &$arr;
	
	    call_user_func_array('array_multisort', $args);
	
	    return $arr;
	}

	/**
	 * Funktion Stoppuhr
	 * @param $messwert             string      Welcher Messwert soll getriggert werden
	 * @param $start                boolean     true = Timer starten, false = Timer stoppen
	 */
	function Stoppuhr($messwert, $start)
	{
		// Messwert neu anlegen, wenn noch nicht vorhanden
		if(!$this->Zeitmessungen[$messwert])
		{
			$this->Zeitmessungen[$messwert] = array
			(
				'start'     => 0, // Zeit des letztes Starts
				'dauer'     => 0, // insgesamt verbrauchte Zeit
				'messungen' => 0, // Anzahl der Messungen
			);
		}

		// Messung starten/stoppen
		if($start)
		{
			// Messung starten
			$this->Zeitmessungen[$messwert]['start'] = microtime(true);
		}
		else
		{
			// Messung stoppen
			$time_end = microtime(true); // Zeit stoppen
			$time = $time_end - $this->Zeitmessungen[$messwert]['start']; // Zeit seit letztem Start berechnen
			$this->Zeitmessungen[$messwert]['dauer'] += $time; // Gemessene Zeit zur Gesamtzeit addieren
			$this->Zeitmessungen[$messwert]['messungen']++; // Messung addieren
		}
	}

	function getZeitmessung($messwert)
	{
		if($messwert)
		{
			// Bestimmten Messwert zurückgeben
			$messung = array
			(
				'dauer'     => str_replace('.', ',', sprintf('%01.4f', $this->Zeitmessungen[$messwert]['dauer'])),
				'messungen' => $this->Zeitmessungen[$messwert]['messungen']
			);
		}
		else
		{
			// Alle Messwerte zurückgeben
		}
		return $messung;
	}

}
