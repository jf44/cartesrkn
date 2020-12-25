<?php
// JF

// Importation des positions des bateaux depuis un fichier
// Les données de latitude et longitude sont rendues en notation décimale comme dans Google Maps et Google Earth.
/*
// Récupéré sur l'Internet
On entend souvent ce type de récrimination : "En saisissant les coordonnées GPS d'un point sur une carte Google Maps,
je me retrouve au beau milieu de l'océan atlantique !"
Les périphériques GPS transmettent par défaut les coordonnées en sexagésimal (
système de numération utilisant la base 60) alors que les cartes Google Maps utilisent le système décimal.
L'unité du sexagésimal est le degré (360 degrés), puis la minute (60 minutes = 1 degré)
puis la seconde (60 secondes = 1 minute).
Une solution possible consiste alors à convertir les degrés sexagésimaux en degrés décimaux.
Prenons un exemple :
Soit une latitude de 46°10'28" (46 degrés, 10 minutes et 28 secondes).
Exprimée en degrés décimaux, la latitude sera égale à : 46 + (10 / 60) + (28 / 3600) soit 46.1744444.
On peut donc écrire cette formule : latitude (degrés décimaux) = degrés + (minutes / 60) + (secondes / 3600).
En sens inverse, voici le déroulement des opérations :
46.1744444 - 0.1744444 = 46 ;
0.174444 * 60 = 10.46664 ;
10.46664 - 0.46664 = 10 ;
0.46664 * 60 = 27.984.
On obtient alors ce résultat : 46° 10' 27.984".
*/


// -------------------------
function get_boats_colors(){
// Récupère le contenu du fichier des couleurs de voiles
//
//AbsoluteDreamer;ffffff,eeeeaa,9999ff,ffaaaa,0033ff
//Barquasse RKN;f5fff0,f0fffd,069c00,007800,007800
//bigouden wind RKN;fc1500,ff6324,ff2a0c,ff3500,ff3500

global $inputdircolors;
global $t_colors;

	$t_colors = array();
	//echo "\n get_positions.php :: 39 :: ".$inputdircolors."boats.csv\n";
	if (file_exists($inputdircolors."boats.csv"))
	{
		if ($lines=file($inputdircolors."boats.csv"))
		{
			foreach ($lines as $line)
			{
				list($boatname, $colors) = explode(';', $line);
				if (!empty($boatname) && !empty($colors))
				{
					// tableau associatif
                	$t_colors[$boatname]=$colors;
				}
			}
		}
	}
	//print_r($t_colors);
}


// -------------------------
function get_boat_color($skipper){
global $t_colors;
	if (!empty($t_colors) && isset($t_colors[$skipper]) && !empty($t_colors[$skipper]))
	{
		return ($t_colors[$skipper]);
	}
	else
	{
		return ("000000,ffffff,9999ff,ffff99,99ff99");
	}
}

// -------------------------
function get_classement($nomboat){
// Lire dans le fichier des positions
	if (!empty($nomboat))
	{
		; // A terminer
	}
	return 0;
}


// -------------------------
function get_trajectoire_json($f_name) {
// lecture dans le fichier json ad hoc
// Fichier des trajectoires course_boatname.txt
// {"trajectoire":"lon!lat;lon!lat...;lon!lat"}
// Retourne une chaîne de trajectoires
// lon!lat;lon!lat...;lon!lat
// 120.45!35.7402;121.513!35.6521;121.723!35.7717;122.005!35.9095;122.929!35.4879;123.407!34.2182;123.777!33.2043;124.302!32.0607;125.059!31.7046;126.559!30.3949;128.73!28.5023;129.363!27.6401;131.83!27.5401;133.809!27.5401;134.338!27.0157;136.038!25.989;137.363!25.2493;138.922!23.7126;139.214!23.2367;139.298!22.5751;141.3!19.3556;141.773!18.7157;142.985!18.4076;143.268!17.7516;144.166!16.7804;145.742!15.6018;146.808!14.453;148.112!12.8392;148.916!11.8915;149.232!11.5815;150.275!10.9631;152.009!9.52024;153.004!8.7869;153.716!8.00948;155.069!6.19258;155.349!5.89654;156.229!4.73727;156.62!4.18257;156.921!3.71216;157.6!1.69251;157.768!1.41695;158.285!0.535084;158.599!-0.228893;158.902!-0.567375;159.482!-2.7696;160.396!-3.9546;160.644!-4.76668;160.902!-5.62809;161.925!-6.87726;162.185!-7.27779;162.358!-7.7477;163.825!-9.02144;164.478!-11.064;164.767!-11.5391;165.559!-12.4896;166.208!-13.6263;166.507!-15.251;166.422!-16.4251;166.498!-16.9328;166.729!-17.1236;167.617!-17.7865;167.909!-18.6209;168.687!-18.1922;168.793!-18.6472;168.909!-19.5911;169.259!-19.5687;170.199!-19.6453;

	// fichier à charger
	// $f_name=$dir_name.$course.'_'.$nomboat.$extension_json;
	if (file_exists($f_name))
	{
		if ($contentjson = file_get_contents($f_name))
		{
            //echo "File JSON :: 290 :: $f_name<br>\n";
			//echo "CONTENT : $contentjson<br>\n";
            // Lire le fichier json
            $t_data=json_decode($contentjson, true); // associative array()
            //echo "Error: ".json_last_error_msg()."<br>\n";
			if (!empty($t_data))
			{
				//print_r($t_data);
				return ($t_data['trajectoire']);
			}
		}
	}
	return '';
}



// -------------------------
function set_trajectoire_json($skipper, $course, $lonlat) {
// ecriture dans le fichier json ad hoc de la chaîne $lonlat formatee "lon!lat"
// Fichier JSON des trajectoires course_boatname.txt
// {"trajectoire":"lon!lat;lon!lat...;lon!lat"}
// 120.45!35.7402;121.513!35.6521;121.723!35.7717;122.005!35.9095;122.929!35.4879;123.407!34.2182;123.777!33.2043;124.302!32.0607;125.059!31.7046;126.559!30.3949;128.73!28.5023;129.363!27.6401;131.83!27.5401;133.809!27.5401;134.338!27.0157;136.038!25.989;137.363!25.2493;138.922!23.7126;139.214!23.2367;139.298!22.5751;141.3!19.3556;141.773!18.7157;142.985!18.4076;143.268!17.7516;144.166!16.7804;145.742!15.6018;146.808!14.453;148.112!12.8392;148.916!11.8915;149.232!11.5815;150.275!10.9631;152.009!9.52024;153.004!8.7869;153.716!8.00948;155.069!6.19258;155.349!5.89654;156.229!4.73727;156.62!4.18257;156.921!3.71216;157.6!1.69251;157.768!1.41695;158.285!0.535084;158.599!-0.228893;158.902!-0.567375;159.482!-2.7696;160.396!-3.9546;160.644!-4.76668;160.902!-5.62809;161.925!-6.87726;162.185!-7.27779;162.358!-7.7477;163.825!-9.02144;164.478!-11.064;164.767!-11.5391;165.559!-12.4896;166.208!-13.6263;166.507!-15.251;166.422!-16.4251;166.498!-16.9328;166.729!-17.1236;167.617!-17.7865;167.909!-18.6209;168.687!-18.1922;168.793!-18.6472;168.909!-19.5911;169.259!-19.5687;170.199!-19.6453;

	global $dir_serveur;
	global $datadirjson;
	global $extension_json;
	$s='';
    $dir_name=$dir_serveur.$datadirjson;
	if (!file_exists($dir_name)){
		mkdir($dir_name);
	}
	// fichier à charger
    $f_name=$dir_name.$course.'_'.$skipper.$extension_json;
	$s=get_trajectoire_json($f_name);

	if (!empty($s))
	{
		//echo "\n:: JSON : $s\n";
		if (!preg_match("/".$lonlat."/",$s))  // eviter le doublonnage
		{
			$s.=";$lonlat";
		}
	}
	else
	{
        $s="$lonlat";
	}
	$t_data['trajectoire']=$s;

	$s=json_encode($t_data);
    file_put_contents($f_name, $s, LOCK_EX );
}


// -------------------------
function get_trajectoire($skipper, $course) {
// lecture dans le fichier json ad hoc de la liste des positions au format "lon!lat"
// Fichier JSON des trajectoires course_boatname.txt
// {"trajectoire":"lon!lat;lon!lat...;lon!lat"}
// 120.45!35.7402;121.513!35.6521;121.723!35.7717;122.005!35.9095;122.929!35.4879;123.407!34.2182;123.777!33.2043;124.302!32.0607;125.059!31.7046;126.559!30.3949;128.73!28.5023;129.363!27.6401;131.83!27.5401;133.809!27.5401;134.338!27.0157;136.038!25.989;137.363!25.2493;138.922!23.7126;139.214!23.2367;139.298!22.5751;141.3!19.3556;141.773!18.7157;142.985!18.4076;143.268!17.7516;144.166!16.7804;145.742!15.6018;146.808!14.453;148.112!12.8392;148.916!11.8915;149.232!11.5815;150.275!10.9631;152.009!9.52024;153.004!8.7869;153.716!8.00948;155.069!6.19258;155.349!5.89654;156.229!4.73727;156.62!4.18257;156.921!3.71216;157.6!1.69251;157.768!1.41695;158.285!0.535084;158.599!-0.228893;158.902!-0.567375;159.482!-2.7696;160.396!-3.9546;160.644!-4.76668;160.902!-5.62809;161.925!-6.87726;162.185!-7.27779;162.358!-7.7477;163.825!-9.02144;164.478!-11.064;164.767!-11.5391;165.559!-12.4896;166.208!-13.6263;166.507!-15.251;166.422!-16.4251;166.498!-16.9328;166.729!-17.1236;167.617!-17.7865;167.909!-18.6209;168.687!-18.1922;168.793!-18.6472;168.909!-19.5911;169.259!-19.5687;170.199!-19.6453;

	global $dir_serveur;
	global $datadirjson;
	global $extension_json;
	$s='';
    $dir_name=$dir_serveur.$datadirjson;
	// fichier à charger
    $f_name=$dir_name.$course.'_'.$skipper.$extension_json;
	return get_trajectoire_json($f_name);
}

// ----------------------------
function get_lonlat($position){
//	var $position // latitude longitude en ° 44°36'41.94"N 8°42'36.81"W
// Retourne un couple lon!lat pur la trajectoire
	$longitude=0;
    $latitude=0;
	if ($tgeocode=position2lonlat($position))
	{
    	// echo "<br />--&gt; <br />\n";
		// print_r($tgeocode);
		foreach($tgeocode  as $ageocode)
		{
        	// echo "<br />==&gt; \n";
			// print_r($ageocode);
            $valeur=geocode2dec($ageocode, false);
			if (($ageocode->type=='O') || ($ageocode->type=='E') || ($ageocode->type=='W'))
			{
            	$longitude=$valeur;
			}
			else
			{
				$latitude=$valeur;
			}
		}
	}

	//echo "<br />Longitude:$longitude\n";
    //echo "<br />Latitude:$latitude\n";
	return ($longitude."!".$latitude);
}



// -----------------------------------
function get_positions($f_name){
// retourne le contenu du fichier de position passé en paramètre
/*
	global $dir_serveur;
	global $datadircsv;
	global $extension_csv;
    $dir_name=$dir_serveur.$datadircsv;
	// fichier à charger
    $f_name=$dir_name.$course.'_'.$team.'_'.$skipper-aaaammjj_hhii.$extension_csv;
*/
global $team;
global $boatname;
global $course;

$t_voiliers = array();
	// DEBUG
    // echo "\n<br /> Outputdir: ".$outputdir.", OutputFILE: ".$outputfile." Extension: ".$extension."\n";
	// Commencer par enregister le fichier
 	if (file_exists($f_name))
	{
		if ($lines=file($f_name))
		{

			foreach ($lines as $line)
			{
				if (!empty($line))
				{
// RT;Skipper;Last Update;Rank;DTF;DTU;BRG;Sail;State;Position;HDG;TWA;TWS;Speed;Factor;Foils;Options
// #;AbsoluteDreamer;2020-12-17 21:40:00 UTC;-;(9615.2);3540.4;135.5°;C0 (Auto);racing;52°41'43.60"S 152°36'41.27"W;87.976;134.994;22.0;20.10;1.0400;100%;?
					$data=explode(';',$line);    // separateur ';'
					if (false)
					{
						echo "<pre>$line</pre>\n";
						echo "<br />\n";
						print_r($data);
					}
					$n=count($data);
// RT;Skipper;Last Update;Rank;DTF;DTU;BRG;Sail;State;Position;HDG;TWA;TWS;Speed;Factor;Foils;Options
// $rt;$boatname;$last_update;$rank;$dtf;$dtu;$brg;$sail;$state;$position;$hdg;$twa;$tws;$speed;$factor;$foils;$options
//
//  0  1         2            3     4    5    6    7     8      9         10   11   12   13     14      15     16
					if ($n>=17)
					{
						if (list($rt, $skipper, $last_update, $rank, $dtf, $dtu, $brg, $sail, $state, $position, $hdg,  $twa, $tws, $speed, $factor, $foils, $options ) = $data)
						{
							if (!preg_match('/Last Update/', $last_update)) // Ligne d'Entête à ne pas traiter
							{
	                    		$CouleurVoilier= SetCouleurBd2Voilier(get_boat_color($skipper)); // "coque,pont,grand voile,voile avant : genois,spi" ff0000,ffff33,ffffff,ee33ef,0000ff
								list($longitude, $latitude) = explode('!',get_lonlat($position));
        	                    $classement = get_classement($skipper); // A terminer

								$un_voilier = new Voilier();

                    			$un_voilier->SetPosition(
    	                      		////$boat['mmsi'],
									$skipper,
        	                    	$team,
            	                	0,
									$last_update,
									// La position est en valeurs décimales
									//geocode2dec(position_geographique($row['latitude']), false),
									//geocode2dec(position_geographique($row['longitude']), false),
                            		(float)$latitude,
									(float)$longitude,
									(float)$hdg,
									(float)$speed,
            	                	$state,
									$sail,	//
									(float)$tws,
									(float)$twa,
									$CouleurVoilier->couleur_coque,   // RRR;VVV;BBB
									$CouleurVoilier->couleur_pont,
									$CouleurVoilier->couleur_gv,
									$CouleurVoilier->couleur_vav,
									$CouleurVoilier->couleur_spi,
                		            (int)$rank,
									(int)$classement  // classement du groupe courant
								);

								//$un_voilier->SetRang($classement);
								// 	<trajectoire>120.45!35.7402;121.513!35.6521;121.723!35.7717;</trajectoire>
								$un_voilier->SetTrajectoire(get_trajectoire($skipper, $course));
            	                $t_voiliers[] = $un_voilier;
	            	       	}
                    	}
					}
				}
			}
		}
 	}
    return ($t_voiliers);
}


/****
// Fonction de include/function.php
 //------------------------------------
function set_voile($sail){
// a partir de l'info sail récupéree dans VR Dashboard
// retourne un numéro de voile pour la base de données
	$voile = 0;

    $sail=str_replace(" (Auto)","",$sail);
	switch ($sail) {
        case "Stay" :  // Tourmentin ?
        case "Jib" :
        	$voile = 1;      // Foc
			break;
        case "Spi" :
          	$voile = 2;
			break;
        case "LJ" :         // Light Jib === genois
        	$voile = 4;     // foc2      === genois
			break;
      	case "Spi Lourd" :
		case "HG":           // Heavy Gennaker
        	$voile = 8;    // Code zéro === spi
			break;
        case "C0" :
        	$voile = 16;    // Code zéro === gennaker
			break;
        case "LG" :       // Light Gennaker
        	$voile = 64;  // Gennaker
			break;
		default :
			$voile = 1;
	}

	return $voile;

}

 ****/



/*
// fonction de la classe  Voilier()
 	function GetVoile(){
		if (empty($this->voile))
		{
            return 'genois';
		}
		else{
			switch ($this->voile)
			{
                case 1 : return 'foc'; break;       // foc
				case 2 : return 'spi'; break;       // spi
				case 4 : return 'genois'; break;    // genois
				case 8 : return 'spi'; break;    	// spi lourd
				case 16 : return 'genois'; break;   // Code zero
				case 32 : return 'spi'; break;      // spi leger
				case 64 : return 'spi'; break;  	// gennaker
				default : return 'genois'; break;   //
			}
		}
	}

*/

function SetVoileBd2Voilier($voile_avant){
// Blindage pour les voiles d'avant
	if (!empty($voile_avant) && ($voile_avant>=1)  && ($voile_avant<=64))
	{
		return $voile_avant;
	}
	else
	{
    	return 1; // Foc
	}
}



// Conversion du shéma de couleur de la BD
// ffffaa,33ff33,ff3333,3333ff,0000ff
// en schema de la classe Voilier
// stdClass Object
//(
//    [couleur_coque] => 255;255;170
//    [couleur_vav] => 255;51;51
//    [couleur_gv] => 51;255;51
//    [couleur_spi] => 51;51;255
//    [couleur_pont] => 0;0;255
//)


//-----------------------------------
function SetCouleurBd2Voilier($bcouleur){
	// $boat['couleur']  	= "coque,voile,foc,spi,pont" coque:000000,voile:ffff33,foc:ffffff,spi:ee33ef,pont:ffffff
	// $voilier->une_couleur_par_element

	$tcol_v = new stdClass();
	$tcol_v->couleur_coque=255; // coque    RRR;VVV;BBB
	$tcol_v->couleur_pont=255; // Pont = ancien Spi 2 On utilise la 5ème coulerurcouleur
	$tcol_v->couleur_gv=255; // GV
	$tcol_v->couleur_vav=255; // VoileAvant
	$tcol_v->couleur_spi=255; // Spi


    if ($bcouleur){
		list($ccoque, $cpont, $cvoile, $cfoc, $cspi ) = explode(',', $bcouleur);
		$tcol_v->couleur_coque = hexa2_3dec($ccoque);
        $tcol_v->couleur_pont = hexa2_3dec($cpont);
        $tcol_v->couleur_gv = hexa2_3dec($cvoile);
        $tcol_v->couleur_vav = hexa2_3dec($cfoc);
        $tcol_v->couleur_spi = hexa2_3dec($cspi);
	}
	return ($tcol_v);
}


?>