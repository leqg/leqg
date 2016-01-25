<?php
/**
 * LeQG Mobile purge file
 *
 * PHP version 5
 *
 * @category Mobile
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


/*
 * Fichier de purge des données en fin de page (MySQL, etc.)
 */

/* On ferme les connexions au serveur SQL */
$db->close();
$noyau->close();
