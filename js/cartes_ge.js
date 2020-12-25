// JavaScript Document
// Appelé par carte_ge.html == index.html


// -----------------
var printError = function(error, explicit) {
    console.log(`[${explicit ? 'EXPLICIT' : 'INEXPLICIT'}] ${error.name}: ${error.message}`);
}

// -----------------
//	convertion decimal ver hexa
function Hexa(Dec){
	if ((Dec>=0) & (Dec<=255))
	{
		var nb = Dec.toString(16);
		if (nb.length < 2)
		{
			nb = "0" + nb;
		}
		return(nb);
	}
	return "00";
}

// -----------------
function verifHexa(acolor){
	if (acolor != "")
	{
		var re = /^[0-9a-f]{6}$/;
		if (re.test(acolor) !== null)
		{
			return true;
		}
	}
	return false;
}


var tparam = ["","",""];
var course="";
var team="";
var scale="";
var nomboat="";


// -----------------
// enregistre la valeurs dans un tableau global
function placerUneValeur(index){
var message="";
	switch(index){
	  case 0 :
        var valeur = document.getElementById("cCourse").value;
		if (valeur.length==0)
		{
            document.getElementById("imputvalue").innerHTML = "Valeur nulle !";
			return false;
		}
        tparam[0]=valeur;
        course = valeur;
 		document.getElementById("cCourse").style.backgroundColor = "#ffffaa";
        document.getElementById("lCourse").innerHTML = valeur;
		//message =  "Vous avez saisi la Course "+Valeur;
		break;
	  case 1 :
        var valeur = document.getElementById("cTeam").value;
		if (valeur.length==0)
		{
            document.getElementById("imputvalue").innerHTML = "Valeur nulle !";
			return false;
		}
		tparam[1]= valeur;
	    team = valeur;
        document.getElementById("cTeam").style.backgroundColor = "#ffffaa";
 		document.getElementById("lTeam").innerHTML = valeur;
        //message =  "Vous avez saisi la Team "+Valeur;
		break;
	  default :
		var valeur = parseFloat(document.getElementById("cScale").value);
		//console.debug(valeur);
		if (isNaN(valeur))
		{
            document.getElementById("imputvalue").innerHTML = "Ce n'est pas un nombre !";
			return false;
		}
		tparam[2]= valeur;
		scale = valeur;
       	document.getElementById("cScale").style.backgroundColor = "#ffffaa";
		document.getElementById("lScale").innerHTML = valeur;
		break;
	}

    var strparams="";
	for (var i=0; i< tparam .length; i++)
	{
		if  (tparam [i]!==undefined)
		{
			if (i< tparam .length-1)
			{
				strparams +=  tparam [i] + ",";
			}
			else
			{
            	strparams +=  tparam [i];
			}
		}
	}
	if ((strparams != "") && (tparam.length>=3))
	{
        set_params();
		document.steering.submitBtn1.focus() ;
		message =  "Vous pouvez maintenant valider vos choix...";
	}

	// console.debug("couleurs chargées: "+strparams );
	document.getElementById("params").value= strparams;
    if (message != "")
	{
		document.getElementById("imputvalue").innerHTML = message;
	}
	return true;
}

// -----------------
function set_all_params(strparams){
// initialise la liste des paramètres après telechargement
	if (validate_params(strparams)) // Tester que c'est une chaine "param1,param2,param3"
	// "course,team,echelle"
	{
      	//console.debug(strparams);
	  	tparam=strparams.split(',')
  		document.getElementById("params").style.background = "#ffff33";
        document.getElementById("params").value= strparams;
		document.getElementById("imputvalue").innerHTML = "Paramètres rechargés...";

		if ( tparam.length==1)
		{
			document.getElementById("cCourse").style.backgroundColor =  "#ffffaa";
            document.getElementById("cCourse").value = tparam[0];
            document.getElementById("lCourse").innerHTML = tparam[0];
			course = tparam[0];
		}
		else if ( tparam.length==2)
		{
			document.getElementById("cCourse").style.backgroundColor =  "#aaffaa";
            document.getElementById("cCourse").value = tparam[0];
            document.getElementById("lCourse").innerHTML = tparam[0];
            course = tparam[0];
            document.getElementById("cTeam").style.backgroundColor = "#ffffaa";
            document.getElementById("cTeam").value = tparam[1];
            team = tparam[1];
        	document.getElementById("lTeam").innerHTML = tparam[1];
		}
		else if ( tparam.length==3)
		{
			document.getElementById("cCourse").style.backgroundColor = "#ffaaff";
            document.getElementById("cCourse").value = tparam[0];
            document.getElementById("lCourse").innerHTML = tparam[0];
            course = tparam[0];
            document.getElementById("cTeam").style.backgroundColor = "#aaffaa";
            document.getElementById("cTeam").value = tparam[1];
            document.getElementById("lTeam").innerHTML = tparam[1];
            team = tparam[1];
            document.getElementById("cScale").style.backgroundColor = "#ffffaa";
            document.getElementById("cScale").value = tparam[2];
            document.getElementById("lScale").innerHTML = tparam[2];
            scale = tparam[2];
			return true;
		}
	}
	return false;
}

// -------------------
function help1(){
    document.getElementById("rightDiv").style.backgroundColor =  "#ceffce";
    document.getElementById("rightDiv").innerHTML="<h2>Les voiliers du Vendée Globe virtuel 2020</h2>";
 	document.getElementById("rightDiv").innerHTML+="<p>Pour l'affichage le modèle autorise 4 couleurs simultanément à choisir parmi 5 que vous me fournissez  : coque, pont, grand'voile, une voile d'avantet un spi...)</p>";
 	document.getElementById("rightDiv").innerHTML+="<br /><img src=\"images/RKN_CZ.png\" border=\"0\" alt=\"GV + Foc\" title=\"GV + Foc\" />";
	document.getElementById("rightDiv").innerHTML+="<img src=\"images/RKN_GENOIS.png\" border=0\" alt=\"GV + Génois\" title=\"GV + Génois\" />";
	document.getElementById("rightDiv").innerHTML+="<img src=\"images/RKN_SPI.png\" border=0\" alt=\"GV + Spi\" title=\"GV + Spi\" />";
	document.getElementById("rightDiv").innerHTML+="<br /><span class=\"small\">Vues des modèles affichés dans G.E.</span></p>";
	document.getElementById("rightDiv").innerHTML+="<h3>Couleurs de vos bateaux</h3>";
	document.getElementById("rightDiv").innerHTML+="<p>Pour que vos amis choisissent leurs couleurs de voile et de coque fournissez leur le lien :<br />";
	document.getElementById("rightDiv").innerHTML+="<a target=\"_blank\" href=\"http://voilevirtuelle.free.fr/vgv2020/ge/cartes_rkn/color_picker.html\">http://voilevirtuelle.free.fr/vgv2020/ge/cartes_rkn/color_picker.html</a></p>";
}

// -------------------
function helpkmz(){
    document.getElementById("rightDiv").style.backgroundColor =  "#ceffce";
    document.getElementById("rightDiv").innerHTML="<h2>Lire les fichiers KML / KMZ</h2>";
 	document.getElementById("rightDiv").innerHTML+="<p>Les cartes G.E. sont distribuées au format KML /KMZ indépendants du serveur<br />";
 	document.getElementById("rightDiv").innerHTML+="<li>Cliquez sur <b>Télécharger</b></li>\n";
    document.getElementById("rightDiv").innerHTML+="<li>Si le fichier s'enregistre comme un fichier <i><b>.ZIP</b></i>\" renommez le <i><b>.kmz</b></i>\"</li>\n";
    document.getElementById("rightDiv").innerHTML+="<li>Puis ouvrez le dans Google Earth; au besoin supprimez le contenu du dossier \"Lieux préférés\".</li>\n";
    document.getElementById("rightDiv").innerHTML+="<li>... et profitez de la vue ! :))</li>\n";
}

// -------------------
function info_carte(fname){
    document.getElementById("myMap").style.backgroundColor =  "#ccccff";
    document.getElementById("myMap").innerHTML="<h2>Génération de la carte Google Earth</h2>";
    document.getElementById("myMap").innerHTML+="<p>Votre fichier Google Earth est disponible : <a target=\"_blank\" href=\""+ fname +"\">Télécharger</a></p>";
    document.getElementById("myMap").innerHTML+="<h3>Lire les fichiers KML / KMZ</h3>";
 	document.getElementById("myMap").innerHTML+="<p>Les cartes G.E. sont distribuées au format KML /KMZ indépendants du serveur<br />";
 	document.getElementById("myMap").innerHTML+="<li>Cliquez sur <b>Télécharger</b></li>\n";
    document.getElementById("myMap").innerHTML+="<li>Si le fichier s'enregistre comme un fichier <i><b>.ZIP</b></i>\" renommez le <i><b>.kmz</b></i>\"</li>\n";
    document.getElementById("myMap").innerHTML+="<li>Puis ouvrez le dans Google Earth; au besoin supprimez le contenu du dossier \"Lieux préférés\".</li>\n";
    document.getElementById("myMap").innerHTML+="<li>... et profitez de la vue ! :))</li>\n";
}

// http://localhost:8080/voilevirtuelle/vgv2020/ge/cartes_rkn/webservice/kmz/VGv2020_RKN_Iroizo+C-RKN_3D2020122414.kmz