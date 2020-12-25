// Javascript document
// JF
// Send Ajax calls to the vgv2020 webserver
// 1) Envoi des paramètres pour la génération des cartes Google Earth
// 2) récupère le nom du fichier KMZ sous form de blob ? A définir.
// Peut-être plutôt le nom du fichier à télécharger sur le serveur...
// Included in ./index.html
//

	// -------------------
	function positions_validate(){
		// envoie le paquet de donnees au webservice
        //console.debug(document.data.liste.value );
		if (document.data.liste.value == "")
		{
			//console.debug( "Données vides" );
            document.getElementById('imputvalue').innerHTML =  "Données vides";
            document.data.liste.focus() ;
            return false;
		}

	    var data = "boatname="+encodeURI(document.naming.nomboat.value)+"&course="+course+"&team="+team+"&scale="+scale+"&liste="+encodeURI(document.data.liste.value);
		//document.getElementById('imputvalue').innerHTML = "DATA SENT: "+data;
        document.getElementById('imputvalue').innerHTML = "Données envoyées";
        document.data.action.value = "Sending...";
		//console.debug("DATA SENT: %s",data);

		post_positions(data);
        return true;
	}

	// -------------------
	function post_positions(data){
		// console.debug("POST_PARAMS ::DATA SENT: %s",data);
		// var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/cartes_rkn/";
		// Voir le fichier config.js
        ajaxBox_loader(true);
		var xhr = new XMLHttpRequest();

		var url =	rknserveururl+"webservice/set_positions.php";
        // console.debug("POST_PARAMS ::URL: %s",url);

		xhr.open("POST", encodeURI(url), true);

		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Mandatory

		// The following code does'nt work because the SolServer is not CORS  (Cross-Origin Resource Sharing)!
		// xhr.setRequestHeader("Access-Control-Allow-Credentials", true);  // CORS (Cross-Origin Resource Sharing)
		// xhr.setRequestHeader("Access-Control-Allow-Origin", "*"); // CORS (Cross-Origin Resource Sharing)
        xhr.responseType = 'text';
		xhr.onload = function(e) {
			if (this.readyState === 4)  // Requête terminée
			{
 				if (this.status === 200)  // page trouvée
				{
                    //ajaxBox_setText(this.responseText);
					if ((this.response!==undefined) && (this.response.length>0))
					{
						var fname=decodeURIComponent(this.response);
            	        //console.debug("FNAME: %s",fname);
						if ((fname !== undefined) && (fname.length>0)
							&& (fname!=='empty file') && (fname!=='empty data'))
						{
 		        	    	document.data.action.value = "Valider";
	        		    	document.data.liste.value = "";
							document.getElementById('imputvalue').innerHTML = "Carte Google Earth disponible !  ";
        	                info_carte(fname);
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
		// POST
		xhr.send(data);
	}

