<?php
/*
	Fichier d'appel des différents modules, fonctions et classes du core du site
*/

// On met en place l'affichage des erreurs en mode développement
error_reporting(-1); // -1 reporte toutes les erreurs PHP (=E_ALL) / 0 en mode production
ini_set('error_reporting', E_ALL);

// On détermine les problématiques de langage des données PHP
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR', 'fr');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

// On détermine le charset du fichier retourné par le serveur
header('Content-Type: text/html; charset=utf-8');

// On récupère le fichier de configuration
$config = parse_ini_file('config.ini', true);

// Appel de la classe MySQL
$db = new mysqli($config['BDD']['host'], $config['BDD']['user'], $config['BDD']['pass'], 'test');

// Constructeur de classes

function __autoload($class_name) {
	include 'class/'.$class_name.'.class.php';
}

// On appelle l'ensemble des classes générales au site
$core =			new core($db, $config['SERVER']['url']);
$csv =			new csv($db, $config['SERVER']['url']);
$user =			new user($db, $config['SERVER']['url']);
$fiche =			new fiche($db, $config['SERVER']['url']);
$tache =			new tache($db, $_COOKIE['leqg-user'], $config['SERVER']['url']);
$dossier =		new dossier($db, $_COOKIE['leqg-user'], $config['SERVER']['url']);
$historique =	new historique($db, $_COOKIE['leqg-user'], $config['SERVER']['url']);
$fichier =		new fichier($db, $_COOKIE['leqg-user'], $config['SERVER']['url']);
$carto =			new carto($db, $_COOKIE['leqg-user'], $config['SERVER']['url']);

// On transforme ces classes générales en variables globales
global $db, $core, $csv, $user, $fiche, $tache, $dossier, $historique, $fichier, $carto;

?>