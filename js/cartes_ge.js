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


var tparam = [];
var modele="";
var tmodeles=[];
var course="";
var team="";
var scale="";
var nomboat="";


// -----------------
// enregistre la valeurs dans un tableau global
function placerUneValeur(index){
var message="";
    var strparams="";
	switch(index)
	{
		case 0 :
        var valeur = document.getElementById("cCourse").value;
		if (valeur.length==0)
		{
            document.getElementById("imputvalue").innerHTML = "Valeur nulle !";
			return false;
		}
        else
		{
            document.getElementById("imputvalue").innerHTML = "Course Ok !";
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
        else
		{
            document.getElementById("imputvalue").innerHTML = "Team Ok !";
		}

		tparam[1]= valeur;
	    team = valeur;
        document.getElementById("cTeam").style.backgroundColor = "#ffffaa";
 		document.getElementById("lTeam").innerHTML = valeur;
        //message =  "Vous avez saisi la Team "+Valeur;
		break;
  		case 2 :
		var input = document.getElementById("cScale").value;
        // console.debug("Saisie "+input);
        var valeur = parseFloat(input);
        if (isNaN(valeur) )
		{
        	var valeur = parseInt(input);
		}
		if (isNaN(valeur))
		{
            document.getElementById("imputvalue").innerHTML = "Ce n'est pas un nombre !";
			return false;
		}
        //console.debug("Valeur "+valeur);

		tparam[2]= valeur;
		scale = valeur;
       	document.getElementById("cScale").style.backgroundColor = "#ffffaa";
		document.getElementById("lScale").innerHTML = valeur;
		break;
    	case 3 :
		var valeur = document.getElementById("cModele").value;
		//console.debug(valeur);
		if (valeur.length==0)
		{
            document.getElementById("imputvalue").innerHTML = "Valeur nulle !";
			return false;
		}

		tparam[3]= valeur;
		modele = valeur;
       	document.getElementById("cModele").style.backgroundColor = "#ffffaa";
		document.getElementById("lModele").innerHTML = valeur;
		break;
	}
    var strparams="";
	if (tparam.length>=4)
	{
		for (var i=0; i<tparam.length-1; i++)
		{
            strparams+=tparam[i]+",";
		}
        strparams+=tparam[3];
        //console.debug(strparams);
        set_params();
		document.steering.submitBtn1.focus() ;
		message =  "Vous pouvez maintenant valider vos choix... / You can validate your choices...";
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
		document.getElementById("imputvalue").innerHTML = "Paramètres rechargés... / Parameters reloaded ";

		if (tparam.length==1)
		{
			document.getElementById("cCourse").style.backgroundColor =  "#ffffaa";
            document.getElementById("cCourse").value = tparam[0];
            document.getElementById("lCourse").innerHTML = tparam[0];
			course = tparam[0];
		}
		else if (tparam.length==2)
		{
			document.getElementById("cCourse").style.backgroundColor =  "#ffaaff";
            document.getElementById("cCourse").value = tparam[0];
            document.getElementById("lCourse").innerHTML = tparam[0];
            course = tparam[0];
            document.getElementById("cTeam").style.backgroundColor = "#ffffaa";
            document.getElementById("cTeam").value = tparam[1];
            team = tparam[1];
        	document.getElementById("lTeam").innerHTML = tparam[1];
		}
		else if (tparam.length==3)
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
		else if (tparam.length==4)
		{
			document.getElementById("cCourse").style.backgroundColor = "#ffaaff";
            document.getElementById("cCourse").value = tparam[0];
            document.getElementById("lCourse").innerHTML = tparam[0];
            course = tparam[0];
            document.getElementById("cTeam").style.backgroundColor = "#aaffaa";
            document.getElementById("cTeam").value = tparam[1];
            document.getElementById("lTeam").innerHTML = tparam[1];
            team = tparam[1];
            document.getElementById("cScale").style.backgroundColor = "#aaaaff";
            document.getElementById("cScale").value = tparam[2];
            document.getElementById("lScale").innerHTML = tparam[2];
            scale = tparam[2];
            document.getElementById("cModele").style.backgroundColor = "#ffaaaa";
            document.getElementById("cModele").value = tparam[3];
            document.getElementById("lModele").innerHTML = tparam[3];
            modele = tparam[3];
			return true;
		}
	}
	return false;
}

// -------------------
function help(){
    document.getElementById("myHelp").style.backgroundColor =  "#ceceaa";
    document.getElementById("myHelp").innerHTML="<h3>Sélection des paramètres d'affichage</h3>";
    document.getElementById("myHelp").innerHTML="<p><b>Echelle des modèles</b><br>Elle détermine la taille des modèles dans Google Earth. <span class=\"small\"><i>Entre 10 et 20</i> --&gt; Courses océaniques, sinon voiliers invisibles ! <i>De 2 à 10</i> --&gt;  Détroits et îles ; <i>De 0.1 et 2</i> --&gt; Passages étroits et arrivées de courses, sinon les modèles se superposent.</span></p>";
    document.getElementById("myHelp").innerHTML+="<img src=\"images/voiliers_echelles.jpg\" border=\"0\" alt=\"Echelles 0.1 à 12\" title=\"Echelles 0.1 à 12\" />";
	document.getElementById("myHelp").innerHTML+="<p><b>Modèles 3D</b><br>Les modèles sont simplifiés au maximum pour accélérer la restitution. Si vous souhaitez ajouter votre propre modèle de voilier contactez-moi.</p>";
}

// -------------------
function help_en(){
    document.getElementById("myHelp").style.backgroundColor =  "#ceceaa";
    document.getElementById("myHelp").innerHTML="<h3>Display pParameters Selection</h3>";
    document.getElementById("myHelp").innerHTML="<p><b>Models' scale</b><br>The scale set the models size in G.E. <span class=\"small\"><i>Between 10 and 20</i> --&gt; Ocean races, otherway boats are too small! <i>2 to 10</i> --&gt; Islands and Narrows; <i>0.1 to 2</i> --&gt; Very narrow and Arrival, otherway the boats are packed.</span></p>";
    document.getElementById("myHelp").innerHTML+="<img src=\"images/voiliers_echelles.jpg\" border=\"0\" alt=\"Echelles 0.1 à 12\" title=\"Echelles 0.1 à 12\" />";
	document.getElementById("myHelp").innerHTML+="<p><b>3D Models</b><br>Very simplificated to speed up the restitution. If you like add your own boat model contact-me.</p>";
}

// -------------------
function help1(){
    document.getElementById("rightDiv").style.backgroundColor =  "#ceffce";
    document.getElementById("rightDiv").innerHTML="<h2>Les voiliers du Vendée Globe virtuel 2020</h2>";
 	document.getElementById("rightDiv").innerHTML+="<p>Pour l'affichage le modèle autorise 4 couleurs simultanément à choisir parmi 5 que vous me fournissez  : coque, pont, grand'voile, une voile d'avantet un spi...)</p>";
 	document.getElementById("rightDiv").innerHTML+="<br /><img src=\"images/RKN_CZ.png\" border=\"0\" alt=\"GV + Foc\" title=\"GV + Foc\" />";
	document.getElementById("rightDiv").innerHTML+="<img src=\"images/RKN_GENOIS.png\" border=\"0\" alt=\"GV + Génois\" title=\"GV + Génois\" />";
	document.getElementById("rightDiv").innerHTML+="<img src=\"images/RKN_SPI.png\" border=\"0\" alt=\"GV + Spi\" title=\"GV + Spi\" />";
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
    document.getElementById("myMap").innerHTML+="<p>Votre fichier Google Earth est disponible / the map is here: <a target=\"_blank\" href=\""+ fname +"\">Télécharger / Download</a></p>";
    document.getElementById("myMap").innerHTML+="<h3>Lire les fichiers KMZ</h3>";
 	document.getElementById("myMap").innerHTML+="<p>Les cartes G.E. sont distribuées au format KMZ indépendants du serveur<br />";
 	document.getElementById("myMap").innerHTML+="<li>Cliquez sur <b>Télécharger / Download</b></li>\n";
    document.getElementById("myMap").innerHTML+="<li>Si le fichier s'enregistre comme un fichier <i><b>.ZIP</b></i>\" renommez le <i><b>.kmz</b></i>\"</li>\n";
    document.getElementById("myMap").innerHTML+="<li>Puis ouvrez le dans Google Earth; au besoin supprimez le contenu du dossier \"Lieux préférés\".</li>\n";
    document.getElementById("myMap").innerHTML+="<li>... et profitez de la vue ! :))</li>\n";
}

// http://localhost:8080/voilevirtuelle/vgv2020/ge/cartes_rkn/webservice/kmz/VGv2020_RKN_Iroizo+C-RKN_3D2020122414.kmz