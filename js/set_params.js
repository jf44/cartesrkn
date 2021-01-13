// Javascript document
// JF
// Send Ajax calls to the vgv2020 web server
// 1) Stocke les paramètres pour la génération des cartes Google Earth
// Included in ./cartes_rkn.html
//


    // -------------------------------------
    // Set the params background command.
	function get_params()
	{
		if (document.naming.nomboat.value != "")
		{
            return (get_params_boat(document.naming.nomboat.value)); // Appel Ajax
		}

        return true;
	}



    // -------------------------------------
    // Set the params background command.
	function set_params()
	{
		if (document.steering.params.value != "")
		{
            document.steering.params.style.background = "#ffff33";
		}

        return true;
	}



	// -------------------------------------
	function validate_params(strparam)
	{
		if (strparam != "")
		{
			//alert("validate_cparam STRPARAM",strparam);
			//console.debug(strparam);
	        var re = /(.+,){3}(.+)$/; // 4 chaines non vide avec separateur ','
			if (re.test(strparam) !== null)
			{
                //console.debug("--> OK");
				return true;
			}
		}
        //console.debug("--> ERROR");
		return false;
	}

	// -------------------------------
    // Form validation code will come here.
	function steeringvalidate_params()
	{
		if ((document.steering.params.value != "") && (document.naming.nomboat.value != "") )
		{
			if (!validate_params(document.steering.params.value))
			{
 				//console.debug( "params invalides" );
            	document.getElementById('imputvalue').innerHTML =  "Paramètres invalides";
            	document.steering.params.focus() ;
                return false;
			}

	        var data = "boatname="+encodeURI(document.naming.nomboat.value)+"&params="+encodeURI(document.steering.params.value);
			document.getElementById('imputvalue').innerHTML = "DATA SENT: "+data;
        	document.steering.action.value = "Sending...";
			//console.debug("DATA SENT: %s",data);

			post_params_boat(data);
		}
        return true;
	}


	// -------------------
	function post_params_boat(data)
	{
		//console.debug("POST_PARAMS ::DATA SENT: %s",data);
		//var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/cartes_rkn/";
		// Voir l fichier config.js
		var xhr = new XMLHttpRequest();

		var url =	rknserveururl+"webservice/set_ge_params.php";
        //console.debug("POST_PARAMS ::URL: %s",url);

		xhr.open("POST", encodeURI(url), true);

		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Mandatory

		// The following code does'nt work because the SolServer is not CORS  (Cross-Origin Resource Sharing)!
		// xhr.setRequestHeader("Access-Control-Allow-Credentials", true);  // CORS (Cross-Origin Resource Sharing)
		// xhr.setRequestHeader("Access-Control-Allow-Origin", "*"); // CORS (Cross-Origin Resource Sharing)
        xhr.responseType = 'text';
		xhr.onload = function(e) {
			if (this.readyState === 4 && this.status === 200) {
                //console.debug("Content-Type: %s", xhr.getResponseHeader("Content-Type"));
				document.getElementById('imputvalue').innerHTML = "Paramètres enregistrés !  ";
				document.steering.params.style.backgroundColor = "#ffffcc";
		        document.getElementById('cTeam').style.backgroundColor = "#ffffff";
        		document.getElementById('cCourse').style.backgroundColor = "#ffffff";
		        document.getElementById('cScale').style.backgroundColor = "#ffffff";
                document.steering.action.value = "Valider";
			}
		};
		// POST
		xhr.send(data);
	}

	// -------------------
	function get_params_boat(boatname)
	{
		if (boatname != "")
		{
			//var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/";
			// Deplacé dans le fichier de configuration
			//console.debug("BOATNAME: %s",boatname);
            var url =	rknserveururl+"webservice/get_ge_params.php?boatname="+boatname;

			var xhr = new XMLHttpRequest();

        	xhr.open("GET", encodeURI(url), true);

			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Mandatory

			// The following code does'nt work on the the SolServer because it is not CORS  (Cross-Origin Resource Sharing)!
			// xhr.setRequestHeader("Access-Control-Allow-Credentials", true);  // CORS (Cross-Origin Resource Sharing)
			// xhr.setRequestHeader("Access-Control-Allow-Origin", "*"); // CORS (Cross-Origin Resource Sharing)

        	xhr.responseType = 'text';
			xhr.onload = function(e)
			{
				if (this.readyState === 4 && this.status === 200)
				{
					//console.log("Content-Type: %s", xhr.getResponseHeader("Content-Type"));

					if ((this.response!==undefined) && (this.response.length>0))
					{
        	            console.debug("RESPONSE: %s",this.response);
                        var json = this.response;
                        // Converting JSON-encoded string to JS object
						var obj = JSON.parse(json);
                        // Accessing individual value from JS object
						if (obj.params)
						{
							set_all_params(obj.params);
						}
 					}
	 			}
			};

			// GET
			xhr.send();
		}
	}

	// -------------------
	function select_modele_3D()
	{
		if ((tmodeles !== null) && (tmodeles !== "undefined"))
		{
 			for(const item of tmodeles)
			{
                if (item==modele)
				{
					document.getElementById("cModele").innerHTML += "<option value=\""+item+"\" selected>"+item+"</option>";
				}
				else
				{
					document.getElementById("cModele").innerHTML += "<option value=\""+item+"\">"+item+"</option>";
				}
			}
		}
	}


    // -------------------
	function get_models3D()
	{
			//var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/";
			// Deplacé dans le fichier de configuration
			//console.debug("BOATNAME: %s",boatname);
            var url =	rknserveururl+"webservice/get_models.php";
			var xhr = new XMLHttpRequest();
        	xhr.open("GET", encodeURI(url), true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Mandatory

			// The following code does'nt work on the SolServer is not CORS  (Cross-Origin Resource Sharing)!
			// xhr.setRequestHeader("Access-Control-Allow-Credentials", true);  // CORS (Cross-Origin Resource Sharing)
			// xhr.setRequestHeader("Access-Control-Allow-Origin", "*"); // CORS (Cross-Origin Resource Sharing)


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
  							// Accessing individual value from JS object
							if (obj.modeles)
							{
								//console.debug("MODELES  : "+obj.modeles);
								tmodeles=obj.modeles.split(",");
								modele=tmodeles[0];
                                select_modele_3D();
							}
                            else
							{
								ajaxBox_setText("Error Models");
							}
 						}
					}
					else
					{
                   		ajaxBox_setText('Error...');
					}
				}

			};

			// GET
			xhr.send();
	}

