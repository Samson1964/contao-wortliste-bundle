<?php

namespace Schachbulle\ContaoWortlisteBundle\Classes;

if (!defined('TL_ROOT')) die('You cannot access this file directly!');
set_time_limit(0);

/**
 * Class Import
  */
class Import extends \Backend
{

	var $Buchstabenwert = array();

	function __construct()
	{
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


	}

	/**
	 * Exportiert alle noch nicht übertragenen Lizenzen zum DOSB
	 */
	public function run_neu()
	{

		// jQuery einbinden
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaowortliste/js/jquery-3.5.1.min.js';

		$content .= '<div id="import" style="margin:10px;"></div>';
		$content .= '<div id="import_status" style="margin:10px;"><img src="bundles/contaowortliste/images/ajax-loader.gif"></div>';

		// Zurücklink generieren
		$backlink = str_replace('&key=import', '', \Environment::get('request'));
		$content .= '<div style="margin:10px;"><a href="'.$backlink.'">Zurück</a> | Alternativ: <a href="bundles/contaowortliste/Import.php" target="_blank">Import.php im neuen Fenster ausführen</div>';

		$content .= '<script>'."\n";
		$content .= '$.ajax({'."\n";
		$content .= '  url: "bundles/contaowortliste/Import.php",'."\n";
		$content .= '  cache: false,'."\n";
		$content .= '  success: function(response) {'."\n";
		$content .= '    $("#import").append(response);'."\n";
		$content .= '    $("#import_status").html("");'."\n";
		$content .= '  }'."\n";
		$content .= '});'."\n";
		$content .= '</script>'."\n";

		return $content;

	}

	/**
	 * Importiert eine Wortliste
	 */
	public function run()
	{

		if(\Input::get('key') != 'import')
		{
			// Beenden, wenn der Parameter nicht übereinstimmt
			return '';
		}

		// Objekt BackendUser importieren
		$this->import('BackendUser','User');
		$class = $this->User->uploader;

		// See #4086
		if (!class_exists($class))
		{
			$class = 'FileUpload';
		}

		$objUploader = new $class();

		// Formular wurde abgeschickt, Wortliste importieren
		if (\Input::post('FORM_SUBMIT') == 'tl_wortliste_import')
		{
			$arrUploaded = $objUploader->uploadTo('system/tmp');

			if(empty($arrUploaded))
			{
				\Message::addError($GLOBALS['TL_LANG']['ERR']['all_fields']);
				$this->reload();
			}

			$this->import('Database');

			foreach ($arrUploaded as $txtFile)
			{
				$objFile = new \File($txtFile, true);

				if ($objFile->extension != 'txt')
				{
					\Message::addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $objFile->extension));
					continue;
				}

				$resFile = $objFile->handle;
				$importiert = 0;
				$ignoriert = 0;
				$bearbeitet = 0;
				$start = microtime(true);

				while(!feof($resFile))
				{
					$line = trim(fgets($resFile));
					$wort = self::replaceWord($line);

					if($wort)
					{
						$bearbeitet++;
						// Wort suchen
						$objResult = \Database::getInstance()->prepare("SELECT * FROM tl_wortliste WHERE wort = ?")
						                                     ->limit(1)
						                                     ->execute($wort);
						if($objResult->numRows)
						{
							// Wort bereits vorhanden
							$ignoriert++;
							continue;
						}

						// Wort noch nicht vorhanden, dann eintragen
						$set = array
						(
							'tstamp'    => time(),
							'wort'      => $wort,
							'anzahl'    => strlen($wort),
							'wert'      => self::Wert($wort),
							'char_65'   => self::Anzahl('A', $wort),
							'char_66'   => self::Anzahl('B', $wort),
							'char_67'   => self::Anzahl('C', $wort),
							'char_68'   => self::Anzahl('D', $wort),
							'char_69'   => self::Anzahl('E', $wort),
							'char_70'   => self::Anzahl('F', $wort),
							'char_71'   => self::Anzahl('G', $wort),
							'char_72'   => self::Anzahl('H', $wort),
							'char_73'   => self::Anzahl('I', $wort),
							'char_74'   => self::Anzahl('J', $wort),
							'char_75'   => self::Anzahl('K', $wort),
							'char_76'   => self::Anzahl('L', $wort),
							'char_77'   => self::Anzahl('M', $wort),
							'char_78'   => self::Anzahl('N', $wort),
							'char_79'   => self::Anzahl('O', $wort),
							'char_80'   => self::Anzahl('P', $wort),
							'char_81'   => self::Anzahl('Q', $wort),
							'char_82'   => self::Anzahl('R', $wort),
							'char_83'   => self::Anzahl('S', $wort),
							'char_84'   => self::Anzahl('T', $wort),
							'char_85'   => self::Anzahl('U', $wort),
							'char_86'   => self::Anzahl('V', $wort),
							'char_87'   => self::Anzahl('W', $wort),
							'char_88'   => self::Anzahl('X', $wort),
							'char_89'   => self::Anzahl('Y', $wort),
							'char_90'   => self::Anzahl('Z', $wort),
							'char_196'  => self::Anzahl('Ä', $wort),
							'char_214'  => self::Anzahl('Ö', $wort),
							'char_220'  => self::Anzahl('Ü', $wort),
							'published' => 1
						);
						$importiert++;
						$objResult = \Database::getInstance()->prepare("INSERT INTO tl_wortliste %s")
						                                     ->set($set)
						                                     ->execute();
					}
					if($importiert == 35000) break;
				}

				$dauer = sprintf('%f0.4', microtime(true) - $start);
				\System::log('Aus Datei '.$objFile->name.' Wörter bearbeitet: '.$bearbeitet.' - importiert: '.$importiert.' - ignoriert: '.$ignoriert.' - Dauer: '.$dauer.'s', __METHOD__, TL_GENERAL);
			}

			// Cookie setzen und zurückkehren zur Adressenliste (key=import aus URL entfernen)
			\System::setCookie('BE_PAGE_OFFSET', 0, 0);
			$this->redirect(str_replace('&key=import', '', \Environment::get('request')));
		}

		// Return form
		return '
<div class="content">
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=import', '', \Environment::get('request'))).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['MSC']['tw_import'][1].'</h2>
'.\Message::generate().'
<form action="'.ampersand(\Environment::get('request'), true).'" id="tl_wortliste_import" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_wortliste_import">
<input type="hidden" name="REQUEST_TOKEN" value="'.REQUEST_TOKEN.'">

<div class="widget">
  <h3>'.$GLOBALS['TL_LANG']['MSC']['source'][0].'</h3>'.$objUploader->generateMarkup().(isset($GLOBALS['TL_LANG']['MSC']['source'][1]) ? '
  <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['MSC']['source'][1].'</p>' : '').'
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['tw_import'][0]).'">
</div>

</div>
</form>
</div>';

	}

	public function Anzahl($buchstabe, $wort)
	{
		$wort = self::is_utf8($wort) ? utf8_decode($wort) : $wort;
		$buchstabe = self::is_utf8($buchstabe) ? utf8_decode($buchstabe) : $buchstabe;
		return substr_count($wort, $buchstabe);
	}

	public function Wert($wort)
	{
		$wort = self::is_utf8($wort) ? utf8_decode($wort) : $wort;
		$rating = 0;
		for($x = 0; $x < strlen($wort); $x++)
		{
			$buchstabe = substr($wort, $x, 1);
			//echo $buchstabe;
			//echo ' / ';
			//echo self::Anzahl($buchstabe, $wort);
			//echo ' / ';
			//echo $this->Buchstabenwert[utf8_encode($buchstabe)];
			//echo '<br>';
			$rating += self::Anzahl($buchstabe, $wort) * $this->Buchstabenwert[utf8_encode($buchstabe)];
		}
		return $rating;
	}

	public function replaceWord($value)
	{
		$value = self::is_utf8($value) ? $value : utf8_encode($value);

		$value = str_replace(array('.', '-', ',', 'ä', 'ö', 'ü', 'ß'), array('', '', '', 'Ä', 'Ö', 'Ü', 'SS'), $value);
		$value = strtoupper($value);
		return $value;
	}

	public function is_utf8($str)
	{
	    $strlen = strlen($str);
	    for ($i = 0; $i < $strlen; $i++) {
	        $ord = ord($str[$i]);
	        if ($ord < 0x80) continue; // 0bbbbbbb
	        elseif (($ord & 0xE0) === 0xC0 && $ord > 0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
	        elseif (($ord & 0xF0) === 0xE0) $n = 2; // 1110bbbb
	        elseif (($ord & 0xF8) === 0xF0 && $ord < 0xF5) $n = 3; // 11110bbb (exkl F5-FF)
	        else return false; // ungültiges UTF-8-Zeichen
	        for ($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
	            if (++$i === $strlen || (ord($str[$i]) & 0xC0) !== 0x80)
	                return false; // ungültiges UTF-8-Zeichen
	    }
	    return true; // kein ungültiges UTF-8-Zeichen gefunden
	}

}
