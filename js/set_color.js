// Javascript document
// JF
// Send an Ajax call to the vgc20 data base
// Included in ../index.php
// Included in ../select_color.html 

    // -------------------------------------
    // Set the couleurs background command.
	function get_couleurs()
	{
		if (document.naming.nomboat.value != "")
		{
            return (get_colors_boat(document.naming.nomboat.value)); // Appel Ajax
		}

        return true;
	}



    // -------------------------------------
    // Set the couleurs background command.
	function set_couleurs()
	{
		if (document.steering.couleurs.value != "")
		{
            document.steering.couleurs.style.background = "#ffff33";
		}

        return true;
	}



	// -------------------------------------
	function validate_color(strcolor)
	{
		if (strcolor != "")
		{
			//alert("validate_color STRCOLOR",strcolor);
			// console.debug(strcolor);
	        var re = /([0-9a-f]{6},){5}$/;
			if (re.test(strcolor) !== null)
			{
                // console.debug("--> OK");
				return true;
			}
		}
        //console.debug("--> ERROR");
		return false;
	}

	// -------------------------------
    // Form validation code will come here.
	function steeringvalidate_color()
	{
		if ((document.steering.couleurs.value != "") && (document.naming.nomboat.value != "") )
		{
			if (!validate_color(document.steering.couleurs.value))
			{
 				//console.debug( "Couleurs invalides" );
            	document.getElementById('imputvalue').innerHTML =  "Couleurs invalides";
            	document.steering.couleurs.focus() ;
                return false;
			}

	        var data = "boatname="+encodeURI(document.naming.nomboat.value)+"&couleur="+encodeURI(document.steering.couleurs.value);
			document.getElementById('imputvalue').innerHTML = "DATA SENT: "+data;
        	document.steering.action.value = "Sending...";
			//console.debug("DATA SENT: %s",data);

			post_colors_boat(data);
		}
        return true;
	}


	// -------------------
	function post_colors_boat(data)
	{
		//console.debug("POST_COLORS ::DATA SENT: %s",data);
		//var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/";

		var xhr = new XMLHttpRequest();

		var url =	rknserveururl+"webservice/set_boat_color.php";
        //console.debug("POST_COLORS ::URL: %s",url);

		xhr.open("POST", encodeURI(url), true);

		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Mandatory

		// The following code does'nt work because the SolServer is not CORS  (Cross-Origin Resource Sharing)!
		// xhr.setRequestHeader("Access-Control-Allow-Credentials", true);  // CORS (Cross-Origin Resource Sharing)
		// xhr.setRequestHeader("Access-Control-Allow-Origin", "*"); // CORS (Cross-Origin Resource Sharing)
        xhr.responseType = 'text';
		xhr.onload = function(e) {
			if (this.readyState === 4 && this.status === 200) {
                //console.debug("Content-Type: %s", xhr.getResponseHeader("Content-Type"));
				document.getElementById('imputvalue').innerHTML = "Donn&eacute;es envoy&eacute;es !  ";
        // RAZ
		document.steering.couleurs.style.backgroundColor = "#ffffcc";
        document.steering.couleurs.value="";
        document.steering.couleurs.innerHTML="";
        document.naming.nomboat.value="";
        document.naming.nomboat.innerHTML="";
		document.steering.rCoque.checked == true;
        document.steering.rPont.checked == false;
        document.steering.rGv.checked == false;
        document.steering.rVav.checked == false;
        document.steering.rSpi.checked == false;
		document.getElementById('cCoque').style.backgroundColor = "#ffffff";
        document.getElementById('cPont').style.backgroundColor = "#ffffff";
        document.getElementById('cGv').style.backgroundColor = "#ffffff";
        document.getElementById('cVav').style.backgroundColor = "#ffffff";
        document.getElementById('cSpi').style.backgroundColor = "#ffffff";
        tcolor=[];

                document.steering.action.value = "Valider";
			}
		};
		// POST
		xhr.send(data);
	}

	// -------------------
	function get_colors_boat(boatname)
	{
		if (boatname != "")
		{
			//var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/";
			// Deplacé dans le fichier de configuration
			//console.debug("BOATNAME: %s",boatname);
            var url =	rknserveururl+"webservice/get_boat_color.php?boatname="+boatname;

			var xhr = new XMLHttpRequest();

        	xhr.open("GET", encodeURI(url), true);

        	xhr.responseType = 'text';
			xhr.onload = function(e)
			{
				if (this.readyState === 4 && this.status === 200)
				{
					//console.log("Content-Type: %s", xhr.getResponseHeader("Content-Type"));

					if ((this.response!==undefined) && (this.response.length>0))
					{
        	            //console.debug("RESPONSE: %s",this.response);
                        var json = this.response;
                        // Converting JSON-encoded string to JS object
						var obj = JSON.parse(json);
                        // Accessing individual value from JS object
						return set_colors_palette(obj.couleur);
 					}
	 			}
			};

			// GET
			xhr.send();
		}
	}
