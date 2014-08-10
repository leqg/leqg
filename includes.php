<?php
/*
	Fichier d'appel des différents modules, fonctions et classes du core du site
*/

// On met en place l'affichage des erreurs en mode développement
error_reporting(-1); // -1 reporte toutes les erreurs PHP (=E_ALL) / 0 en mode production
ini_set('error_reporting', E_ALL);

// On détermine les problématiques de langage des données PHP
setlocale(LC_ALL, 'fr_FR', 'fr');

// On détermine le charset du fichier retourné par le serveur
header('Content-Type: text/html; charset=utf-8');

// On récupère le fichier de configuration
$config = parse_ini_file('config.ini', true);

// Appel de la classe MySQL
$db = new mysqli($config['bdd']['host'], $config['bdd']['user'], $config['bdd']['pass'], 'test');

// Constructeur de classes

function __autoload($class_name) {
	include 'class/'.$class_name.'.class.php';
}

// On appelle l'ensemble des classes générales au site
$core =			new core($db);
$user =			new user($db);
$fiche =			new fiche($db);
$tache =			new tache($db, $_COOKIE['leqg-user']);
$dossier =		new dossier($db, $_COOKIE['leqg-user']);
$historique =	new historique($db, $_COOKIE['leqg-user']);
$fichier =		new fichier($db, $_COOKIE['leqg-user']);
$carto =			new carto($db, $_COOKIE['leqg-user']);

// On transforme ces classes générales en variables globales
global $db, $core, $user, $fiche, $tache, $dossier, $historique, $fichier, $carto;

?>