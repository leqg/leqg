<?php
/*
	Fichier d'appel des différents modules, fonctions et classes du core du site
*/

// On détermine les problématiques de langage des données PHP
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR', 'fr');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

// On détermine le charset du fichier retourné par le serveur
header('Content-Type: text/html; charset=utf-8');

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

// On récupère le fichier de configuration
$config = parse_ini_file('config.ini', true);

// On applique la configuration chargée
Configuration::write('db.host', $config['BDD']['host']);
Configuration::write('db.basename', 'leqg');
Configuration::write('db.user', $config['BDD']['user']);
Configuration::write('db.pass', $config['BDD']['pass']);
Configuration::write('ini', $config);

// On fabrique la classe $noyau de connexion au noyau central
$host = $config['BDD']['host'];
$port = 3306;
$dbname = 'leqg_' . $config['LEQG']['compte'];
$user = $config['BDD']['user'];
$pass = $config['BDD']['pass'];
$charset = 'utf8';
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$core = new PDO($dsn, $user, $pass);

// On fabrique la classe $link de liaison PDO
$dsn = 'mysql:host=' . Configuration::read('db.host') . ';dbname=' . Configuration::read('db.basename') . ';charset=utf8';
$link = new PDO($dsn, Configuration::read('db.user'), Configuration::read('db.pass'));

// On enregistre les liaisons SQL
Configuration::write('db.core', $core);
Configuration::write('db.link', $link);

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

Configuration::write('api', $api);

// On inclut les classes non chargées
require_once 'class/boite.class.php';
require_once 'class/campagne.class.php';
require_once 'class/carto.class.php';
require_once 'class/contact.class.php';
require_once 'class/core.class.php';
require_once 'class/csv.class.php';
require_once 'class/dossier.class.php';
require_once 'class/evenement.class.php';
require_once 'class/map.class.php';
require_once 'class/mission.class.php';
require_once 'class/porte.class.php';
require_once 'class/rappel.class.php';
require_once 'class/user.class.php';

?>
