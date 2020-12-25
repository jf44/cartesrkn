<?php
// set_params.php : modifie la liste des parametres de génération des cartes G.E. pour un skipper donné et un team donné ,
// ligne 'bateau;params' du fichier ./data/ge_params.csv
// paramètres : liste boat,course,team,echelle (separateur ',')    "jf44-RKN;VGv2020,RKN,8"


$boatname=""; // par exemple LadyJane
$boat_file="/data/ge_params.csv"; // la liste des paramètres
$output=array();

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
	$uri = 'https://';
} else {
	$uri = 'http://';
}

//$url_serveur_local = $uri.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].get_url_pere($_SERVER['SCRIPT_NAME']);
$url_serveur_local = $uri.$_SERVER['HTTP_HOST'].get_url_pere($_SERVER['SCRIPT_NAME']);

// DEBUG
//echo "<br>URL : $url_serveur_local\n";

$dir_serveur = dirname($_SERVER['SCRIPT_FILENAME']);
// DEBUG
//echo "<br>Répertoire serveur : $dir_serveur\n";
// Nom du script chargé dynamiquement.
$appli=$_SERVER["PHP_SELF"];

if (isset($_GET['boatname']) && ($_GET['boatname']!="")){
	$boatname=to_utf8($_GET['boatname']);    //Les superglobales $_GET et $_REQUEST sont déjà décodées mais leur fonction de decodage déconne avec utf8
}
if (isset($_GET['params']) && ($_GET['params']!="")){
	$params=to_utf8($_GET['params']);    //Les superglobales $_GET et $_REQUEST sont déjà décodées mais leur fonction de decodage déconne avec utf8
}

if (isset($_POST['boatname']) && ($_POST['boatname']!="")){
	$boatname=urldecode(to_utf8($_POST['boatname']));
}
if (isset($_POST['params']) && ($_POST['params']!="")){
	$params=urldecode(to_utf8($_POST['params']));    //Les superglobales $_GET et $_REQUEST sont déjà décodées mais leur fonction de decodage déconne avec utf8
}

//echo "<br>$dir_serveur$boat_file \n";
if (empty($boatname))
{
 	echo '{"error":"empty boatname"}';
	exit;
}

if (empty($params))
{
 	echo '{"error":"empty params"}';
	exit;
}


//echo "<br>Connecté: BOATNAME:$boatname;$params\n";
if (!empty($boatname) && !empty($params))
{
	if (!empty($dir_serveur.$boat_file) && file_exists($dir_serveur.$boat_file))
	{
		$ok_loaded=false;
		$lines = file($dir_serveur.$boat_file);
		//print_r($lines);
		foreach ( $lines as $line)
		{

			if (!empty($line) && preg_match("/;/", $line))
			{
				list($un_nomboat,$un_params) = explode(";", $line);
			    //echo "<br>BOATNAME:$un_nomboat;$une_params\n";

				$un_nomboat=trim($un_nomboat);
				if (!empty($un_nomboat) && (trim($boatname) == $un_nomboat))
	        	{
    	        	$output[]=trim($boatname).";".trim($params); // Mise à jour
                    $ok_loaded=true;
				}
				else
				{
            	    $output[]=trim($line);
				}
			}
		}
		if (!$ok_loaded) // Ajouter une ligne de paramètres au fichier
		{
     	    $output[]=trim($boatname).";".trim($params);
		}
	}

	// réécrire le fichier
	if ($f=fopen($dir_serveur.$boat_file, "w"))
	{
		foreach ($output as $line)
		{
            //echo "$line";
			fwrite ($f, $line."\n");
		}
		fclose($f);
 		echo '{"file":"OK"}';
	}
	else
	{
 		echo '{"error":"file"}';
	}
}


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

?>