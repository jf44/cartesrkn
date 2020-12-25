<?php
// get_kmz.php
// reponse à un appel Ajax
//  Retourne une liste de fichiers KMZ
$extension_kmz='.kmz'; // lue par Google Earth
$boatname='';

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
	$uri = 'https://';
} else {
	$uri = 'http://';
}

$url_serveur_local = $uri.$_SERVER['HTTP_HOST'].get_url_pere($_SERVER['SCRIPT_NAME']);
$dir_serveur = dirname($_SERVER['SCRIPT_FILENAME']);
// DEBUG
// echo "<br>Répertoire serveur : $dir_serveur\n";
// Nom du script chargé dynamiquement.
$appli=$_SERVER["PHP_SELF"];

if (!empty($_GET['boatname']))
{
	$boatname=to_utf8($_GET['boatname']);      // to_utf8($_GET['params'])
}
if (!empty($_POST['boatname']))
{
	$boatname=to_utf8($_POST['boatname']);      // to_utf8($_GET['params'])
}

if (!empty($boatname))
{
	// $tfiles["fichiers"][0]="VGv2020_C-RKN_IroizoC-RKN_3D2020122112.kmz";
	// $tfiles["fichiers"][1]="VGv2020_C-RKN_IroizoC-RKN_3D2020122118.kmz";
	echo '{"fichiers":"'.listeArchives($extension_kmz, $boatname).'"}';
}
else
{
    echo '{"erreur":"Bateau inconnu"}';
}


// ################################################### FONCTIONS DIVERSES
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


// ------------------
function listeArchives($extension, $boatname){
	$s='';
    $boat='';
    if(!empty($boatname))
	{
		$boat=str_replace(' ','',$boatname); // Purger les espaces
	}
	// DEBUG
	// echo "<br>Boat: $boat\n";

	$tobj=array();
	$sep = '/';
	$path=".";

	$h1=opendir($path);
    $nobj = 0;

    while ($f = readdir($h1) )
    {
		if (($f != ".") && ($f != "..")) {
			// Les fichiers commençant par '_' ne sont pas affichés
			// Ni le fichier par defaut ni le fichier de cache ne sont affichés
			// Les fichiers ne commençant pas par le nom par defaut ne sont pas affichés
			// les fichier n'ayant pas la bonne extension ne sont pas affichés
	        if (!is_dir($path.$sep.$f)){
				// KML
				$g=preg_replace('/'.$extension.'/', '', $f); //
				// DEBUG
				// echo "<br>g:$g  g+:$g$extension_kml  f:$f\n ";
				if (
					(substr($g,0,1) != "_") // Les fichiers commençant par '_' ne sont pas affichés
					&&
					(strtoupper($g.$extension) == strtoupper($f)) // les fichier n'ayant pas la bonne extension ne sont pas affichés
					&&
					(empty($boat) || preg_match('/'.$boat.'/',$g)) // les fichiers ne contenant pas le nom du bateau ne sont pas affichés
				)

				{
	               	$s .= $f.',';
                    $nobj ++;
				}
			} // fin traitement d'un fichier
		} // fin du test sur entrees speciales . et ..
	}  // fin du while sur les entrees du repertoire traite

	closedir($h1);
	return substr($s,0,-1); // Chasser la dernière virgule
}



?>