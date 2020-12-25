<?php

// JF + Xolub + Kobis

// Génération KML des  VOILIERS en 3D

// --------------------
function amure($nom_voile, $cog, $twa)
{
	$awa = $twa-$cog;
	if ($awa<180) // Bâbord
	{
		if ($nom_voile=='spi')
		{
			return '_SPI_BABORD';
		}
		else if ($nom_voile=='genois')
		{
			return '_GENOIS_BABORD';
		}
		else if ($nom_voile=='gennaker')
		{
			return '_GENNAKER_BABORD';
		}
		else
		{
			return '_FOC_BABORD';
		}
	}
	else {
 		if ($nom_voile=='spi')
		{
			return '_SPI_TRIBORD';
		}
		else if ($nom_voile=='genois')
		{
			return '_GENOIS_TRIBORD';
		}
		else if ($nom_voile=='gennaker')
		{
			return '_GENNAKER_TRIBORD';
		}
		else
		{
			return '_FOC_TRIBORD';
		}
	}
}

// --------------------
function GetFichierModele($nom_voile, $boat_type, $cog, $twa){
// retourne un modele de fichier dae correspondant a la voile portee
	return ($boat_type. amure( $nom_voile, $cog, $twa).'.dae');
}

// --------------------
function GenereCoqueBateau_3D($dossier_3d, $url_serveur, $couleurcoque, $couleurpont){
global $dir_serveur;
global $dossier_textures;
$s='';
	$chemin=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	if ($url_serveur!=''){ // liens absolus
		$url=$url_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	}
	else{ // liens relatifs
		$url='../'.$dossier_textures;
	}
	
	if ($couleurcoque){
		list($rouge, $vert, $bleu) = explode(';', $couleurcoque);
		$texture=genere_texture($chemin, $rouge, $vert, $bleu);
		if ($texture!=""){
			$s.='	<Alias>
		<sourceHref>c.PNG</sourceHref>
    	<targetHref>'.$url.'/'.$texture.'</targetHref>
	</Alias>
';
		}
	}
	if ($couleurpont){
		list($rouge, $vert, $bleu) = explode(';', $couleurpont);
		$texture=genere_texture($chemin, $rouge, $vert, $bleu);
		if ($texture!=""){
			$s.='	<Alias>
		<sourceHref>der.PNG</sourceHref>
    	<targetHref>'.$url.'/'.$texture.'</targetHref>
	</Alias>
';
		}
	}
	return $s;
}

// --------------------
function GenereGrandVoileBateau_3D($dossier_3d, $url_serveur, $couleur){
global $dir_serveur;
global $dossier_textures;
	$chemin=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	if ($url_serveur!=''){ // liens absolus
		$url=$url_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	}
	else{ // liens relatifs
		$url='../'.$dossier_textures;
	}
	
	if ($couleur){
		list($rouge, $vert, $bleu) = explode(';', $couleur);
		// DEBUG
		// echo '<br>Couleur ('.$rouge.', '.$vert.', '.$bleu.')'."\n";
		$texture=genere_texture($chemin, $rouge, $vert, $bleu);

		if ($texture!=""){
			$s='	<Alias>
		<sourceHref>gv.PNG</sourceHref>
    	<targetHref>'.$url.'/'.$texture.'</targetHref>
	</Alias>
';
			return $s;
		}

	}
	return '';
}

// --------------------
function GenereVoileAvantBateau_3D($dossier_3d, $url_serveur, $voile, $couleur){
// amure fournie par une fonction de cog et twa
global $dir_serveur;
global $dossier_textures;
	$chemin=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	if ($url_serveur!=''){ // liens absolus
		$url=$url_serveur.'/'.$dossier_3d.'/'.$dossier_textures;
	}
	else{ // liens relatifs
		$url='../'.$dossier_textures;
	}
	
	if ($couleur){
		list($rouge, $vert, $bleu) = explode(';', $couleur);
		// DEBUG
		// echo '<br>Couleur ('.$rouge.', '.$vert.', '.$bleu.')'."\n";
		$texture=genere_texture($chemin, $rouge, $vert, $bleu);

		if ($texture!=""){
			$s='	<Alias>
		<sourceHref>vav.PNG</sourceHref>
    	<targetHref>'.$url.'/'.$texture.'</targetHref>
	</Alias>
';
			return $s;
		}
	}
	return '';
}

// --------------------
function GenereRessourceMapBateau_3D($dossier_3d, $url_serveur, $bato){
// 
	$s='<ResourceMap>
';
	$nomvoile= $bato->GetVoile();
	$s.=GenereCoqueBateau_3D($dossier_3d, $url_serveur, $bato->couleur_coque, $bato->couleur_pont);
	$s.=GenereGrandVoileBateau_3D($dossier_3d, $url_serveur, $bato->couleur_gv);
	if ($nomvoile=='spi')
	{
		$s.=GenereVoileAvantBateau_3D($dossier_3d, $url_serveur, $bato->voile, $bato->couleur_spi);
	}
	else if ($nomvoile=='foc')
	{
    	$s.=GenereVoileAvantBateau_3D($dossier_3d, $url_serveur, $bato->voile, $bato->couleur_foc);
	}
	else if ($nomvoile=='gennaker')
	{
		$s.=GenereVoileAvantBateau_3D($dossier_3d, $url_serveur, $bato->voile, $bato->couleur_vav);
	}
	else
	{
		$s.=GenereVoileAvantBateau_3D($dossier_3d, $url_serveur, $bato->voile, $bato->couleur_vav);
	}
	$s.='</ResourceMap>
';
	return $s;
}

// --------------------
function GenereBateauKML_3D($dossier_3d, $url_serveur, $boat_type, $bato, $echelle=6, $altitude=1000){
// Modele 3D
// Pas de generation de parcours dans cette fonction
// le parcours est transformé en Tour appelé dans GenereEnQueue()
global $dossier_modeles;
global $afficher_trajectoire;
global $t_parcours;


	// $echelle_z=$echelle*1.5;
	$echelle_z=$echelle;
	$s='';
	if ($url_serveur!='')
	{ // liens absolus
		$url_modeles=$url_serveur.'/'.$dossier_3d.'/'.$dossier_modeles;
	}
	else
	{ // liens relatifs
		$url_modeles=$dossier_modeles;
	}

	if (!empty($bato))
	{

/****************
		if (!empty($bato->symbole) )
		{
			// On place juste une balise
			// Pas de parcours
			$s.='<Placemark>
<open>1</open>
<description>
<![CDATA[<p>
';
			if (!empty($bato->id))
			{
			    $s.=' Id: '.$bato->idboat;
			}
			$s.='<br>'.$bato->date_enregistrement.'
<br>Lon: '.$bato->longitude.'
<br>Lat: '.$bato->latitude.'
<br>COG: '.$bato->cog;
			if (!empty($bato->sog))
			{
			    $s.='<br>SOG: '.$bato->sog;
			}
			if (!empty($bato->twa))
			{
			    $s.='<br>TWA: '.$bato->twa;
			}
			if (!empty($bato->tws))
			{
			    $s.='<br>TWS: '.$bato->tws;
			}
			if (!empty($bato->classement))
			{
				$s.='<br>Rang Team : '.$bato->classement;
			}
			if (!empty($bato->dtu))
			{
				$s.='<br>DTU : '.$bato->dtu;
			}
			$s.='</p>]]>
</description>
	<LookAt>
    	<longitude>'.$bato->longitude.'</longitude>
		<latitude>'.$bato->latitude.'</latitude>
        <altitude>'.$altitude.'</altitude>
		<range>100000</range>
        <heading>'.$bato->cog.'</heading>
        <tilt>85</tilt>
		<altitudeMode>relativeToGround</altitudeMode>
    </LookAt>
<styleUrl>#Balise_sailboat</styleUrl>
	<Point>
		<gx:drawOrder>1</gx:drawOrder>
		<coordinates>'.$bato->longitude.','.$bato->latitude.',0</coordinates>
	</Point>
</Placemark>
';

		}
		else
		{
**********************/
			$gite=$bato->GiteVoilier();
            $t_parcours[]=new Coordonnees($bato->longitude, $bato->latitude);

			if ($bato->nomboat!='')
			{

				$s.='<Placemark>
<name>'.$bato->nomboat.'</name>
<description>
<![CDATA[<p>
';
/*
if (!empty($bato->mmsi)){
    $s.=' MMSI: '.$bato->mmsi;
}
*/
				if (!empty($bato->idboat))
				{
				    $s.=' Id: '.$bato->idboat;
				}
				$s.='<br>'.$bato->date_enregistrement.'
<br>Lon: '.$bato->longitude.'
<br>Lat: '.$bato->latitude.'
<br>COG: '.$bato->cog;
				if (!empty($bato->sog))
				{
				    $s.='<br>SOG: '.$bato->sog;
				}
				if (!empty($bato->twa))
				{
				    $s.='<br>TWA: '.$bato->twa;
				}
				if (!empty($bato->tws))
				{
    				$s.='<br>TWS: '.$bato->tws;
				}
				if (!empty($bato->classement))
				{
					$s.='<br>Rang Team : '.$bato->classement;
				}
/*
if (!empty($bato->dtg)){
	$s.='<br>DTG : '.$bato->dtg;
}

if (!empty($bato->dbl)){
	$s.='<br>DBL : '.$bato->dbl;
}
*/
				if (!empty($bato->voile))
				{
					$s.='<br>Voile : '.$bato->GetVoile();
				}
				$s.='</p>]]>
</description>
	<LookAt>
    	<longitude>'.$bato->longitude.'</longitude>
		<latitude>'.$bato->latitude.'</latitude>
        <altitude>'.$altitude.'</altitude>
		<range>100000</range>
        <heading>'.$bato->cog.'</heading>
        <tilt>85</tilt>
		<altitudeMode>relativeToGround</altitudeMode>
    </LookAt>
';
				$s.='
	<styleUrl>#Balise_sailboat</styleUrl>
	<MultiGeometry>
    	<Point>
            <extrude>1</extrude>
            <altitudeMode>relativeToGround</altitudeMode>
            <coordinates>'.$bato->longitude.','.$bato->latitude.',0</coordinates>
		</Point>
';
 				$s.='		<Model>
			<altitudeMode>relativeToGround</altitudeMode>
	    	<Location>
    			<longitude>'.$bato->longitude.'</longitude>
    			<latitude>'.$bato->latitude.'</latitude>
    			<altitude>'.$altitude.'</altitude>
	    	</Location>
    		<Orientation>
    			<heading>'.$bato->cog.'</heading>
				<tilt>'.$gite->x.'</tilt>
                <roll>'.$gite->y.'</roll>
    		</Orientation>
	    	<Scale>
    			<x>'.$echelle.'</x>
        		<y>'.$echelle.'</y>
        		<z>'.$echelle_z.'</z>
	    	</Scale>
';
				// A VERIFIER CAR ICI J'IMPROVISE
				$fichier_dae=GetFichierModele($bato->GetVoile(), $boat_type, $bato->cog, $bato->twa);
				if ($fichier_dae!='')
				{
					// recopier le fichier modele
					if (recopier_modele_dae($dossier_3d, $fichier_dae, $bato->nomboat, $bato->GetVoile(), $boat_type))
					{
						$s.='
			<Link id="'.$bato->nomboat.'">
    			<href>'.$url_modeles.'/'.$boat_type.'_'.$bato->nomboat.'_'.$bato->GetVoile().'.dae</href>
    		</Link>
';
					}
				}
				$s.= GenereRessourceMapBateau_3D($dossier_3d, $url_serveur, $bato);
				$s.='
		</Model>
	</MultiGeometry>
</Placemark>
';

			// Trajectoire  reportée en dehors pour la concentre dans un folder...
			//if (isset($afficher_trajectoire) && ($afficher_trajectoire==1)){
			//	$s.=GenereTrajectoireBateauKML($bato, $altitude);
			//}
/**************
 		}
**************/
		}
	}
	return $s;
}

// --------------------
function GenereMarquesParcoursEtDebutPositionsBateauxKML_3D($okmarques=true){
global $url_serveur;
global $dossier_marques;
global $fichier_marques;
global $course;
global $team;

	$s='';
	if ($okmarques)
	{
//Peut etre remplace par
		$s='
		<NetworkLink id="MarquesStyles">
			<name>Marques de parcours</name>
			<refreshVisibility>1</refreshVisibility>
			<flyToView>0</flyToView>
			<Link id="vgv2020">
				<href>';
		$s.=$dossier_marques."/".$fichier_marques; // $url_fichier_marque='marques/MarquesParcoursVGV2020.kml';
		$s.='</href>
				<refreshMode>onChange</refreshMode>
			</Link>
		</NetworkLink>
';

	}
	$s.= GenereStylesBalisesBateauxKML();
	$s.='		<Folder>
			<name>'.strtoupper($team).'_Position</name>
';
	return $s;
}

// -------------------------
function GenereEnQueueKML_3D(){
	$s='</Folder>
	</Folder>
</Document>
</kml>
';
	return $s;
}

//------------------------
function genere_texture($chemin, $rouge, $vert, $bleu, $size=144){
// cree une image RVB
	if (file_exists($chemin.'/c_'.$rouge.'_'.$vert.'_'.$bleu.'.PNG')){
		return 'c_'.$rouge.'_'.$vert.'_'.$bleu.'.PNG';
	}
	else{
		// Générer les textures à la volee
		$image=imagecreatetruecolor($size, $size);
		$back = imagecolorallocate($image, $rouge, $vert, $bleu);
		imagefilledrectangle($image, 0, 0, $size - 1, $size - 1, $back);
		// Enregistrer l'image
		$fichier=$chemin.'/c_'.$rouge.'_'.$vert.'_'.$bleu.'.PNG';
		$ok=imagepng($image, $fichier);
		imagedestroy($image);
		if ($ok){
			return 'c_'.$rouge.'_'.$vert.'_'.$bleu.'.PNG';
		}
		else{
			return '';
		}
	}
}

//------------------------
function recopier_marques_parcours($dossier_cible){
// copie du fichier de marques dans le dossier des marques
global $dir_serveur;
global $fichier_marques;
global $dossier_marques;

	// DEBUG
	if (!empty($dossier_marques) && !empty($fichier_marques))
	{

		if (file_exists($dir_serveur.'/sources_3d/'.$dossier_marques.'/'.$fichier_marques)){
			$contenu=file_get_contents($dir_serveur.'/sources_3d/'.$dossier_marques.'/'.$fichier_marques);
			$f_name=$dir_serveur.'/'.$dossier_cible.'/'.$dossier_marques.'/'.$fichier_marques;
			if (file_exists($f_name))
			{
				return true;
			}
			else
			{
				// DEBUG
				//echo "\nkml_3d.php:: 483 :: DIR_SERVEUR:".$dir_serveur.'/'.$dossier_cible.'/'.$dossier_marques.'/'.$fichier_marques." recopié<br />\n";

				$fp = fopen($f_name, 'w');
				if ($fp){
					fwrite($fp, $contenu);
					fclose($fp);
	        	   return $fp;
				}
			}
		}
	}

	return false;
}


//------------------------
function recopier_modele_dae($dossier_3d, $fichier_dae, $nom_bato, $voile, $boat_type){
// copie du modele sous le nom du bateau
global $dir_serveur;
global $extension_dae;
global $dossier_modeles;
	// DEBUG
	// echo "\nkml_3d.php:: 417 :: DIR_SERVEUR:".$dir_serveur.'/sources_3d/'.$dossier_modeles.'/'.$fichier_dae."<br />\n";

	if (file_exists($dir_serveur.'/sources_3d/'.$dossier_modeles.'/'.$fichier_dae)){
		$contenu=file_get_contents($dir_serveur.'/sources_3d/'.$dossier_modeles.'/'.$fichier_dae);
		$f_name=$dir_serveur.'/'.$dossier_3d.'/'.$dossier_modeles.'/'.$boat_type.'_'.$nom_bato.'_'.$voile.$extension_dae;
        // echo "\n422 :: -->".$dir_serveur.'/'.$dossier_3d.'/'.$dossier_modeles.'/'.$boat_type.'-'.$nom_bato.'_'.$voile.$extension_dae."<br />\n";
		if (file_exists($f_name))
		{
			return true;
		}
		else
		{
			$fp = fopen($f_name, 'w');
			if ($fp){
				fwrite($fp, $contenu);
				fclose($fp);
	           return $fp;
			}
		}
	}

	return false;
}



// -----------------------
function EnregistreKML_3D($dossier_cible, $contenu){
// Deux fichiers sont crees : un fichier d'archive et un fichier courant (dit de cache) au contenu identique.
// c'est ce fichier de cache (dont le nom est toujours identique) qui est appelé par le fichier rkn.kml lu par GoogleEarth
// Le dossier d'achive est zippé

global $dir_serveur;
global $fichier_kml_courant;
global $fichier_kml_cache;
global $extension_kml;
global $extension_kmz;
global $dossier_3d;
global $dossier_modeles;
global $dossier_textures;
global $dossier_marques;
global $dossier_kmz;
global $course;
global $team;
global $boatname;

	$fichier_kml_cache_3d=$fichier_kml_cache.'_3D';
	// Commencer par enregister le fichier KML
	$f_cache_name=$dir_serveur.'/'.$dossier_cible.'/'.$fichier_kml_cache_3d.$extension_kml;
	$fp_data = fopen($f_cache_name, 'w');
	if ($fp_data ){
		fwrite($fp_data, $contenu);
		fclose($fp_data);
	}
	// faire une copie zippee du  dossier $dossier_cible
	$t_fichiers=array();
	$t_fichiers[0]=$dossier_cible.'/'.$fichier_kml_cache_3d.$extension_kml;
	$t_fichiers[1]=$dossier_cible.'/'.$dossier_modeles;
	$t_fichiers[2]=$dossier_cible.'/'.$dossier_textures;
	$t_fichiers[3]=$dossier_cible.'/'.$dossier_marques;
    if (creer_fichier_zip($dossier_cible, $t_fichiers, $fichier_kml_cache_3d))
	{
		// puis le renommer .kmz
		$nom_fichier_kmz=renommer_fichier_zip($fichier_kml_cache_3d, $extension_kmz);
		if ($nom_fichier_kmz!='')
		{
				// creer un fichier d'archive
				// le nom du fichier d'archive recoit une date+heure qui sera utilisee 
				// pour verifier si le delai depuis la génération précédente est suffisant
				$f_name_cache=$dir_serveur.'/'.$nom_fichier_kmz;
				$date_stamp=gmdate('YmdH');
				$nfarchive=$dossier_kmz.'/'.nom_fichier($nom_fichier_kmz).$date_stamp.$extension_kmz;
				$f_archive=$dir_serveur.'/'.$nfarchive;
  				// On supprime les fichiers intermédiaires
				rename($f_name_cache, $f_archive);
				delTree($dir_serveur.'/'.$dossier_cible.'_'.$date_stamp);
				// Et on renvoie le nom du fichier
     			return ($nfarchive);
    	}
	}
	return "";
}

// -----------------------
function ExisteKML_3D(){
// verifie si une generation a ete faite durant l'heure courante
global $dir_serveur;
global $dossier_3d;
global $fichier_kml_courant;
global $fichier_kml_cache;
global $extension_kml;
global $extension_kmz;

$fichier_kml_cache_3d=$fichier_kml_cache.'_3D';

	$f_data_name=$dir_serveur.'/'.$fichier_kml_cache_3d.gmdate('YmdH').$extension_kmz;
	// DEBUG
	// echo "<br>Fichier courant: $f_data_name\n";
	
	if (file_exists($f_data_name)){
		return $f_data_name;
	}
	else{
		return '';
	}
}

// --------------------
function GenereStylesBalisesBateauxKML(){
global $url_serveur;
	$s='
		<Style id="Balise_sailboat">
			<IconStyle>
				<scale>0.4</scale>
				<Icon>
					<href>http://maps.google.com/mapfiles/kml/shapes/sailing.PNG</href>
				</Icon>
			</IconStyle>
		</Style>
';

	return $s;
}


// --------------------
function GenereEnteteKML_3D($longitude, $latitude, $cog){
global $team; // groupe courant determine aussi le prefixe du fichier KML

$cog_oppose=(($cog+180) % 360);

// <href>http://maps.google.com/mapfiles/kml/shapes/arrow.PNG</href>
	$s='<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
<Document>
	<Style id="pmIcon">
    	<IconStyle id="">
        	<color>CC00FFFF</color>
        	<scale>0.5,0.5</scale>
        	<Icon>
';
	$s.='        	<href>http://maps.google.com/mapfiles/kml/pal4/icon21.PNG</href>'."\n";

	$s.='        	</Icon>
		    <hotSpot x="0.5"  y="0.5" xunits="fraction" yunits="fraction"/>    <!-- kml:vec2 -->
      	</IconStyle>
		<LineStyle>
			<wmmsith>1</wmmsith>
		</LineStyle>
    </Style>
	<Folder>
		<name>'.strtoupper($team).'_3D</name>
		<open>1</open>
		<LookAt>
			<longitude>'.$longitude.'</longitude>
			<latitude>'.$latitude.'</latitude>
    	    <altitude>5000000</altitude>
			<range>100000</range>
	        <heading>0</heading>
    	    <tilt>'.$cog_oppose.'</tilt>

			<range>100000</range>
	        <heading>270</heading>
    	    <tilt>20</tilt>
		</LookAt>
';
return $s;
}


// --------------------
function GenereKML_3D($dossier_3d, $url_serveur){	
// génere le fichier courant à charger dans Google Earth

global $dir_serveur;
global $fichier_kml_courant;
global $fichier_kml_cache;
global $extension_kml;
global $extension_kmz;
$fichier_kml_cache_3d=$fichier_kml_cache.'3D';

	$s='<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Folder>
    <NetworkLink>
      <refreshVisibility>0</refreshVisibility>
      <flyToView>1</flyToView>
      <Link>
        <href>'.$url_serveur.'/'.$dossier_3d.'/'.$fichier_kml_cache_3d.$extension_kml.'</href>
        <refreshInterval>1800</refreshInterval>
        <viewRefreshMode>onRequest</viewRefreshMode>
      </Link>
    </NetworkLink>
  </Folder>
</kml>
';
	// enregistrer ce ficher
	$fp_data = fopen($fichier_kml_courant.$extension_kml, 'w');
	if ($fp_data ){
		fwrite($fp_data, $s);
		fclose($fp_data);
	}
}

?>