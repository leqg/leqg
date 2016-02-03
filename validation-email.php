<?php
/**
 * Script de validation par email des inscriptions
 *
 * PHP version 5
 *
 * @category Users
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On commence par charger les includes nécessaires au système
require_once 'includes.php';

// On récupère le hash demandé
$hash = $_GET['email'];

// On regarde dans la base de données si un compte correspond
$query = 'SELECT *
          FROM `users`
          WHERE `user_new_email_hash` = "' . $hash . '"';
$sql = $noyau->query($query);

if ($sql->num_rows == 1) {
    // On modifie dans la base de données l'email demandé et en profite
    // pour réinitialiser les connexions au compte en question
    $query = 'UPDATE `users`
              SET `user_reinit` = NOW(),
                  `user_email` = `user_new_email`,
                  `user_new_email` = NULL,
                  `user_new_email_hash` = NULL
              WHERE `user_new_email_hash` = "' . $hash . '"';
    $core->goPage(true);

    if ($noyau->query($query)) {

    } else {
        $core->goPage(true);
    }
} else {
    $core->goPage(true);
}
