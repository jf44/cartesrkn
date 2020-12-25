<?php
// set_params.php : modifie la liste des parametres de génération des cartes G.E. pour un skipper donné et un groupe donné ,
// ligne 'bateau;params' du fichier ./data/ge_params.csv
// paramètres : liste boat,course,team,echelle (separateur ',')    "jf44-RKN,VGv2020,RKN,8"

// http://localhost:8080/voilevirtuelle/vgv2020/cartes_rkn/%3Cbr%20/%3E%3Cb%3EWarning%3C/b%3E:%20%20scandir(D:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/rkn3d_2020122523,D:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/rkn3d_2020122523):%20Le%20fichier%20sp%EF%BF%BDcifi%EF%BF%BD%20est%20introuvable.%20(code:%202)%20in%20%3Cb%3ED:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/include/zip.php%3C/b%3E%20on%20line%20%3Cb%3E160%3C/b%3E%3Cbr%20/%3E%3Cbr%20/%3E%3Cb%3EWarning%3C/b%3E:%20%20scandir(D:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/rkn3d_2020122523):%20failed%20to%20open%20dir:%20No%20such%20file%20or%20directory%20in%20%3Cb%3ED:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/include/zip.php%3C/b%3E%20on%20line%20%3Cb%3E160%3C/b%3E%3Cbr%20/%3E%3Cbr%20/%3E%3Cb%3EWarning%3C/b%3E:%20%20scandir():%20(errno%202):%20No%20such%20file%20or%20directory%20in%20%3Cb%3ED:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/include/zip.php%3C/b%3E%20on%20line%20%3Cb%3E160%3C/b%3E%3Cbr%20/%3E%3Cbr%20/%3E%3Cb%3EWarning%3C/b%3E:%20%20array_diff():%20Argument%20#1%20is%20not%20an%20array%20in%20%3Cb%3ED:\xampp\htdocs\voilevirtuelle\vgv2020\cartes_rkn\webservice\include\zip.php%3C/b%3E%20on%20line%20%3Cb%3E160%3C/b%3E%3Cbr%20/%3E%3Cbr%20/%3E%3Cb%3EWarning%3C/b%3E:%20%20Invalid%20argument%20supplied%20for%20foreach()%20in%20%3Cb%3ED:\xampp\htdocs\voilevirtuelle\vgv2020\cartes_rkn\webservice\include\zip.php%3C/b%3E%20on%20line%20%3Cb%3E161%3C/b%3E%3Cbr%20/%3E%3Cbr%20/%3E%3Cb%3EWarning%3C/b%3E:%20%20rmdir(D:/xampp/htdocs/voilevirtuelle/vgv2020/cartes_rkn/webservice/rkn3d_2020122523):%20No%20such%20file%20or%20directory%20in%20%3Cb%3ED:\xampp\htdocs\voilevirtuelle\vgv2020\cartes_rkn\webservice\include\zip.php%3C/b%3E%20on%20line%20%3Cb%3E164%3C/b%3E%3Cbr%20/%3Ehttp://localhost:8080/voilevirtuelle/vgv2020/cartes_rkn/webservice/kmz/VGv2020_C-RKN_IroizoC-RKN_3D2020122523.kmz

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
if (empty($boatname) || empty($params))
{
	echo "Erreur!";
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
			if (list($un_nomboat,$une_params) = explode(";", $line))
			{
    	        //echo "<br>Connecté. BOATNAME:$un_nomboat;$une_params\n";
				if (trim($boatname) == trim($un_nomboat))
	        	{
    	        	$output[]=trim($boatname).";".trim($params);
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