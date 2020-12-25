// Javascript document
// JF
// Send Ajax calls to the vgv2020 web server
// 1) Stocke les paramètres pour la génération des cartes Google Earth
// Included in ./cartes_rkn.html
//

	var nomboat;

	// -------------------------------
	function checkCookieBoatName()
	{
  		nomboat = getCookie("rknnomboat");

  		if (nomboat == "")
		{
			nomboat = prompt("Nom de votre bateau :", "");
			if (nomboat != "" && nomboat != null)
			{
				setCookie("rknnomboat", nomboat, 8);
			}
  		}

  		if (nomboat != "")
		{
			document.getElementById("nomboat").style.backgroundColor =  "#ceceff";
    		document.getElementById("nomboat").innerHTML="Bienvenue "+nomboat;
            get_kmz(nomboat);
  		}
	}
	// Uncaught SyntaxError: unexpected token: identifier get_kmz.js:47:88


	// -------------------
	function display_all(str)
	{
		if (str)
		{
			var res= str.split(",");
			var urlbase = rknserveururl+"webservice/kmz/";
			document.getElementById("myFiles").style.backgroundColor =  "#ceffce";
    		document.getElementById("myFiles").innerHTML="<h3>Liste des fichiers KMZ pour votre bateau</h3>";
 			document.getElementById("myFiles").innerHTML+="<p>Les cartes G.E. sont distribuées au format KML /KMZ indépendants du serveur</p>";
            document.getElementById("myFiles").innerHTML+="<ul>";

			for(const item of res)
			{
                var url=urlbase+item;
                document.getElementById("myFiles").innerHTML+="<li><a href=\""+url+"\">"+item+"</a></li>";
			}
            document.getElementById("myFiles").innerHTML+="</ul>";
		}
	}


	// -------------------
	function get_kmz(boatname)
	{
		if (boatname != "")
		{
			//var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/";
			// Deplacé dans le fichier de configuration
			//console.debug("BOATNAME: %s",boatname);
            var url =	rknserveururl+"webservice/kmz/get_kmz.php?boatname="+boatname;
			var xhr = new XMLHttpRequest();
        	xhr.open("GET", encodeURI(url), true);
        	xhr.responseType = 'text';
			xhr.onload = function(e)
			{
				if (this.readyState === 4)  // Requête terminée
				{
 					if (this.status === 200)  // page trouvée
					{
 						if ((this.response!==undefined) && (this.response.length>0))
						{
                            //console.debug("RESPONSE : "+this.responseText);
							var obj = JSON.parse(this.responseText);
                            if ((obj.erreur !==null) && (obj.erreur !== undefined) && (obj.erreur !== "OK"))
							{
								ajaxBox_setText("Erreur : "+obj.erreur);
							}
  							// Accessing individual value from JS object
							else if (obj.fichiers)
							{
								// console.debug("FICHIERS  : "+obj.fichiers);
								display_all(obj.fichiers);
							}
 						}
					}
					else
					{
                   		ajaxBox_setText('Error...');
					}
                	// Stopper le loading
					ajaxBox_loader(false);
				}

			};

			// GET
			xhr.send();
		}
	}
