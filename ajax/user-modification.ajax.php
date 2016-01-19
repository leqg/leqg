<?php
/**
 * Modification des données d'un utilisateur
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


header('Content-Type: application/json');

// On récupère les informations du formulaire
$infos = $_POST;

// On les retraite pour la base de données
$firstname = $core->securisation_string($infos['firstname']);
$lastname = $core->securisation_string($infos['lastname']);
$email = $core->securisation_string($infos['email']);
$auth = $infos['auth'];
$id = $infos['compte'];

// On les enregistre dans la base de données
$query = 'UPDATE `users`
          SET `user_firstname` = "' . $firstname . '",
              `user_lastname` = "' . $lastname . '",
              `user_email` = "' . $email . '",
              `user_auth` = ' . $auth . '
          WHERE `user_id` = ' . $id;
$noyau->query($query);

$infos['auth'] = $user->status($infos['auth'], true);

echo json_encode($infos);
