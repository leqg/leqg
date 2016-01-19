<?php
/**
 * Mise en place d'un nouveau numéro de téléphone pour un utilisateur
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$user = $_POST['user'];

$valeur = $_POST['valeur'];

// Si le champ correspond au téléphone ou au mobile,
// on supprime les espaces qui ne servent à rien
$valeur = preg_replace('`[^0-9]`', '', $valeur);

// On met à jour le contenu dans la base de données
$query = 'UPDATE `users`
          SET `user_phone` = "' . $valeur . '"
          WHERE `user_id` = ' . $user;
$noyau->query($query);

return true;
