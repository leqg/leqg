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
Configuration::write('db.basename', 'leqg');
Configuration::write('db.user', $config['BDD']['user']);
Configuration::write('db.pass', $config['BDD']['pass']);

// On fabrique la classe $noyau de connexion au noyau central
$host = '217.70.189.234';
$port = 3306;
$dbname = 'leqg-core';
$user = 'leqg-remote';
$pass = 'pbNND3JY2cfrDUuZ';
$charset = 'utf8';
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$noyau = new PDO($dsn, $user, $pass);

// On fabrique la classe $link de liaison PDO
$dsn = 'mysql:host=' . Configuration::read('db.host') . ';dbname=' . Configuration::read('db.basename') . ';charset=utf8';
$link = new PDO($dsn, Configuration::read('db.user'), Configuration::read('db.pass'));

// On enregistre les liaisons SQL
Configuration::write('db.core', $noyau);
Configuration::write('db.link', $link);

// Constructeur de classes
function __autoload($class_name) {
	include 'class/'.$class_name.'.class.php';
}

// Appel de la classe MySQL du compte
$db = new mysqli($config['BDD']['host'], $config['BDD']['user'], $config['BDD']['pass'], 'leqg');

// Temporaire, à des fins de comptabilité
$cookie = $_COOKIE['leqg'];

// On appelle l'ensemble des classes générales au site
$core =			new core($db, $noyau, $config['SERVER']['url']);
$csv =			new csv();
$user =			new user($db, $noyau, $config['SERVER']['url']);
$fiche =		new fiche($db, $cookie, $config['SERVER']['url']);
$tache =		new tache($db, $cookie, $config['SERVER']['url']);
$historique =	new historique($db, $cookie, $config['SERVER']['url']);
$fichier =		new fichier($db, $cookie, $config['SERVER']['url']);
$carto =		new carto($db, $noyau, $config['SERVER']['url']);
$mission =		new mission($db, $cookie, $config['SERVER']['url']);
$boitage =		new boitage($db);
$porte =		new porte($db);
$notification =	new notification($db, $cookie, $config['SERVER']['url']);

// On transforme ces classes générales en variables globales
global $db, $noyau, $config, $core, $csv, $user, $fiche, $tache, $historique, $fichier, $carto, $mission, $notification, $boitage, $porte, $link;

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