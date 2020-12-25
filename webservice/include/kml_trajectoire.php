<?php

// include_once("include/GeoCalc.class.php");




// --------------------
function couleur_hexa($couleur, $transparence='ff'){
// conversion d'une couleur RVB en hexadecimal + transparence en tête
// The order of expression is aabbggrr, where aa=alpha (00 to ff); bb=blue (00 to ff); gg=green (00 to ff); rr=red (00 to ff).
	$hex=$transparence;
	if ($couleur){
		$rvb = explode(';', $couleur);
		//conversion en hexadecimal 
   		for ($i=2; $i>-1; $i--){
			$temp = dechex($rvb[$i]); // rouge , vert , bleu
   			//test si la chaine fait 1 pour ajouter un 0 devant
	   		if (strlen($temp) < 2){
    	 		// ajout du zéro
     			$hex .= "0". $temp; 
   			}
			else{
     			//ajout du chiffre à la chaine
     			$hex .= $temp; 
			} 
		}
	}
	return $hex; 
}


// --------------------
function GenereTrajectoireBateauKML($bato, $altitude){

	$s='';

	if (!empty($bato) && isset($bato->trajectoire) && !empty($bato->trajectoire))
	{
  		$s='
<Placemark>
<name>'.$bato->nomboat.'_route</name>
<Style>
<LineStyle>
<color>'.couleur_hexa($bato->couleur_coque).'</color>
<width>1</width>
</LineStyle>
</Style>
';
		$s.='<LineString>
<extrude>1</extrude>
<tessellate>1</tessellate>
<coordinates>
';
			$s.=$bato->ListeTrajectoire($altitude);
			$s.='
</coordinates>
</LineString>
</Placemark>
';

	}
	return $s;
}


// --------------------
function compare_distance($a, $b, $ref){
// comparaisons de deux coordonnées par rapport à un point geographique de reference
// par la methode du grand cercle
	// retourne 1 si $a>b, 0 si a==$b, et -1 sinon

	$a_lon=(float)$a->GetLon();
	$a_lat=(float)$a->GetLat();
	$a_alt=(float)$a->GetAlt();
	$b_lon=(float)$b->GetLon();
	$b_lat=(float)$b->GetLat();
	$b_alt=(float)$b->GetAlt();

	// point de réference
	$ref_lon=(float)$ref->GetLon();
	$ref_lat=(float)$ref->GetLat();
	$ref_alt=(float)$ref->GetAlt();

	$oGC = new GeoCalc();

	$a_reference= $oGC->GCDistance($ref_lat, $ref_lon, $a_lat, $a_lon);
	$b_reference= $oGC->GCDistance($ref_lat, $ref_lon, $b_lat, $b_lon);

	// echo '<br>DEBUG :: kml_trajectoire.php :: Ligne 117 :: COMPARAISON : ('.$a_lon.','.$a_lat.','.$a_alt.') avec ('.$b_lon.','.$b_lat.','.$b_alt.') -- &gt; '."\n";
	if ($a_reference>$b_reference) $ordre= 1;
	else if ($a_reference==$b_reference) $ordre= 0;
	else $ordre= -1;
	echo $ordre;
	return $ordre;
}


// --------------------
function calcule_distance($lon, $lat, $lon0, $lat0){
// Distance la methode du grand cercle

	$oGC = new GeoCalc();
	return $oGC->GCDistance($lat, $lon, $lat0, $lon0);
}




// --------------------
function OrdonnerCoordonnees($t_parcours){
	// DEBUG
	// echo "<br />DEBUG :: test_parcours.php :: 98 :  <b>Parcours</b><br />\n";
	// print_r($t_parcours);
	
	$echange=true;
	
	$n=count($t_parcours);
	if ($n>0){
		while ($echange==true){
			$i=0;
			$echange=false;
			while ($i<$n-1){
				// comparaison 
				if (compare_coord($t_parcours[$i], $t_parcours[$i+1])==1){ //
					// echanger
					$aux=$t_parcours[$i];
					$t_parcours[$i]=$t_parcours[$i+1];
					$t_parcours[$i+1]=$aux;
					$echange=true;
				}
				$i++;
			}
		}
	}
	
	// echo "<br />DEBUG :: test_parcours.php :: 109 :  <b>Parcours ORDONNE</b><br />\n";
	// print_r($t_parcours);
	return ($t_parcours);
}


/*
// --------------------
function compare_distance($a, $b, $ref){
// comparaisons de deux coordonnées par rapport à un point geographique de reference
// par la methode du grand cercle
	// retourne 1 si $a>b, 0 si a==$b, et -1 sinon

	$a_lon=(float)$a->GetLon();
	$a_lat=(float)$a->GetLat();
	$a_alt=(float)$a->GetAlt(); 
	$b_lon=(float)$b->GetLon();
	$b_lat=(float)$b->GetLat();
	$b_alt=(float)$b->GetAlt();
	
	// point de réference
	$ref_lon=(float)$ref->GetLon();
	$ref_lat=(float)$ref->GetLat();
	$ref_alt=(float)$ref->GetAlt(); 

	$oGC = new GeoCalc();
	
	$a_reference= $oGC->GCDistance($ref_lat, $ref_lon, $a_lat, $a_lon);
	$b_reference= $oGC->GCDistance($ref_lat, $ref_lon, $b_lat, $b_lon);
	
	echo '<br>DEBUG :: kml_trajectoire.php :: Ligne 117 :: COMPARAISON : ('.$a_lon.','.$a_lat.','.$a_alt.') avec ('.$b_lon.','.$b_lat.','.$b_alt.') -- &gt; '."\n";
	if ($a_reference>$b_reference) $ordre= 1;
	else if ($a_reference==$b_reference) $ordre= 0;
	else $ordre= -1;
	echo $ordre;
	return $ordre;
}
*/


// --------------------
function compare_coord($a, $b){
// comparaisons de deux coordonnées
	// retourne 1 si $a>$b, 0 si $a==$b, et -1 sinon
	$a_lon=(float)$a->GetLon();
	$a_lat=(float)$a->GetLat();
	$a_alt=(float)$a->GetAlt(); 
	$b_lon=(float)$b->GetLon();
	$b_lat=(float)$b->GetLat();
	$b_alt=(float)$b->GetAlt();
	// echo '<br>COMPARAISON : ('.$a_lon.','.$a_lat.','.$a_alt.') avec ('.$b_lon.','.$b_lat.','.$b_alt.') -- &gt; '."\n";
	if ($a_lon>$b_lon) $ordre= 1;
	elseif (($a_lon==$b_lon) && ($a_lat>$b_lat)) $ordre= 1;
	elseif (($a_lon==$b_lon) && ($a_lat==$b_lat) && ($a_alt>$b_alt))  $ordre= 1;
	elseif (($a_lon==$b_lon) && ($a_lat==$b_lat) && ($a_alt==$b_alt)) $ordre= 0;
	else $ordre= -1;
	// echo $ordre;
	return $ordre;
}

// --------------------
function GenereVisiteBateauxKML($t_parcours, $echelle, $yet_sorted=true){
// $t_parcours est un tableau d'objets de type Coordonnées(voir classe Coordonnées dans le fichier Voilier()
// Le parcours est fabriqué lors du chargement des bateaux
// Parcours transparent. Juste pour faire une visite
global $coursenumber;
global $groupe;
global $t_code_groupes;
$altitude = $echelle * 20000.0;
	if (isset($t_parcours) && ($t_parcours)){
		if (!$yet_sorted){
			$t_parcours=OrdonnerCoordonnees($t_parcours);
		}
		$s='
<Placemark>
<name>'.strtoupper($t_code_groupes[$groupe]).'_route</name>
<Style>
<LineStyle>
<color>00550000</color>
<width>1</width>
</LineStyle>
</Style>
<LineString>
<extrude>1</extrude>
<tessellate>1</tessellate>
<coordinates>
';
		for($i=0; $i< count($t_parcours); $i++){
			if (isset($t_parcours[$i]) && ($t_parcours[$i])){
				$s.=$t_parcours[$i]->GetLon().','.$t_parcours[$i]->GetLat().','.$altitude.' ';
			}
		}
		$s.='
</coordinates>
</LineString>
</Placemark>
';
	}
	return $s;
}

// ------------------------
function compare_classement($a, $b)
// Attention on veut un résultat trié dans l'ordre décroissant
{
	if (!empty($a) && !empty($b))
	{
		if ($a->classement == $b->classement)
		{
			return 0;
		}
		return ($a->classement > $b->classement) ? -1 : 1;
	}
	return 0;
}

// --------------------
function GenereTourBateauxKML($les_voiliers, $echelle){
// cette fonction travaille directement sur la liste des  bateaux
// Si ceux-ci sont ordonnés le parcours a un sens , sinon ça saute d'un bateau à l'autre...
// On a un tableau ordonné de voiliers
// Juste pour faire une visite du premier au dernier
$altitude=$echelle * 5000.0;
$range=$echelle * 25000.0;
$ecart_min = 40.0 * $echelle;
$vitesse_min  = $ecart_min / 6.0;  // 4 secondes pour faire le deplacement
$s='';

$t_voiliers=array();

if (!empty($les_voiliers))
{
	foreach ( $les_voiliers as $un_voilier)
	{
		//print_r(array($un_voilier->nomboat, $un_voilier->latitude, $un_voilier->longitude, $un_voilier->cog, $un_voilier->sog, $un_voilier->classement));
		$a_boat=new stdClass();
    	$a_boat->nomboat=$un_voilier->nomboat;
    	$a_boat->latitude=$un_voilier->latitude;
    	$a_boat->longitude=$un_voilier->longitude;
    	$a_boat->cog=$un_voilier->cog;
    	$a_boat->sog=$un_voilier->sog;
 		$a_boat->classement=$un_voilier->classement;
    	$t_voiliers[]=$a_boat;
	}

	if (!empty($t_voiliers) && (count($t_voiliers)>1))
	{
		usort($t_voiliers, "compare_classement");

		$s.='
	<Folder>
  		<name>Tour</name>
        <Style id="pushpin">
    		<IconStyle id="mystyle">
      			<Icon>
        			<href>http://maps.google.com/mapfiles/kml/pushpin/ylw-pushpin.png</href>
        			<scale>1.0</scale>
      			</Icon>
    		</IconStyle>
  		</Style>

		<gx:Tour>
			<name>Play me!</name>
			<description>
				<![CDATA[Le Grand Tour de la Flotte...]]>
			</description>
			<gx:Playlist>
';
        $lon0=0.0;
		$lat0=0.0;
		// Ordonner les bateaux


		foreach ($t_voiliers as $un_voilier)
		{
			if (!empty($un_voilier))
			{

				$lon=$un_voilier->longitude;
				$lat=$un_voilier->latitude;

				$ecart = calcule_distance($lon, $lat, $lon0, $lat0);

				if ($ecart > $ecart_min)
				{
					$duree= min($ecart / $vitesse_min, 20.0);
				//	$cog = ($un_voilier->GetCog()) + 180.0 + rand(0, 60) - 30.0;
                	$cog = ($un_voilier->cog) + rand(0, 60) - 30.0;
                    $lon0=$lon;
                    $lat0=$lat;
                    $s .= '
				<gx:FlyTo>
        			<gx:flyToMode>smooth</gx:flyToMode>
        			<gx:duration>'.$duree.'</gx:duration>
        			<LookAt>
          				<longitude>'.$lon.'</longitude>
          				<latitude>'.$lat.'</latitude>
          				<altitude>'.$altitude.'</altitude>
          				<heading>'.$cog.'</heading>
                        <range>'.$range.'</range>
          				<tilt>80.0</tilt>
        			</LookAt>
      			</gx:FlyTo>
';
					$s.='
				<gx:AnimatedUpdate>
       				<gx:duration>2.0</gx:duration>
        			<Update>
          				<targetHref></targetHref>
          				<Change>
            				<IconStyle targetId="mystyle">
              					<scale>'.$echelle.'</scale>
            				</IconStyle>
          				</Change>
			        </Update>
			    </gx:AnimatedUpdate>
';
                }
			}
		}
        $s.='
			</gx:Playlist>
		</gx:Tour>
	</Folder>
';
	}
	}
	return $s;
}

?>