<?php
// renomme_upper.php
// Renomme tous les fichiers du repertoire en majuscules
//
$extension='.json'; // fichiers de position

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
renommeArchives($extension);
echo "Terminé";

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
	{
		$pere = $path;
	}
	return $pere;
}




// ------------------
function renommeArchives($extension){
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
				if (strtoupper($g.$extension) == strtoupper($f)) // les fichier n'ayant pas la bonne extension ne sont pas affichés
				{
	               	$tobj[] = $g;
                    $nobj ++;
				}
			} // fin traitement d'un fichier
		} // fin du test sur entrees speciales . et ..
	}  // fin du while sur les entrees du repertoire traite

	closedir($h1);
	foreach ($tobj as $afile)
	{
		rename($afile.$extension, strtoupper($afile).$extension);
	}
}



?>