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

// Appel de la classe MySQL du noyau
$noyau = new mysqli($config['BDD']['host'], $config['BDD']['user'], $config['BDD']['pass'], 'leqg');

// Constructeur de classes
function __autoload($class_name) {
	include 'class/'.$class_name.'.class.php';
}

// On regarde les cookies
if (isset($_COOKIE['leqg-user'])) {
	$query = 'SELECT * FROM users LEFT JOIN clients ON users.client_id = clients.client_id WHERE user_id = ' . $_COOKIE['leqg-user'];
	$sql = $noyau->query($query);
	$row = $sql->fetch_assoc();
	
	$cookie = $_COOKIE['leqg-user'];
	$base = $row['client_bdd'];
} else { $base = null; $cookie = null; }

// On met à jour l'heure de dernière action pour le membre connecté
	$noyau->query('UPDATE `users` SET `user_lastaction` = NOW() WHERE `user_id` = ' . $_COOKIE['leqg-user']);

// Appel de la classe MySQL du compte
$db = new mysqli($config['BDD']['host'], $config['BDD']['user'], $config['BDD']['pass'], $base);

// On appelle l'ensemble des classes générales au site
$core =			new core($db, $noyau, $config['SERVER']['url']);
$csv =			new csv($db, $config['SERVER']['url']);
$user =			new user($db, $noyau, $config['SERVER']['url']);
$fiche =		new fiche($db, $cookie, $config['SERVER']['url']);
$tache =		new tache($db, $cookie, $config['SERVER']['url']);
$dossier =		new dossier($db, $cookie, $config['SERVER']['url']);
$historique =	new historique($db, $cookie, $config['SERVER']['url']);
$fichier =		new fichier($db, $cookie, $config['SERVER']['url']);
$carto =		new carto($db, $cookie, $config['SERVER']['url']);
$mission =		new mission($db, $cookie, $config['SERVER']['url']);
$notification =	new notification($db, $cookie, $config['SERVER']['url']);

// On transforme ces classes générales en variables globales
global $db, $noyau, $config, $core, $csv, $user, $fiche, $tache, $dossier, $historique, $fichier, $carto, $mission, $notification;

// On charge les API extérieures
require_once 'api/esendex/autoload.php';
require_once 'api/phpmailer/class.phpmailer.php';

// On configure les données des API extérieures
$api['sms']['auth'] = new \Esendex\Authentication\LoginAuthentication($config['SMS']['compte'], $config['SMS']['login'], $config['SMS']['pass']);

$api['mail']['charset'] = 'UTF-8';
$api['mail']['smtp']['host'] = $config['MAIL']['host'];
$api['mail']['smtp']['user'] = $config['MAIL']['user'];
$api['mail']['smtp']['pass'] = $config['MAIL']['pass'];
$api['mail']['smtp']['port'] = $config['MAIL']['port'];
$api['mail']['from']['email'] = 'no-reply@leqg.info';
$api['mail']['from']['nom'] = 'Ne Pas Répondre';
$api['mail']['reply']['email'] = 'serveur@leqg.info';
$api['mail']['reply']['nom'] = 'LeQG';

?>