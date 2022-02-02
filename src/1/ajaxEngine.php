<?php
ini_set("memory_limit","2560M");

include('include/config.php');
include('classes/WordWithFriends.php');

// Daten laden und Spiel speichern
$spielfeld['buchstaben'] = str_replace(array('ä', 'ö', 'ü'), array('Ä', 'Ö', 'Ü'), strtoupper($_POST['buchstaben']));
$spielfeld['spielname'] = strtoupper($_POST['spielname']);
$spielfeld['felder'] = array();
for($feld = 1; $feld <= 225; $feld++)
{
	$spielfeld['felder'][$feld] = str_replace(array('ä', 'ö', 'ü'), array('Ä', 'Ö', 'Ü'), strtoupper($_POST['feld'.$feld]));
}
$fp = fopen('daten/'.$spielfeld['spielname'], 'w');
fputs($fp, serialize($spielfeld));
fclose($fp);

// Engine initialisieren
$spiel = new WordWithFriends($spielfeld);
$spiel->Stoppuhr('skript', true);

// Zuggenerator aufrufen
$liste = $spiel->Generator();

$ausgabe['result'] = $liste;

$spiel->Stoppuhr('skript', false);

// Laufzeit Skript
$zeit = $spiel->getZeitmessung('skript');
$ausgabe['zeitname'][] = 'Skriptlaufzeit';
$ausgabe['zeitdauer'][] = $zeit['dauer'];

// Laufzeit Wörterbuch
$zeit = $spiel->getZeitmessung('woerterbuch_einlesen');
$ausgabe['zeitname'][] = 'Wörterbuch einlesen';
$ausgabe['zeitdauer'][] = $zeit['dauer'];

// Laufzeit Buchstabenvergleich
$zeit = $spiel->getZeitmessung('BuchstabenAnzahlVergleich');
$ausgabe['zeitname'][] = 'Buchstabenvergleich';
$ausgabe['zeitdauer'][] = $zeit['dauer'];

// Laufzeit Suche
$zeit = $spiel->getZeitmessung('suche');
$ausgabe['zeitname'][] = 'Suche';
$ausgabe['zeitdauer'][] = $zeit['dauer'];

//print_r($ausgabe);
//echo "</pre>";
echo json_encode($ausgabe);
