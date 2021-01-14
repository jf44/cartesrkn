// Javascript document
// JF
// Send Ajax calls to the vgv2020 web server
// 1) Stocke les paramètres pour la génération des cartes Google Earth
// Included in ./cartes_rkn.html
//

	var nomboat;


    // -------------------------------------
    // Set the boatname.
	function set_BoatName()
	{
		if (document.getElementById("nomboat").value != "")
		{
            nomboat=document.getElementById("nomboat").value;
            setCookie("rknnomboat", nomboat, 8);
			document.getElementById("nomboat").style.backgroundColor =  "#ffceff";
    		document.getElementById("nomboat").innerHTML=nomboat;
            document.getElementById("inputValue").innerHTML="Bonjour "+nomboat;
            get_kmz(nomboat);
  		}
        return true;
	}


	// -------------------------------
	function checkCookieBoatName()
	{
        // Find a nomboat
		var cookies = document.cookie;
		if (cookies !== null)
		{
  			nomboat = getCookie("rknnomboat");
            //console.log("COOKIES: %s",cookies);
            //console.log("rknnomboat: %s",nomboat);
 			if (nomboat.length)
			{
				document.getElementById("nomboat").style.backgroundColor =  "#ceceff";
    			document.getElementById("nomboat").innerHTML=nomboat;
                document.getElementById("nomboat").value=nomboat;
            	get_kmz(nomboat);
                //console.log("NOMBOAT: %s",nomboat);
				document.getElementById("inputValue").innerHTML =  "Bonjour "+nomboat+" !";
			}
			else
			{
           		// alert( "Fournissez un nom de bateau !" );
				document.getElementById("inputValue").innerHTML =  "Saisissez le nom du bateau ! Give a Boatname Please!";
    	        document.naming.nomboat.focus() ;
			}
		}
	}


	// -------------------
	function display_all(str)
	{
		if (str)
		{
			var res= str.split(",");
			var urlbase = rknserveururl+"webservice/kmz/";
			document.getElementById("myFiles").style.backgroundColor =  "#ceffce";
    		document.getElementById("myFiles").innerHTML="<h3>Fichiers KMZ pour votre bateau / KMZ files for your boat</h3>";
 			document.getElementById("myFiles").innerHTML+="<p>Les cartes G.E. sont distribuées au format KMZ indépendants du serveur / G.E. Maps are in KMZ format.</p>";
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
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Mandatory

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
								ajaxBox_setText("Error : "+obj.erreur);
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
