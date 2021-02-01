# Vos voiliers dans Google Eath  / Your boats in Google Earth

<jean.fruitet@free.fr>

Version 0.2 du 1/2/2021

Cette application en ligne produit des carte Google Earth (.kmz) de votre bateau et de vos bateaux amis sur les courses
de Virtual Regata.
Elle s'appuie sur l'extension Chrome VR Dashbord pour récupérer les positions de bateaux sur la course sélectionnée.

Installez tout le paquet sur un serveur LAMP / WAMP local ou distant et paramétrez le fichier js/config_server.js

### Version de Google Earth
Les cartes générées contiennent des représentations 3D de voiliers virtuels réalisés avec une modélisation Collada (.dae)
Il faut installer la version Google Eath Professional (desktop version) pour les afficher.
Aucune des versions tablette ou Web de G.E. ne le fait correctement.

## Configuration des appels Ajax

Lors de l'installation des sources sur un serveur web vous devez configurer le fichier ./js/config_server.js

### Exemple de paramétrage

> - Serveur local
    var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/cartes_rkn/";
> - Serveur voilevirtuelle.free.fr
    var rknserveururl ="http://voilevirtuelle.free.fr/vgv2020/cartes_rkn/";

# Your boats in Google Earth

This app sets Google Earth maps (.kmz) of your friends boats in Virtual Ragatta's virtual sail races.
It uses the Chrome extension VR Dashboard to get the boats' positions

Install all this stuff in a directory of your LAMP or WAMP server and set the config_server.js file

### Google Earth Professional (Desktop version)
You have to install G.E Professional to display the 3D Collada (.dae) models.
None of the other versions of G.E. can display such files.

## Server calls configuration

When you are installing the sources on a local server you have to exchange the server definitions
in the config_server.js file.

### Examples

> - Local server
   var rknserveururl ="http://localhost:8080/voilevirtuelle/vgv2020/cartes_rkn/";
> - voilevirtuelle server
   var rknserveururl ="http://voilevirtuelle.free.fr/vgv2020/cartes_rkn/";



JF44.