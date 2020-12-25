// Javascript document
// JF
// Display a color palette
// Included in ../index.php
// Included in ../select_color.html 

var tcolor = ["","","","",""];

// enregistre la valeur dans un tableau global
function placerUneCouleur(Hex){
var message="";
	if (document.getElementById("rCoque").checked == true)
	{
        tcolor[0]=Hex;
        document.getElementById("rCoque").checked = false;
        document.getElementById("rPont").checked = true;
 		document.getElementById("cCoque").style.backgroundColor = "#"+Hex;
	}
	else if (document.getElementById("rPont").checked == true)
	{
        tcolor[1]=Hex;
        document.getElementById("rPont").checked = false;
        document.getElementById("rGv").checked = true;
        document.getElementById("cPont").style.backgroundColor = Hex;
	}
	else if (document.getElementById("rGv").checked == true)
	{
        tcolor[2]=Hex;
        document.getElementById("rGv").checked = false;
        document.getElementById("rVav").checked = true;
        document.getElementById("cGv").style.backgroundColor = "#"+Hex;
		//message =  "Vous avez saisi la couleur de de la grand voile: "+Hex;
	}
	else if (document.getElementById("rVav").checked == true)
	{
        tcolor[3]=Hex;
        document.getElementById("rVav").checked = false;
        document.getElementById("rSpi").checked = true;
		document.getElementById("cVav").style.backgroundColor = "#"+Hex;
		//message =  "Vous avez saisi la couleur de la voile d'avant: "+Hex;
	}
	else if (document.getElementById("rSpi").checked == true)
	{
        tcolor[4]=Hex;
        document.getElementById("rSpi").checked = false;
        document.getElementById("rCoque").checked = true;
		document.getElementById("cSpi").style.backgroundColor = "#"+Hex;
        //message =  "Vous avez saisi la couleur du spi: "+Hex;
	}

    var strcolors="";
	for (var i=0; i<tcolor.length; i++)
	{
        if (i<tcolor.length-1)
		{
			strcolors += tcolor[i] + ",";
		}
		else
		{
            strcolors += tcolor[i];
		}
	}
	if ((strcolors != "") && (strcolors.length>=34))
	{
        set_couleurs();
		document.steering.submitBtn1.focus() ;
		message =  "Vous pouvez maintenant valider vos choix...";
	}

	// console.debug("couleurs chargées: "+strcolors );
	document.getElementById("couleurs").value= strcolors;
    if (message != "")
	{
		document.getElementById("imputvalue").innerHTML = message;
	}
}


function set_colors_palette(strcolors){
// initialise la palette après telechargement
	if (validate_color(strcolors)) // Tester que c'est une chaine d'hexa  "aaaaaa,bbbbbb,cccccc,dddddd,eeeeee"
	{
        //console.debug("COULEURS CHARGEES : %s",strcolors);
        set_couleurs();

        document.getElementById("couleurs").value= strcolors;
		document.getElementById("imputvalue").innerHTML = "Couleurs extraites du fichier des voiliers...";

        const regex = /"/g;
        tcolor=strcolors.replace(regex,'').split(",");
		// Debug
		//tcolor.forEach(function (value, index){
		//	console.log(index); //
    	//	console.log(value); //
		//});

		if (tcolor.length==1)
		{
			document.getElementById("cCoque").style.backgroundColor = "#"+tcolor[0];
		}
		else if (tcolor.length==2)
		{
			document.getElementById("cCoque").style.backgroundColor = "#"+tcolor[0];
            document.getElementById("cPont").style.backgroundColor = "#"+tcolor[1];
		}
		else if (tcolor.length==3)
		{
			document.getElementById("cCoque").style.backgroundColor = "#"+tcolor[0];
            document.getElementById("cPont").style.backgroundColor = "#"+tcolor[1];
            document.getElementById("cGv").style.backgroundColor = "#"+tcolor[2];
		}
		else if (tcolor.length==4)
		{
			document.getElementById("cCoque").style.backgroundColor = "#"+tcolor[0];
            document.getElementById("cPont").style.backgroundColor = "#"+tcolor[1];
            document.getElementById("cGv").style.backgroundColor = "#"+tcolor[2];
            document.getElementById("cVav").style.backgroundColor = "#"+tcolor[3];
		}
		else if (tcolor.length==5)
		{
			document.getElementById("cCoque").style.backgroundColor = "#"+tcolor[0];
            document.getElementById("cPont").style.backgroundColor = "#"+tcolor[1];
            document.getElementById("cGv").style.backgroundColor = "#"+tcolor[2];
            document.getElementById("cVav").style.backgroundColor = "#"+tcolor[3];
 	        document.getElementById("cSpi").style.backgroundColor = "#"+tcolor[4];
		}
		// Repositionner sur la coque
        document.getElementById("rCoque").checked == true;
        document.getElementById("rPont").checked == false;
        document.getElementById("rGv").checked == false;
        document.getElementById("rVav").checked == false;
        document.getElementById("rSpi").checked == false;

		return true;
	}
	return false;
}