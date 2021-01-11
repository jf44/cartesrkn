<?php
// repris de get_kmz.php
// acces direct aux fichiers du dossier
//  Retourne une liste de fichiers KMZ
$extension_kmz='.kmz'; // lue par Google Earth
$boatname='';
$otherboat='';
$choice='';

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

// COOKIES ?
if (isset($_COOKIE["rknnomboat"]) && ($_COOKIE["rknnomboat"]!='')){
	$boatname=$_COOKIE["rknnomboat"];
}

if (!empty($_GET['boatname']))
{
	$boatname=to_utf8($_GET['boatname']);      // to_utf8($_GET['params'])
	$choice='Mine';
}

if (!empty($_POST['choice']))
{
	$choice=to_utf8($_POST['choice']);      // to_utf8($_GET['params'])
}

if (!empty($_POST['otherboat']))
{
	$otherboat=to_utf8($_POST['otherboat']);      // to_utf8($_GET['params'])
	if (!empty($otherboat) && empty($boatname))
	{
		$boatname=$otherboat;
	}
	$choice='Other';
}

if (!empty($boatname))
{
    setcookie("rknnomboat", $boatname, time()+24*3600);  /* expire in a day */
}

entete();
echo '<div id="bandeauDiv"><h1 class="bandeau">Vos bateaux amis du Vendée Globe virtuel 2020 dans Google Earth</h1></div>
';
// DEBUG
//echo "BOATNAME : $boatname, OTHERBOAT : $otherboat, CHOICE : $choice <br />\n";

echo '
<div id="centerDiv">
';
if (!empty($boatname))
{
	echo '<p>Bonjour <i><b>'.$boatname.'</b></i></p>'."\n";
}
echo '<form action="'.$appli.'" method="post">
';
echo '<p>Vous pouvez</p><ul>';
echo '<li>Soit saisir le nom d\'un bateau pour ne voir que ses propres cartes<br />';
echo '<input type="text" name="otherboat" size="20" maxsize="30" value="" />';
echo '<input type="hidden" name="choice" value="Other" />';
echo '</li>';

if (!empty($boatname))
{
	echo '<li>Soit sélectionnez vos propres cartes ';
	echo '<input type="radio" name="choice" value="Mine" />';
    echo '</li>';
}
    echo '<li>Soit demander à voir toutes les cartes ';
	echo '<input type="radio" name="choice" value="All" checked />';
	echo '</li></ul>';

echo'
<input type="reset" /> &nbsp; &nbsp; &nbsp; &nbsp;
<input type="submit" value="Valider" />
</form>
';


if (!empty($boatname) && !empty($choice) && ($choice=="Mine"))
{
	display(listeArchives($extension_kmz, $boatname));
}
else if (!empty($otherboat) && !empty($choice) && ($choice=="Other"))
{
	display(listeArchives($extension_kmz, $otherboat));
}
else if (!empty($choice) && ($choice=="All")){
	display(listeArchives($extension_kmz, ""));
}
echo '</div>
';
enqueue();

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
function listeArchives($extension, $boatname=""){
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

// -------------------
function display($str)
{
	global $url_serveur_local;
	if (!empty($str))
	{
		$res= explode(",",$str);
        $nobj=count($res);
		$urlbase = $url_serveur_local;
		echo '<h3>Liste des fichiers KMZ</h3>
<p>Les cartes G.E. sont distribuées au format KMZ indépendant du serveur</p>
<table class="small">
<tr><th class="small" colspan="5" align="center">Archives</th></tr>
<tr valign="top" bgcolor="#ffffff">'."\n";
    	$colonne=0;
		$i=0;
    	$max_colonne=min($nobj,5);
		foreach ($res as $item)
		{
        	$url=$urlbase.'/'.$item;
            echo '<td class="small"><a class="small" href="'.$url.'">'.$item.'</a></td>'."\n";
	        $colonne++;
			$i++;
			if ($colonne>=$max_colonne){
               	echo '</tr>'."\n";
				if ($i<$nobj)
				{
            		echo '<tr valign="top" bgcolor="#ffffff">'."\n";
				}
        		$colonne=0;
			}
    	}
		while($colonne<$max_colonne){
       		echo '<td class="small" >&nbsp;</td>'."\n";
			$colonne++;
		}
       	echo '</tr>'."\n";
	}
	else
	{
		echo '<h3>Liste des fichiers KMZ</h3>
<p>Aucun fichier ne correspond à vos choix</p>
';

	}
}


//----------------------------------
function entete(){
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="Author" content="JF">
<meta name="description" content="Vos bateaux dans Google Earth">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="author" title="Auteur" href="mailto:jean.fruitet@free.fr">
<title>Vos bateaux dans Google Earth</title>
<meta name="ROBOTS" content="none,noarchive">
<link href="../style.css" rel="stylesheet" type="text/css">
</head>
<body>
';
}


//----------------------------------
function enqueue(){
echo '
<div id="baspageDiv"><a class="bandeau" href="mailto:jean.fruitet@free.fr?subject=Cartes GE">(c) JF</a></div>

</body>
</html>
';
}

?>