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

// Inclus dans set_positions.php

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

$boat_type='IMOCA'; // Le modèle 3D de voilier IMOCA à foils de la VGv,
// Différent du $team sauf pour le groupe RKN
// Pour changer le modèle voilier il faut creer des modeles 3D différents
$scale=6;		// valeur d'echelle des voiliers 3D par defaut

$boatname=''; // le bateau qui collecte les données
$course='VGv2020';
$team='RKN';

$extension_csv='.csv';
$datadir = '/positions/';             // Contient les fichiers des positions csv. Doit exister au prealable
$classementdir = '/classement/'; // Doit exister au prealable

$datadirjson = '/trajectoires/'; // Les fichiers csv des positions y sont archivés. Doit exister au prealable
$extension_json='.json'; // Pour les classements et le strajectoires
$colorsdir = '/data/';

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

$t_fichiers_dae = array();    // Optimisation des fichiers de modèles
$t_fichiers_texture = array();// Optimisation des fichiers de texture

// Langue utilisateur (n'est pas vraiment utilisé dans cette version).
$langue_user="FR";
$t_voilier=array(); // la liste des voiliers dont les positions sont chargées

$t_trajectoire=array(); // la trajectoire du bateau principal
$t_parcours=array();

$horaire_fichier='';
$liste='';



/***************************************************************/
/*                         GNERATION DU FICHIER KMZ            */
/* *************************************************************/

//-----------------
function genere_kmz($inputdir, $nfile)
// assume que les positions sont disponibles dans un fichier ad hoc
{
global $boatname;
global $course;
global $team;
global $fichier_kml_cache;
global $fichier_kml_courant;
global $t_voilier;
global $dossier_3d;
global $correction_geodesique;
global $url_serveur;
global $boat_type;
global $scale;

$s='';
// echo '<p>DEBUG :: compute_kmz.php :: 100 :: Boatname = <code>'.$boatname.'</code></p>'."\n";

if (!empty($boatname) && !empty($course) && !empty($team) && !empty($inputdir) && !empty($nfile))
{
	$fichier_kml_cache=str_replace(' ','',$course.'_'.$team.'_'.$boatname);
    $fichier_kml_courant=str_replace(' ','',$course.'_'.$team); // celui qui est lu par Google Earth; il serait utile de pouvoir modifier ce prefixe depuis le programme
	// Structure d'accueil pour les données
	creer_dossier_kml();
    get_boats_colors(); // Lit les codes de couleurs des voiles dans un fichier

	// DEBUG
//	echo "\ncompute_kmz.php :: 111 : NomBoat=".$boatname." Course=".$course." Groupe=".$team." File:".$nfile."\n";
    // Fichier des positions de la forme
//    echo "\nINPUT FILE : ".$inputdir.$nfile."\n";

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
        return (EnregistreKML_3D($dossier_3d, $s));
	}
}
return "";
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


