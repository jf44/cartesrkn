<?php
// set_boat_color.php : modifie la liste des couleurs avec séparateur ,
// ligne 'bateau;couleur' du fichier ./ata/boats.csv
// coleur liste des couleurs (separateur ',')    "300c00,907800,9dfc00,6dff60,c0ffe2"


$boatname=""; // par exemple LadyJane
$boat_file="/data/boats.csv"; // la liste des bateaux et des couleurs associées
$output=array();

//include('../ge/include/config.php');
//include('../ge/include/functions.php');


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
if (isset($_GET['couleur']) && ($_GET['couleur']!="")){
	$couleur=to_utf8($_GET['couleur']);    //Les superglobales $_GET et $_REQUEST sont déjà décodées mais leur fonction de decodage déconne avec utf8
}

if (isset($_POST['boatname']) && ($_POST['boatname']!="")){
	$boatname=urldecode(to_utf8($_POST['boatname']));
}
if (isset($_POST['couleur']) && ($_POST['couleur']!="")){
	$couleur=urldecode(to_utf8($_POST['couleur']));    //Les superglobales $_GET et $_REQUEST sont déjà décodées mais leur fonction de decodage déconne avec utf8
}

//echo "<br>$dir_serveur$boat_file \n";
if (empty($boatname) || empty($couleur))
{
	echo "Erreur!";
	exit;
}

//echo "<br>Connecté: BOATNAME:$boatname;$couleur\n";
if (!empty($boatname) && !empty($couleur))
{
	if (!empty($dir_serveur.$boat_file) && file_exists($dir_serveur.$boat_file))
	{
		$ok_loaded=false;
		$lines = file($dir_serveur.$boat_file);
		//print_r($lines);
		foreach ( $lines as $line)
		{
			if (list($un_nomboat,$une_couleur) = explode(";", $line))
			{
    	        //echo "<br>Connecté. BOATNAME:$un_nomboat;$une_couleur\n";
				if (trim($boatname) == trim($un_nomboat))
	        	{
    	        	$output[]=trim($boatname).";".trim($couleur);
                    $ok_loaded=true;
				}
				else
				{
            	    $output[]=trim($line);
				}
			}
		}
		if (!$ok_loaded) // Ajouter un bateau
		{
     	    $output[]=trim($boatname).";".trim($couleur);
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
		echo "Done!";
	}
	else
	{
  		echo "Erreur!";
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