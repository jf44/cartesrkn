// JavaScript Document
	// -----------------
	// Fonction ajoutant ou supprimant le 'loader'
	function ajaxBox_loader(pState){
		// Ajout de l'image de loader
		if (pState === true)
		{
			// Ajout d'un élement <img> d'id #ajaxBox_loader
			var ajaxBox = document.getElementById('ajaxBox');
			if (ajaxBox !== undefined)
			{
				var img = ajaxBox.appendChild(document.createElement('img'));
				img.id = 'ajaxBox_loader';
				img.src = 'images/giphy.gif';
			}
		}
		// Suppression de l'image de loading
		else
		{
			// Suppression de l'élement #ajaxBox_loader
			var ajaxBox_loader = document.getElementById('ajaxBox_loader');
			if ((ajaxBox_loader !== undefined) && ajaxBox_loader)
			{
				ajaxBox_loader.parentNode.removeChild(ajaxBox_loader);
			}
		}
	}

	// Fonction de mise à jour du contenu de la div #ajaxBox
	// Ajout d'un element <p> contenant le message, dans le div #ajaxBox
	// ---------------
	function ajaxBox_setText(pText){
		var elt = document.getElementById('ajaxBox');
		if (elt)
		{
			var p = elt.appendChild(document.createElement('p'));
			p.appendChild(document.createTextNode(pText));
		}
	}

