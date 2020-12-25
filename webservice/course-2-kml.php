<?php
// JF 2020
// Génère la carte des positions des bateaux du Rienkanou dans Google Earth en exportant un fichier KML
// Evolution des versions de 2008 et de 2016 sans utilisation d'une base de données
// 2020 : Capture des données grace à l'extension Chrome VR Dashboard
// Les données de course sont lues dans un fichier ./positions/course_team_skipper-aaaammjj_hhmm.csv
// Les trajectoires dans le fichier
// Un script color_picker.html permet aux utilisateur d'indiquer les couleurs de leur bateau (coque, pont, GV, voile d'avant, Spi)

// Exemple d'appel :
// http://localhost:8080/voilevirtuelle/vgv2020/ge/cartes_rkn/webservice/course-2-kml.php?boatname=jf44-RKN&course=VGv2020&team=RKN&nfile=VGv2020_RKN_jf44-RKN-20201218_0530.csv
// http://localhost:8080/voilevirtuelle/vgv2020/ge/cartes_rkn/webservice/course-2-kml.php?boatname=jf44-RKN&course=VGv2020&team=RKN&nfile=VGv2020_RKN_jf44-RKN_20201218_1400.csv

define ('CORRECTION_GEODESIQUE_MP2GE', 1);      // appliquer une correction sur les latitudes
define ('COEFF_C0RRECTION_MP2GE_HORN', 1.00037);      // appliquer une correction sur les latitudes

require_once('include/functions.php');

require_once('include/zip.php'); // utilise la bibliotheque pclzip
require_once('include/GeoCalc.class.php'); // pour le calcul de distance par GrandCercle
require_once('include/Voilier.php'); // Définition de la classe Voilier
require_once('include/kml_trajectoire.php'); // Génération KML de la trajectoire 
require_once('include/kml_3d.php'); // Génération KML des bateaux comme des modeles 3D
require_once('include/get_positions.php'); // Position géographique et allure des bateaux
require_once('include/un_classement.php'); // Classement des bateaux

// VARIABLES GLOBALES
if (CORRECTION_GEODESIQUE_MP2GE){      // appliquer une correction sur les latitudes
	$correction_geodesique=COEFF_C0RRECTION_MP2GE_HORN;
}
else{
    $correction_geodesique=1.0;
}

$boat_type='RKN'; // Le modèle 3D de voilier IMOCA à foils de la VGv,
// Différent du $team sauf pour le groupe RKN
// Pour changer le modèle voilier il faut creer des modeles 3D différents

$boatname=''; // le bateau qui collecte les données
$course='VGv2020';
$team='RKN';
$scale=6;		// valeur d'echelle des voiliers 3D par defaut

$extension_csv='.csv';
$extension_kmz='.kmz';
$extension_json='.json';  // Fichier JSON des trajectoires course_boatname.txt
// {"boatname":"boatname","trajectoire":[{"lon":"val", "lat":"val"}, {"lon":"val", "lat":"val"},{"lon":"val", "lat":"val"}]}

$colorsdir = '/data/';
$datadir = '/positions/'; // Les fichiers csv des positions y sont archivé. Doit exister au prealable
$datadirjson = '/trajectoires/'; // Les fichiers csv des positions y sont archivés. Doit exister au prealable

$classementdir = '/classement/'; // Doit exister au prealable

$date_data=gmdate("Y-m-d", time()); // A priori c'est le jour de la capture
$date_stamp=gmdate("Ymd_", time());


$version="<b>Version 0.1</b> du 16/12/2020"; // Il faut bien un numéro de version
$auteurs='jf44 &lt;<a href="mailto:jean.fruitet@free.fr?subject=VGv20">jean.fruitet@free.fr</a>&gt; - Xo Lub &lt;xolub62@gmail.com&gt; Math Kobis &lt;pertobast@free.fr&gt;';
$MAXTAILLECACHE='1024';

$dossier_kmz = 'kmz'; // Les fichiers kmz y sont archivés.
$dossier_3d='rkn3d';// Le dossiers de modèles de bateaux
$dossier_3d_cache=$dossier_3d; // initialise avec la date par un appel de fonction
$extension_dae='.dae'; // fichier COLLADA
$extension_kmz='.kmz'; // lue par Google Earth
$extension_kml='.kml'; // lue par Google Earth
$dossier_textures='textures';
$dossier_modeles='models';
$dossier_marques='marques';
$fichier_marques='MarquesParcoursVGV2020.kml';

// Langue utilisateur (n'est pas vraiment utilisé dans cette version).
$langue_user="FR";
$t_voilier=array(); // la liste des voiliers dont les positions sont chargées

$t_trajectoire=array(); // la trajectoire du bateau principal
$t_parcours=array();

// ###################### Initialisation variables PHP

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
	$uri = 'https://';
} else {
	$uri = 'http://';
}
//$url_serveur_local = $uri.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].get_url_pere($_SERVER['SCRIPT_NAME']);
$url_serveur_local = $uri.$_SERVER['HTTP_HOST'].get_url_pere($_SERVER['SCRIPT_NAME']);
// DEBUG
// echo "<br>URL : $url_serveur_local\n"; 

$dir_serveur = dirname($_SERVER['SCRIPT_FILENAME']); 
// DEBUG
// echo "<br>Répertoire serveur : $dir_serveur\n";
// Nom du script chargé dynamiquement.
$appli=$_SERVER["PHP_SELF"];

$inputdircolors = $dir_serveur.$colorsdir;
$inputdir = $dir_serveur.$datadir;
$outputdir = $dir_serveur.$datadir;
$outputdirclassement = $dir_serveur.$classementdir;
$output='';

////////////////////////// DEBUT DU PROGRAMME ////////////////////////
// Définit le fuseau horaire par défaut à utiliser. Disponible depuis PHP 5.1
// affiche le numéro de version courante du PHP.
if (false) {
	echo "Version PHP courante : " . phpversion();
}
if (phpversion()>="5.2"){
	date_default_timezone_set('UTC');
}

//heure    minute seconde jour mois annee
//$to_ten_minutes  = mktime(gmdate("G"), gmdate("i")+10, 0, gmdate("m"), gmdate("d"), gmdate("Y"));
$to_one_hour  = mktime(gmdate("G")+1, 0, 0, gmdate("m"),gmdate("d"),gmdate("Y"));
//$from_ten_minutes  = mktime(gmdate("G"), gmdate("i")+10, 0, gmdate("m"), gmdate("d"), gmdate("Y"));
$from_one_hour  = mktime(gmdate("G"), 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"));
$date_cache_one_hour=gmdate("Y-m-d H:i:s",$from_one_hour);
//$date_cache_ten_minutes=gmdate("Y-m-d H:i:s",$from_ten_minutes);
//$str_time_cache_ten_minutes = " de dix minutes ";
$str_time_cache_one_hour = " d'une heure ";

// Dix minutes non completement implanté. A TERMINER... dans ExisteKML
//$date_cache=$date_cache_ten_minutes;
//$to_next_time_cache = $to_ten_minutes; // Cela pourrait être laissé à l'utilisateur
//$time_cache = $from_ten_minutes;
//$str_time_cache = $str_time_cache_ten_minutes;
// Seul le cache de 60 minutes est implanté car plus simple
$date_cache=$date_cache_one_hour;
$to_next_time_cache = $to_one_hour; // Cela pourrait être laissé à l'utilisateur
$time_cache = $from_one_hour;
$str_time_cache = $str_time_cache_one_hour;


if (!empty($_GET['boatname']))
{
	$boatname=to_utf8($_GET['boatname']);      // to_utf8($_GET['params'])
}
if (!empty($_GET['course']))
{
	$course=to_utf8($_GET['course']);
}
if (!empty($_GET['team']))
{
	$team=to_utf8($_GET['team']);
}
if (!empty($_GET['scale']) && ($_GET['scale']!='') && ($_GET['scale']>0))
{
	$scale=(int)to_utf8($_GET['scale']);
}
if (!empty($_GET['nfile']))
{
	$nfile=to_utf8($_GET['nfile']);  // nom du fichier de positions à traiter
}

// print_r($_POST);
if (!empty($_POST['boatname']))
{
	$boatname=to_utf8($_POST['boatname']);      // to_utf8($_GET['params'])
}
if (!empty($_POST['course']))
{
	$course=to_utf8($_POST['course']);
}
if (!empty($_POST['team']))
{
	$team=to_utf8($_POST['team']);
}
if (!empty($_POST['scale']) && ($_POST['scale']!=''))
{
	$scale=(int)to_utf8($_POST['scale']);
}
if (!empty($_POST['liste']))
{
	$liste=to_utf8($_POST['liste']);
}
if (!empty($_POST['nfile']))
{
	$nfile=to_utf8($_POST['nfile']);  // nom du fichier de positions à traiter
}



/***************************************************************/
/*                         GNERATION DU FICHIER KMZ            */
/* *************************************************************/

if (!empty($boatname) && !empty($course) && !empty($team) && !empty($nfile))
{
	$fichier_kml_cache=$course.'_'.$team.'_'.$boatname;
    $fichier_kml_courant=$course.'_'.$team; // celui qui est lu par Google Earth; il serait utile de pouvoir modifier ce prefixe depuis le programme
	// Structure d'accueil pour les données
	creer_dossier_kml();
    get_boats_colors(); // Lit les codes de couleurs des voiles dans un fichier

	// DEBUG
	//echo "\ncourse-2-kml.php :: 187 : NomBoat=".$boatname." Course=".$course." Groupe=".$team." File:".$nfile."\n";
    // $dir_name=$dir_serveur.$datadir;
	// Fichier des positions de la forme
    //echo "\nINPUT FILE : ".$inputdir.$nfile."\n";

	// Liste des positions des voiliers
	$t_voilier=get_positions($inputdir.$nfile);   // lecture dans le fichier csv ad hoc

	if (isset($t_voilier) && is_array($t_voilier) && (count($t_voilier)>0))
	{
		$s=GenereEnteteKML_3D($t_voilier[0]->longitude, $t_voilier[0]->latitude * $correction_geodesique, $t_voilier[0]->cog);
		$s.=GenereMarquesParcoursEtDebutPositionsBateauxKML_3D(true);
		$s.='	<Folder>
		<open>1</open>
		<name>Positions des voiliers</name>
';
        foreach($t_voilier as $a_voilier)
		{
			if (!empty($a_voilier))
			{
				$a_voilier->latitude *= $correction_geodesique;  // tenir compte de la correction de latitude
				$s.=GenereBateauKML_3D($dossier_3d, $url_serveur, $boat_type, $a_voilier, $scale, $scale*150);
			}
		}
		$s.='	</Folder>'."\n";
		$s.='	<Folder>
		<open>1</open>
		<name>Trajectoires des voiliers </name>
';
		foreach($t_voilier as $a_voilier)
		{
            if (!empty($a_voilier))
			{

				/*
				// La trajectoire est lue dans un fichier JSON
				$s_trajectoire='';
			    $dir_name=$dir_serveur.$datadirjson;
 				// fichier à charger
			    $f_name=$dir_name.$course.'_'.$a_voilier->nomboat.$extension_json;
				// Inutile car la trajectoire est déjà intégrée dans le voilier
				$s_trajectoire=get_trajectoire_json($f_name);   // lecture dans le fichier json ad hoc
				$a_voilier->SetTrajectoireKML($s_trajectoire);
				*/
				$a_voilier->latitude *= $correction_geodesique;  // tenir compte de la correction de latitude
				$s.= GenereTrajectoireBateauKML($a_voilier, $scale*150);
     		}
		}
		$s.='	</Folder>'."\n";

		$s.=GenereTourBateauxKML($t_voilier, $scale);  // génère la visite guidée  du premier au dernier.
		$s.=GenereEnQueueKML_3D();

		// Production du fichier KMZ
        EnregistreKML_3D($dossier_3d, $s, true, false);
	}
}




// ################################################### FONCTIONS DIVERSES

//------------------------
function creer_dossier_kml(){
	// Crée un dossier unique pour archiver les donnees KML
	global $dir_serveur;
	global $dossier_3d;
	global $dossier_textures;
	global $dossier_modeles;
    global $dossier_marques;
	global $dossier_kmz;

	$dir_name=$dir_serveur.'/'.$dossier_kmz;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}

    $dir_name=$dir_serveur.'/'.$dossier_3d;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
	// DEBUG
	//echo "\n vgv20-2-kml.php :: 1120 :: Dir_Name=".$dir_name."<br />\n";
	$dir_name=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_modeles;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
	// DEBUG
	//echo "\n1126 :: Dir_Name=".$dir_name."<br />\n";
	$dir_name=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
	// DEBUG
	//echo "\n1126 :: Dir_Name=".$dir_name."<br />\n";
	$dir_name=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_marques;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
    recopier_marques_parcours($dossier_3d);
	// DEBUG
	//echo "\n1131 :: Dir_Name=".$dir_name."<br />\n";
	$dossier_3d_cache=$dossier_3d.'_'.gmdate("YmdH");
	$dir_name=$dir_serveur.'/'.$dossier_3d_cache;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
	// DEBUG
	//echo "\n1139 :: Dir_Name=".$dir_name."<br />\n";
	$dir_name=$dir_serveur.'/'.$dossier_3d_cache.'/'.$dossier_modeles;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
    // DEBUG
	//echo "\n1145 :: Dir_Name=".$dir_name."<br />\n";
	$dir_name=$dir_serveur.'/'.$dossier_3d_cache.'/'.$dossier_textures;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
// DEBUG
	//echo "\n1151 :: Dir_Name=".$dir_name."<br />\n";
	$dir_name=$dir_serveur.'/'.$dossier_3d_cache.'/'.$dossier_marques;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
 	recopier_marques_parcours($dossier_3d_cache);
}


// -----------------------
function ExisteKML(){
	// verifie si une generation a ete faite durant l'heure courante
	return ExisteKML_3D();
}


// -----------------------
function selection_langue($langue_user){
// selection
echo '
Sélectionnez votre langue : <select name="langue_user">
';
switch ($langue_user){
case 'FR' : echo '
<option value="FR" SELECTED>FRANCAIS</option>
<option value="EN">ENGLISH</option>
<option value="SP">SPANISH</option>
<option value="DE">DEUTCH</option>
</select>
';
break;
case 'EN' : echo '
<option value="EN" SELECTED>ENGLISH</option>
<option value="FR">FRANCAIS</option>
<option value="SP">SPANISH</option>
<option value="DE">DEUTCH</option>
</select>
';
break;
case 'SP' : echo '
<option value="SP" SELECTED>SPANISH</option>
<option value="FR">FRANCAIS</option>
<option value="EN">ENGLISH</option>
<option value="DE">DEUTCH</option>
</select>
';
break;
case 'DE' : echo '
<option value="DE" SELECTED>DEUTCH</option>
<option value="FR">FRANCAIS</option>
<option value="EN">ENGLISH</option>
<option value="SP">SPANISH</option>
</select>
';
break;
default : echo '
<option value="FR">FRANCAIS</option>
<option value="EN">ENGLISH</option>
<option value="SP">SPANISH</option>
<option value="DE">DEUTCH</option>
</select>
';
break;
}
}


// --------------
    if (!function_exists('json_last_error_msg')) {
        function json_last_error_msg() {
            static $ERRORS = array(
                JSON_ERROR_NONE => 'No error',
                JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
                JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
                JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
                JSON_ERROR_SYNTAX => 'Syntax error',
                JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
            );

            $error = json_last_error();
            return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
        }
    }


