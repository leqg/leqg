<?php
/**
 * LeQG Mobile include system
 *
 * PHP version 5
 *
 * @category Mobile
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On détermine les problématiques de langage des données PHP
setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR', 'fr');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

// On détermine le charset du fichier retourné par le serveur
header('Content-Type: text/html; charset=utf-8');

/**
 * LeQG Mobile configuration class
 *
 * PHP version 5
 *
 * @category Mobile
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Configuration
{
    /**
     * Configuration data array
     * @var array
     */
    static $confArray = [];

    /**
     * Read a configuration data
     * @param  string $name Data name
     * @return mixed
     */
    public static function read($name)
    {
        return self::$confArray[$name];
    }

    /**
     * Write a configuration data
     * @param  string $name  Data name
     * @param  mixed  $value Data value
     * @return void
     */
    public static function write($name, $value)
    {
        self::$confArray[$name] = $value;
    }
}

// On récupère le fichier de configuration
$config = parse_ini_file('../config.ini', true);

// On applique la configuration chargée
Configuration::write('db.host', $config['BDD']['host']);
Configuration::write('db.basename', 'leqg');
Configuration::write('db.user', $config['BDD']['user']);
Configuration::write('db.pass', $config['BDD']['pass']);
Configuration::write('ini', $config);

// On fabrique la classe $noyau de connexion au noyau central
$host = '217.70.189.234';
$port = 3306;
$dbname = 'leqg-core';
$user = 'leqg-remote';
$pass = 'pbNND3JY2cfrDUuZ';
$charset = 'utf8';
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$core = new PDO($dsn, $user, $pass);

// On fabrique la classe $link de liaison PDO
$dsn = 'mysql:host=' . Configuration::read('db.host') .
       ';dbname=' . Configuration::read('db.basename') .
       ';charset=utf8';
$link = new PDO(
    $dsn,
    Configuration::read('db.user'),
    Configuration::read('db.pass')
);

// On enregistre les liaisons SQL
Configuration::write('db.core', $core);
Configuration::write('db.link', $link);

// On charge les API extérieures
require_once '../api/esendex/autoload.php';
require_once '../api/phpmailer/class.phpmailer.php';

// On configure les données des API extérieures
$api['sms']['auth'] = new \Esendex\Authentication\LoginAuthentication(
    $config['SMS']['compte'],
    $config['SMS']['login'],
    $config['SMS']['pass']
);

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
require_once '../class/boite.class.php';
require_once '../class/campagne.class.php';
require_once '../class/carto.class.php';
require_once '../class/contact.class.php';
require_once '../class/core.class.php';
require_once '../class/csv.class.php';
require_once '../class/dossier.class.php';
require_once '../class/evenement.class.php';
require_once '../class/map.class.php';
require_once '../class/mission.class.php';
require_once '../class/porte.class.php';
require_once '../class/rappel.class.php';
require_once '../class/user.class.php';
