<?php
// set_positions.php
// Version webservice de file_charge_data.php
// DVR Dashvoard est une extension Chrome interfacée avec Virtual Regatta qui capture els données d'un bateau et les transmet àau roteur ZeZo
// Les données se présentent sous forme d'un tableau avec séparateur tab et label des données en tête
// Cette page permet la capture du tableau par copier coller
// Les données sont placées dans un fichier .csv du dossier csv
// Nom du fichier produit : course_team_skipper-yyyymmddhhgg.csv
// A IMPLANTER : jout d'une fonction de classement de groupe par rapport au bateau "boatname" avec la DTU
// C'est forcément un post traitement
// Cette version n'utilise pas de base de données
// Elle crée un fichier de position ./positions/course_team_skipper-yyyymmddhhgg.csv, des fichiers de trajectoires ./trajectoires/course_skipper.json
// A IMPLANTER : et un fichier de classement ./classement/classement-course_team_skipper-yyyymmddhhgg.csv


// Exemple d'appel :
// http://localhost:8080/voilevirtuelle/vgv2020/ge/cartes_rkn/webservice/set_positions.php?boatname=jf44-RKN&course=VGv2020&team=RKN&nfile=VGv2020_RKN_jf44-RKN-20201218_2320.csv
// http://localhost:8080/voilevirtuelle/vgv2020/ge/cartes_rkn/webservice/set_positions.php?boatname=Iroizo C-RKN&course=VGv2020&team=C-RKN&nfile=VGv2020_C-RKN_Iroizo C-RKN_20201221_1200.csv

/*
Lors de la capture le séparateur /tab doit être remplacé par ; et le symbole '⎈' par ''
RT;Skipper;Last Update;Rank;DTF;DTU;BRG;Sail;State;Position;HDG;TWA;TWS;Speed;Factor;Foils;Hull
;bigouden wind RKN;8:20:00 UTC;-;(618.8);-223.5;133.1°;Jib;racing;41°52'29.84"N 14°40'37.87"W;265.0;50.1;29.6;11.62;1.0000;?;no
;Bourne34 RKN;8:20:00 UTC;-;(763.6);146.1;182.8°;C0 (Auto);racing;42°02'48.71"N 18°29'22.03"W;212.0;114.4;18.4;20.13;1.0431;100%;100%
;City of le MANS RKN;8:20:00 UTC;-;(753.9);153.7;177.2°;C0 (Auto);racing;41°55'13.35"N 18°09'39.83"W;208.2;120.8;19.2;20.49;1.0431;100%;100%

*/
// Variables spécifique du programme d'acquisition
// Ici pour VR Dashboard

include('include/compute_kmz.php');

////////////////////////// DEBUT DU PROGRAMME ////////////////////////

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
$outputfile="";
$output='';

$date_data=gmdate("Y-m-d", time()); // A priori c'est le jour de la capture
$date_stamp=gmdate("Ymd_", time());

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
	$scale=(float)to_utf8($_POST['scale']);
}
if (!empty($_POST['liste']))
{
	$liste=to_utf8($_POST['liste']);
}
if (!empty($_POST['nfile']))
{
	$nfile=to_utf8($_POST['nfile']);  // nom du fichier de positions à traiter
}


// Fichier en sortie
$outputfile=$course.'_'.$team.'_'.$boatname;
$outputfile=str_replace(' ','',$outputfile); // Pas d'espace dans les noms de fichiers !

if (!empty($liste) && !empty($boatname))
{
	// Traitement des données VR Dashboard
	if ($lines=explode("\n", $liste))
	{
		purge($lines, true, true); // ajout de \n en fin de ligne ; sauvegarde forcée dans un fichier .CSV
	}

	if (!empty($output))
	{
		if (!empty($horaire_fichier))
		{
			$outputfile.='_'.$date_stamp.$horaire_fichier;
		}
		else
		{
    		$outputfile.='_'.$date_stamp."_".gmdate("Hi", time());
		}
		// echo '<p>Les données sont exportées dans le fichier <code>'.$datadir.$outputfile.$extension_csv.'</code></p>'."\n";
    	if ($nom_complet=enregistre_data($output, $outputdir, $outputfile, $extension_csv))
		{
			// Conversion du fichier de positions en fichier kmz
        	if ($nfilekmz = genere_kmz($outputdir,$outputfile.$extension_csv))
			{
            	echo urlencode($url_serveur_local.'/'.$nfilekmz);
			}
			exit;
		}
		else
		{
 			echo 'empty file';
			exit;
		}
    }
	else
	{
 		echo 'empty data';
		exit;
	}
}
else if (!empty($nfile) && !empty($boatname))
{
	// Traitement du fichier

	if (!empty($outputdir.$nfile))
	{
		//echo "176 :: '".$outputdir.$nfile."'\n";
        if (file_exists($outputdir.$nfile))
		{
			if ($lines = file($outputdir.$nfile))
			{
                //echo "182\n";
				//print_r($lines);

				purge($lines, false);   // Modifie $output par effet de bord
				// A priori ce traitement n'est pas nécessaire car le fichier à traiter existe
				// Mais en cas d'erreur dans ce fichier la génération de la carte ne sera pas effectuée...
			}
			if (!empty($output))
			{
            	//echo "188\n OUTPUT ".$output;

				//echo urlencode($nfile);
        		if ($nfilekmz = genere_kmz($outputdir,$nfile))
				{
                    echo urlencode($url_serveur_local.'/'.$nfilekmz);
				}
				exit;
			}
		}
	}
	else
	{
 		echo 'empty data';
		exit;
	}
}


/*

// Classement
if (!empty($t_positions))
{
	if ($json=classement_json($t_positions))
	{
		// Sauvegarde du classement
		// echo '<pre>'.$json.'</pre>'."\n";
		if (!empty($horaire_fichier))
		{
			// 14:20:00 UTC
	    	$outputfile=$course.'_'.$team.'_'.$boatname.'_classement-'.$date_stamp.$horaire_fichier;
		}
		else
		{
			$outputfile=$course.'_'.$team.'_'.$boatname.'_classement-'.$date_stamp."_".gmdate("Hi", time());
		}
    	enregistre_data($json, $classementdir, $outputfile, $extension_json);
        enregistre_data(classement_json2csv($json), $classementdir, $outputfile, $extension_csv);

		echo "<br /><pre>".classement_json2csv($json)."</pre>\n";
		echo '<p>Le classement est exporté dans les fichiers <a href="'.$url_serveur_local.$classementdir.$outputfile.$extension_json.'"><i>'.$outputfile.$extension_json.'</i></a>'."\n";
		echo 'et <a href="'.$url_serveur_local.$classementdir.$outputfile.$extension_csv.'"><i>'.$outputfile.$extension_csv.'</i></a></p>'."\n";

	}
}

*/


// -----------
function purge_utc($last_update){
// supprime la fin de la chaine  '2020-11-09 14:05:00 UTC'
	return (trim(preg_replace("/UTC/i","",$last_update)));
}

// -----------
function purge($lines, $terminateur=false){
// Supprime quelques codes inutiles et reformat les données en sortie.
// Si terminateur == true ajoute \n en fin de chaque ligne

	global $horaire;
    global $horaire_fichier;
    global $date_data;
	global $date_stamp;
	global $course;
    global $team;
	global $boatname;
	global $output;

	if (!empty($lines))
	{
        $ok=false;
		$output='';
		$rt='';
		$skipper=''; //  nom de connexion du boatname i.e. le nom de son bateau sur la course
		$last_update=''; // Date de mise à jour au format HH:MM:SS UTC
		$rank=''; // rang dans VR
		$dtf=''; // DTF : Distance To Finish = distance pour finir = 23024.51 ou (351.4) ou autre chose =''; bizarre
		$dtu=''; // DTU : Distance To User
		$brg=''; // BRG : Bearing To Destination = cap à suivre pour le prochain WP   = 148.9°
		$sail=''; // Sail : "jib", "LJ" | "LJ (Auto)" = Light Jib, "C0" | "C0(Auto)" code zero,  Spi, HG
		$state=''; // State : "racing" ou autre chose si beeché ?
		$position=''; // latitude longitude en ° 44°36'41.94"N 8°42'36.81"W
		$hdg=''; // HDG : Heading : cap du navire (différent de cog si il y adu courant ou de la dérive
		$twa='';       // TWA   True Wind Angle = direction du vent
		$tws='';       // TWS   True Wind Speed = vitesse du vent
		$speed='';       // v   itesse du bateau après application des différents coefficients correcteur
		$factor='';  // 1.x appliqué en fonction des options
		$foil=''; // efficacité % permettant probablement d'appliquer un facteur de vitesse.
		$hull='';       // N ou 100% : Polish ou pas sur la coque en % d'efficaccité
		$a_date='';

		//echo '<pre>'."\n";

	    foreach ($lines as $line_num => $line)
		{
			//echo "$line\n";
			if (!empty($line))
			{
 				// $pattern= array('/ +/','/\t+/');
				// $replace= array(' ',' ');

    			$pattern= array('/âŽˆ/','/⎈/','/Â°/','/\t/');
				$replace= array('#','#','°',';');
    			$line=preg_replace($pattern, $replace, $line); //
				// if (!preg_match("/#/",$line)){    // commentaire
                if (!empty($line))
				{
					$data=explode(';',$line);    // separateur ';'
					if (false)
					{
						echo "<pre>$line</pre>\n";
						echo "<br />\n";
						print_r($data);
					}
					$n=count($data);
// RT;Skipper;Last Update;Rank;DTF;DTU;BRG;Sail;State;Position;HDG;TWA;TWS;Speed;Factor;Foils;Hull
// $rt;$boatname;$last_update;$rank;$dtf;$dtu;$brg;$sail;$state;$position;$hdg;$twa;$tws;$speed;$factor;$foils;$hull
//
//  0  1       2    3      4    5   6   7   8    9     10       11  12  13  14    15     16    17
					if ($n>=17)
					{
					// RT;Skipper;Last Update;Rank;DTF;DTU;BRG;Sail;State;Position;HDG;TWA;TWS;Speed;Factor;Foils;Options
					// #;AbsoluteDreamer;2020-11-29 18:40:00 UTC;-;(5326.0);1705.5;121.0°;C0;racing;42°16'21.78"S 2°34'47.59"W;109.9;128.9;23.3;21.27;1.0400;100%;?

					//           #;   jf44-RKN; 2020-11-26 08:10:00 UTC;296160;20601.56;0.0;  -°;   Jib;   racing; 13°47'42.00"S 33°46'49.37"W; 192.0; 93.6; 15.2; 15.30;  1.0030;  no;     hull,light,radio,skin
						if (list($rt, $skipper, $last_update, $rank, $dtf, $dtu, $brg, $sail, $state, $position, $hdg,  $twa, $tws, $speed, $factor, $foils, $hull ) = $data)	{
							//echo "<br />$rt, $skipper, $last_update, $rank, '$dtf', DTU:'$dtu', $brg, $sail, $state, $position, $hdg, $twa, $tws, $speed, $factor, $foils, $hull\n";

	                         $sauve=false;
							// if (!empty($last_update) && ($last_update!='Last Update'))

                            if (!empty($last_update) && (strpos($last_update, 'Last Update')===false) )
							{
								// 14:20:00 UTC
								// $date data 2020-11-12 fournie comment ?
								// $last_update 22:50:00 UTC
                                $last_update = str_replace("  ", " ",$last_update);

								//echo "\n<br />last-update: '$last_update'\n";
        						$tab_date=explode(" ", $last_update);
								// print_r($tab_date);
								if (count($tab_date)>2)
								{
                                    $date_data=trim($tab_date[0]);
									$horaire=trim($tab_date[1]);
									$utc=trim($tab_date[2]);
								}
								else
								{
									$horaire=trim($tab_date[0]);
									$utc=trim($tab_date[1]);
								}
                                //echo "\n<br />date_data: '$date_data'\n";
								//echo "\n<br />horaire: '$horaire'\n";
                                //echo "\n<br />utc: '$utc'\n";

								list($heure,$minute,$seconde) = explode(":", $horaire);
								if (strlen($heure)==1) $heure="0".$heure;
                                if (strlen($minute)==1) $minute="0".$minute;
                                if (strlen($seconde)==1) $seconde="0".$seconde;
								$horaire = $heure.$minute.$seconde;
                                $horaire_fichier = $heure.$minute;
								$last_update=trim($date_data).' '.trim($heure).':'.trim($minute).':'.trim($seconde).' '.trim($utc);
								$sauve=true;							}

                            $output.="$rt;$skipper;$last_update;$rank;$dtf;$dtu;$brg;$sail;$state;$position;$hdg;$twa;$tws;$speed;$factor;$foils;$hull";
							if ($terminateur)
							{
                                $output.="\n";
							}
							if ($sauve)
							{
                                $last_update=purge_utc($last_update);
								// Bug à corriger
                                // echo "<br />'$dtu'\n";
								if ((trim($dtu)=='-') || (preg_match("/[0-9]+/",trim($dtu)) === false))
								{
                                    //echo "<br /> '$dtu' ";
									$dtu='0.0';
							       	//echo " --&gt; '$dtu'\n";
								}
								if (($twa!='-') && ($tws!='-') && ($speed!='-')) // Certains bateaux dont les rééel n'ont pas ces informations attachées
                            	{
									$rposition = new stdClass();
									$rposition->boatname=$skipper;
									$rposition->last_update=$last_update;
									$rposition->rangvr=$rank;
                                    $rposition->rangrkn=0;
									$rposition->latlon=$position;
									$rposition->dtu=$dtu;
									$t_positions[]=$rposition;

									// Enregistrer la trajectoire
                                    $lonlat=get_lonlat($position);
                                    set_trajectoire_json($skipper, $course, $lonlat);
								}
							}
						}
					}
					else
					{
						/*
						echo '<br />Erreur de données à la ligne '.$line_num.' !'."\n";
                        echo '<br /><i>'.$line.'</i>'."\n";
                        echo '<br />Nombre de champs incorrect.'."\n";
						break;
						*/
					}
				}
			}
		}
	}
}

// ----------------------------
class UploadException extends Exception
{
    public function __construct($code) {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }

    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }
}


// -----------------------
function enregistre_data($contenu, $outputdir, $outputfile, $extension){
// Un fichier de positions est crée.
	$f_name=$outputdir.$outputfile.$extension;
	$fp_data = fopen($f_name, 'w');
	fwrite($fp_data, $contenu);
	fclose($fp_data);
    return ($outputfile.$extension);
}


// ------------------------
function classement_json2csv($json){
// transforme le fichier en lignes avec separateur ;
	$s='';
	if (!empty($json))
	{
		$t_json = json_decode($json, true);
        //echo "<pre>".json_last_error_msg()."</pre>\n";
		if (!empty($t_json))
		{
            //echo "\n<pre><br />\n";
			//print_r($t_json);
    		//echo "\n</pre>\n";
  			$s=$t_json['classement']['date'].";\n";
			$s.="rangrkn;boatname;last_update;latlon;rangvr;dtu\n";
    	    foreach ($t_json['classement']['positions'] as $position)
			{
				$s.=$position['rangrkn'].';'.$position['boatname'].';'.$position['last_update'].';'.$position['latlon'].';'.$position['rangvr'].';'.$position['dtu']."\n";
			}
		}
	}
	return $s;
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



?>