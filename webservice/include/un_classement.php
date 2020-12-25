<?php
// ge/include/un_classement.php

// Post_traitement après que le fichier de données obtenu avec VR Dashboard
// ait été intégré dans la BD de la course
// La DTU fournit la distance d'un bateau au nom du bateau de l'utilisateur qui
// a fait l'intégration depuis VR Dashbord avec bd_charge_data.php

// Table traitée `rkn_position`, Champs utiles : idpos, dtu --> rangrkn
// Pour tous les bateaux pour une date (GMT) donnée de format  2020-11-26 08:20:00
// Extraire toutes les positions selon dtu décroissant
// Puis pour toutes les positions mettre à jour rangrkn

// ------
function ajoute_une_heure($une_date)
{
	$matches=array();
	if (!empty($une_date))
	{
		if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d)/",$une_date, $matches) !==false)
		{
            $date=$matches[0];
			$annee=$matches[1];
         	$mois=$matches[2];
         	$jour=$matches[3];
         	$heure=$matches[4];
			/*
	   		echo "<br />90 :: MATCHES\n<pre>\n";
		    print_r($matches);
    		echo "</pre>\n";
			echo "<br />$date == $annee, $mois, $jour, $heure\n";
			*/

            $d = DateTime::createFromFormat("Y-m-d H", $annee."-".$mois."-".$jour." ".$heure);
    		//echo " Date créée : '".$d->format('Y-m-d H')."'\n";

            $d->add(new DateInterval('PT3600S'));// Une heure de plus
			//echo " --&gt;  '".$d->format('Y-m-d H')."'\n";
		   	return ($d->format('Y-m-d H'));
		}
	}
	return '';
}

// -------------
function reformate_periode($periode)
// on recherche les données sur une période d'une heure minimum
{
    $matches=array();
    $periode_out = new stdClass();
    $periode_out->nom=$periode->nom;
	if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d)/",$periode->datemin, $matches) !==false)
	{
        if (!empty($matches))
		{
			$periode_out->datemin=$matches[1]."-".$matches[2]."-".$matches[3]." ".$matches[4];
		}
		else
		{
            $periode_out->datemin=0;
		}
	}
	else
	{
        $periode_out->datemin=0;
	}

  	if (preg_match("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d)/",$periode->datemax, $matches) !==false)
	{
        if (!empty($matches))
		{
 			$periode_out->datemax=ajoute_une_heure($periode->datemax);
		}
		else
		{
            $periode_out->datemax=0;
		}
	}
	else
	{
        $periode_out->datemax=0;
	}
  	return ($periode_out );

}


// ------------------------
function compare_dtu_dec($a, $b)
// Attention on veut un résultat trié dans l'ordre décroissant
{
	if (!empty($a) && !empty($b))
	{
		if ($a->dtu == $b->dtu)
		{
			return 0;
		}
		return ($a->dtu > $b->dtu) ? -1 : 1;
	}
	return 0;
}

//-------------
function classement_position_rkn($t_positions_dtu)
{
// tableau d'objets idpos, dtu
// affecte le rangrkn en fonctionde la dtu
global $connexion;
	if (!empty($connexion) && !empty($t_positions_dtu) && (count($t_positions_dtu)>1))
	{
		usort($t_positions_dtu, "compare_dtu_dec");
		$rang=0;
		foreach ($t_positions_dtu as $pos_dtu)
		{
			if (!empty($pos_dtu))
			{
				$rang++;
				$requete = "UPDATE `rkn_position` SET `rangrkn`= ".$rang." WHERE `idpos`=".$pos_dtu->idpos." ;";
				$res=execute_requete($requete);
            	if (!$res)
				{
            		echo "ge/include/un_classement.php :: 115\nSQL : ".mysqli_error($connexion)."<br/>REQUETE INJECTION POSITION ::\nSQL &gt;\n$requete\n";
	   			}
			}
		}
	}
}


// --------------------------
function classement_json($t_positions){
// tableau d'objets
// retourne une chaîne json
// affecte le rangrkn en fonctionde la dtu
/*
										$rposition = new stdClass();
											$rposition->skipper=$skipper;
											$rposition->last_update=$last_update;
											$rposition->rangvr=$rank;
											$rposition->latlon=$position;
											$rposition->dtu=$dtu;
											$t_positions[]=$rposition;

*/
	$json='';
	if (!empty($t_positions) && (count($t_positions)>1))
	{
		/*
		echo "\nun_classement.php :: 147 :: \n<pre>\n";
		print_r($t_positions);
    	echo "\n</pre>\n";
		*/
		usort($t_positions, "compare_dtu_dec");
		/*
		echo "\nun_classement.php :: 147 :: \n<pre>\n";
		print_r($t_positions);
    	echo "\n</pre>\n";
		*/
		$rang=0;
		$i=0;
        while ($i<count($t_positions))
		{
			$rang++;
            $t_positions[$i]->rangrkn=$rang;
			$i++;
		}

		$date = date("Y-m-d H:i", time());
		$json='{"classement":{"date":'.json_encode($date);
		$json.=', "positions":';
		$json.=json_encode($t_positions);
        $json.='}}';
        // echo "<pre>".json_last_error_msg()."</pre>\n";
	}
	return $json;
}



//-------------
function classement_rkn($date, $groupe=0)
// Cette fonction n'est plus utilisée car la gestion des dates est problématique
// vu que la même flotte ne reçoit pas exactement la même heure de chargement de données avec VR
// Pour tous les bateaux pour une date (GMT) donnée de format  2020-11-26 08:20:00
// Eventuellement pour un groupe donné
{
	global $connexion;

	if (!empty($connexion) && !empty($date))
	{
    	$periode = new stdClass();
    	$periode->nom="Classement RKN";
        $periode->datemin=$date;
        $periode->datemax=$date;
        $periode=reformate_periode($periode);

		if (!empty($groupe))
		{
            $requete= "SELECT DISTINCT `idpos` FROM `rkn_position`, `rkn_group_boat`  WHERE `date`>='".$periode->datemin."' AND `date`<'".$periode->datemax."' AND `rkn_position`.`refbato`= `rkn_group_boat`.`ref_boat` AND `rkn_group_boat`.`ref_group`=".$groupe." ORDER BY `dtu` DESC;";
		}
		else
		{
			$requete= "SELECT DISTINCT `idpos` FROM `rkn_position` WHERE `date`>='".$periode->datemin."' AND `date`<'".$periode->datemax."' ORDER BY `dtu` DESC;";
		}

		// Chargement des données
        $rows = array();
		if (verifie_requete_select( $requete))
		{
			if ($res=execute_requete( $requete))
			{
				while ($row = mysqli_fetch_array($res))
				{
                    $rows[] = $row;
				}
			}
			else
			{
           		echo "ge/include/un_classement.php :: 38\nSQL : ".mysqli_error($connexion)."<br />REQUETE RECHERCHE POSITION ::\nSQL &gt;\n$requete\n";
			}
 		}
		// Mise à jour
		if (!empty($rows))
		{
			$rang=0;
			foreach ($rows as $row)
			{
				$rang++;
				$requete2 = "UPDATE `rkn_position` SET `rangrkn`= ".$rang." WHERE `idpos`=".$row['idpos']." ;";
				$res2=execute_requete($requete2);
            	if (!$res2)
				{
            		echo "ge/include/un_classement.php :: 52\nSQL : ".mysqli_error($connexion)."<br/>REQUETE INJECTION POSITION ::\nSQL &gt;\n$requete2\n";
				}
			}
		}
	}
}

// ------------------------
function set_classement_rkn($groupe, $une_periode){
// effectue le classement sur la période fournie pour le groupe indiqué.
// les données du champ rangrkn de la table des position est recalculée
// Ce qui signifie que cete valeur dépend des paramètres de sélection des voiliers à afficher

global $connexion;

$periode=reformate_periode($une_periode); // on compare les positions dans l'heure sinon on aura une flotte dispersée
// en différentes sou-flottes selon la minute de chargement des data

    if (!empty($connexion))
	{
        $requete='';
		if (empty($group))
		{
	        if (!empty($periode)){
				$requete = 'SELECT * FROM `rkn_position` WHERE `date`>="'.$periode->datemin.'" AND `date`<"'.$periode->datemax.'" ORDER BY `date` DESC;';
			}
			else{
				$requete = 'SELECT * FROM `rkn_position` ORDER BY `date` DESC;';
			}
		}
		else
		{
	        if (!empty($periode))
			{
				$requete = 'SELECT DISTINCT `rkn_position`.`idpos` FROM `rkn_group_boat` AS g, `rkn_position` AS p  WHERE g.`ref_group`='.$group.' AND g.`ref_boat`=p.`refbato`
 AND  p.`date`>="'.$periode->datemin.'" AND p.`date`<"'.$periode->datemax.'" ORDER BY p.`date` DESC;';
			}
			else
			{
				$requete = 'SELECT DISTINCT `rkn_position`.`idpos` FROM `rkn_group_boat` AS g, `rkn_position` AS p  WHERE g.`ref_group`='.$group.' AND g.`ref_boat`=p.`refbato` ORDER BY p.`date` DESC;';
			}
		}

		if (!empty($requete))
		{
	        // Chargement des données
    	    $rows = array();
			if (verifie_requete_select( $requete))
			{
				if ($res=execute_requete( $requete))
				{
					while ($row = mysqli_fetch_array($res))
					{
	                    $rows[] = $row;
					}
				}
				else
				{
    	       		echo "./include/un_classement.php :: 38\nSQL : ".mysqli_error($connexion)."<br />REQUETE RECHERCHE POSITION ::\nSQL &gt;\n$requete\n";
				}
	 		}

			// Mise à jour
			if (!empty($rows))
			{
				$rang=0;
				foreach ($rows as $row)
				{
					$rang++;
					$requete2 = "UPDATE `rkn_position` SET `rangrkn`= ".$rang." WHERE `idpos`=".$row['idpos']." ;";
					$res2=execute_requete($requete2);
            		if (!$res2)
					{
            			echo "./include/un_classement.php :: 52\nSQL : ".mysqli_error($connexion)."<br/>REQUETE INJECTION POSITION ::\nSQL &gt;\n$requete2\n";
					}
				}
			}
		}
	}
}


?>