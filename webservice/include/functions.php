<?php

// ----------------------------
function get_url_pere($path) {
// Retourne l'URL du répertoire contenant le script
// global $PHP_SELF;
// DEBUG
// echo "<br>PHP_SELF : $PHP_SELF\n";
//	$path = $PHP_SELF;
	$nomf = substr( strrchr($path, "/" ), 1);
	if ($nomf){
		$pos = strlen($path) - strlen($nomf) - 1;
		$pere = substr($path,0,$pos);
	}
	else
		$pere = $path;
	return $pere;
}


//----------
function position2lonlat($str_position){
	// conversionde la position en longitude latitude décimales
	// 44°36'41.94"N 8°42'36.81"W   ->
	// Pourrait être plus général en commençant par couper selon l'espace puis appeler position_geographique
	// mais pas plus rapide
	// etourne un tableau de gcodes

// Determiner Longitude (E|W|O)
// Latitude (N|S)
	// DEBUG
    $strgeo_out='';
	$out=array();

	if (!empty($str_position)){
		// Purger quelques scories
		$search  = array("’",'O','"','&deg;');
		$replace = array("'",'W','','°');   //
		$str_position=str_replace($search,$replace,$str_position); // Transforme Ouest en West

		// Decouper en deux sous-chaines avec le séparateur ' '
		// c'est un point faible car si il y a 44° 36'41.9"N 8°42'36.8109"W à traiter cela echoue.
		// je ne peux même pas vérifier que la longueur des sous-chaînes est identique

		// echo "\n<pre>DEBUG :: 103 :: STR_POSITION : \"".$str_position."\"\nMATCHES:\n";
    	$matches=preg_split("/[\s,]+/",trim($str_position));
		// print_r($matches);
    	// echo "\n</pre>\n";

		foreach($matches as $strgeo)
		{
    		$geocode = new stdClass();
			$geocode->type='';
			$geocode->degre='';
			$geocode->minute='';
			$geocode->seconde='';

    	    if (strpos($strgeo,'E') !== false){
				//echo "Traitement Est<br />\n";
            	$strgeo_out = "E";
	            $geocode->type='E';
    	        $strgeo=str_replace('E','',$strgeo);
			}
			elseif (strpos($strgeo,'W') !== false){
    	        //echo "Traitement Ouest<br />\n";
        	    $strgeo_out = "O";
            	$geocode->type='O';   // Francisé
				$strgeo=str_replace('W','',$strgeo);
    	        $strgeo=str_replace('O','',$strgeo);  // A priori inutile
			}
			elseif (strpos($strgeo,'S') !== false){
        		//echo "Traitement Sud<br />\n";
	            $strgeo_out = "S";
    	        $geocode->type='S';
        	    $strgeo=str_replace('S','',$strgeo);
			}
			elseif (strpos($strgeo,'N') !== false){
   				//echo "Traitement Nord<br />\n";
	            $strgeo_out = "N";
    	        $geocode->type='N';
        	    $strgeo=str_replace('N','',$strgeo);
			}

			// Recombiner  en utilisant la librairie des fonctions multi bytes
	    	// $degres =  mb_strstr($strgeo, "°", true); Non supporte chez FREE
			$degres ='';
		    $minutes_secondes = '';
			$minutes = '';
			$secondes = '';

    		$len_degch=strlen("°");
		    $degrepos = strpos($strgeo, "°",0);
			if ($degrepos!==false){
        		$degres = substr($strgeo, 0, $degrepos);
	        	$minutes_secondes = substr($strgeo, $degrepos+$len_degch, strlen($strgeo));
	 		}
    		$len_minch=strlen( "'");
	    	$minutepos = strpos($minutes_secondes, "'",0);
			if ($minutepos!==false){
    	    	$minutes = substr($minutes_secondes, 0, $minutepos);
	    	    $secondes = substr($minutes_secondes, $minutepos+$len_minch, strlen($minutes_secondes));
			}

    		//echo '<br \>ELEMENTS :: Degrés: "'.$degres.'" Minutes_secondes: "'.$minutes_secondes.'" Minutes: "'.$minutes.'" Secondes: "'.$secondes.'" <br />'."\n";

			//$strgeo_out.=$degres.'°'.$minutes."'".$secondes;
 			//echo ' --> "'.$strgeo_out.'" <br />'."\n";

	    	$geocode->degre=(int)$degres;
			$geocode->minute=(int)$minutes;
			$geocode->seconde=(float)$secondes;
			$out[] = $geocode;
		}
	}
    // echo "\n<pre>DEBUG :: 171 :: OUT:\n";
	// print_r($out);
    // echo "\n</pre>\n";

	return  $out;
}


//----------
function position_geographique($str_position){
// conversionde la position en longitude latitude décimales
// 44°N 36' 41.94"
// 8°W 42' 36.81"   ->
// Determiner Longitude (E|W|O)
// Latitude (N|S)
	// DEBUG
    $str_position_out='';

	$geocode = new stdClass();
	$geocode->type='';
	$geocode->degre='';
	$geocode->minute='';
	$geocode->seconde='';

if (!empty($str_position)){
	$search  = array("’", ' ', '"', 'W', '&deg;');
	$replace = array("'", '', '', 'O', '°');
	$str_position=str_replace($search,$replace,$str_position); // Supprimer les espace et autres caractères indésirables

        if (strpos($str_position,'E') !== false){
			//echo "Traitement Est<br />\n";
            $str_position_out = "E";
            $geocode->type='E';
            $str_position=str_replace('E','',$str_position);
		}
		elseif (strpos($str_position,'O') !== false){
            //echo "Traitement Ouest<br />\n";
            $str_position_out = "O";
            $geocode->type='O';
			$str_position=str_replace('W','',$str_position);   // W -> O
            $str_position=str_replace('O','',$str_position);
		}
		elseif (strpos($str_position,'S') !== false){
        	//echo "Traitement Sud<br />\n";
            $str_position_out = "S";
            $geocode->type='S';
            $str_position=str_replace('S','',$str_position);
		}
		elseif (strpos($str_position,'N') !== false){
   			//echo "Traitement Nord<br />\n";
            $str_position_out = "N";
            $geocode->type='N';
            $str_position=str_replace('N','',$str_position);
		}
		else{
			return null;
		}

		// Recombiner  en utilisant la librairie des fonctions multi bytes
	    // $degres =  mb_strstr($str_position, "°", true); Non supporte chez FREE
		$degres ='';
	    $minutes_secondes = '';
		$minutes = '';
		$secondes = '';

    	$len_degch=strlen("°");
	    $degrepos = strpos($str_position, "°",0);
		if ($degrepos!==false){
        	$degres = substr($str_position, 0, $degrepos);
	        $minutes_secondes = substr($str_position, $degrepos+$len_degch, strlen($str_position));
 		}
    	$len_minch=strlen( "'");
	    $minutepos = strpos($minutes_secondes, "'",0);
		if ($minutepos!==false){
        	$minutes = substr($minutes_secondes, 0, $minutepos);
	        $secondes = substr($minutes_secondes, $minutepos+$len_minch, strlen($minutes_secondes));
		}

    	//echo '<br \>ELEMENTS :: Degrés: "'.$degres.'" Minutes_secondes: "'.$minutes_secondes.'" Minutes: "'.$minutes.'" Secondes: "'.$secondes.'" <br />'."\n";

		//$str_position_out.=$degres.'°'.$minutes."'".$secondes;
 		//echo ' --> "'.$str_position_out.'" <br />'."\n";

    	$geocode->degre=$degres;
		$geocode->minute=$minutes;
		$geocode->seconde=$secondes;
	}
     return  $geocode;
}



//------------------------------
function geocode2str($geocode, $fin=false){
	$str='';
	if ($fin){
    	return $geocode->degre."&deg;".$geocode->minute."'".$geocode->seconde." ".$geocode->type;
	}
	else{
		return $geocode->type." ".$geocode->degre."&deg;".$geocode->minute."'".$geocode->seconde;
	}
}

//------------------------------
function geocode2dec($geocode, $type=false){
	if (!empty($geocode)){
        $coord_decimal = $geocode->degre + ($geocode->minute / 60.0) + ($geocode->seconde / 3600.0);
		if (($geocode->type == 'S') || ($geocode->type == 'O') || ($geocode->type == 'W'))
		{
            $coord_decimal = -$coord_decimal;
		}
		if ($type)
		{
			if (($geocode->type == 'S') || ($geocode->type == 'N'))
			{
				$coord_decimal .= " Latitude";
			}
			else
			{
                $coord_decimal .= " Longitude";
			}
		}
        return $coord_decimal;
	}
	return false;
}

//------------------------------------
function set_voile($sail){
	// retourne un numéro de voile
	$voile = 0;

    $sail=str_replace(" (Auto)","",$sail);
	switch ($sail) {
           case "Stay" :
        	$voile = 1;      // Tourmentin ?
			break;
        case "Jib" :
        	$voile = 1;      // Foc
			break;
        case "Spi" :
        case "Spi Lourd" :
		case "HG":
         	$voile = 2;
			break;
        case "LJ" :
        	$voile = 4;     // foc2
			break;
        case "C0" :
        	$voile = 16;    // Code zéro
			break;
        case "LG" :
        	$voile = 64;  // Gennaker
			break;
		default :
			$voile = 1;
	}

	return $voile;

}


// --------------
function to_utf8( $string ) {
// From http://w3.org/International/questions/qa-forms-utf-8.html
    if ( preg_match('%^(?:
      [\x09\x0A\x0D\x20-\x7E]            # ASCII
    | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
    | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
    | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
    | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
    | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
    | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
    | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
)*$%xs', $string) ) {
        return $string;
    } else {
        return iconv( 'CP1252', 'UTF-8', $string);
    }
}


//-------------------------
function hexa2_3dec($hexa){
	// rrvvbb -> hexdec(rr);hexdec(vv);hexdec(bb)
    if (list($rr, $vv, $bb) = explode(';', chunk_split ($hexa,2,';')))
	{
		return (hexdec($rr).';'.hexdec($vv).';'.hexdec($bb));
	}
	return false;
}


?>