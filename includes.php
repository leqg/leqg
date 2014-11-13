<?php
/*
	Fichier d'appel des différents modules, fonctions et classes du core du site
*/

// On détermine les problématiques de langage des données PHP
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR', 'fr');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

// On détermine le charset du fichier retourné par le serveur
header('Content-Type: text/html; charset=utf-8');

// On récupère le fichier de configuration
$config = parse_ini_file('config.ini', true);

// On lance la classe de configuration
class Configuration
{
	static $confArray;
	
	public static function read($name)
	{
		return self::$confArray[$name];
	}
	
	public static function write($name, $value)
	{
		self::$confArray[$name] = $value;
	}
}

// On applique la configuration chargée
Configuration::write('db.host', $config['BDD']['host']);
Configuration::write('db.basename', 'strasbourg');
Configuration::write('db.user', $config['BDD']['user']);
Configuration::write('db.pass', $config['BDD']['pass']);

// On fabrique la classe $link de liaison PDO
$link = new PDO('mysql:host=' . Configuration::read('db.host') . ';dbname=' . Configuration::read('db.basename') . ';charset=utf8', Configuration::read('db.user'), Configuration::read('db.pass'));	

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
if (isset($_COOKIE['leqg-user']))
{
	$noyau->query('UPDATE `users` SET `user_lastaction` = NOW() WHERE `user_id` = ' . $_COOKIE['leqg-user']);
}

// Appel de la classe MySQL du compte
$db = new mysqli($config['BDD']['host'], $config['BDD']['user'], $config['BDD']['pass'], $base);

// On appelle l'ensemble des classes générales au site
$core =			new core($db, $noyau, $config['SERVER']['url']);
$csv =			new csv();
$user =			new user($db, $noyau, $config['SERVER']['url']);
$fiche =		new fiche($db, $cookie, $config['SERVER']['url']);
$tache =		new tache($db, $cookie, $config['SERVER']['url']);
$dossier =		new dossier($db, $cookie, $config['SERVER']['url']);
$historique =	new historique($db, $cookie, $config['SERVER']['url']);
$fichier =		new fichier($db, $cookie, $config['SERVER']['url']);
$carto =		new carto($db, $noyau, $config['SERVER']['url']);
$mission =		new mission($db, $cookie, $config['SERVER']['url']);
$boitage =		new boitage($db);
$porte =		new porte($db);
$notification =	new notification($db, $cookie, $config['SERVER']['url']);

// On transforme ces classes générales en variables globales
global $db, $noyau, $config, $core, $csv, $user, $fiche, $tache, $dossier, $historique, $fichier, $carto, $mission, $notification, $boitage, $porte, $link;

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

// On inclut les classes non chargées
include 'class/contact.class.php';
include 'class/evenement.class.php';
include 'class/folder.class.php';
include 'class/rappel.class.php';

?>