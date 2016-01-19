<?php
/**
 * Création d'une nouvelle campagne d'envoi SMS ou Mail
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On vérifie la bonne réception des données
if (isset($_POST['objet'], $_POST['message'], $_POST['type'])) {
    // On récupère les données
    $objet = $_POST['objet'];
    $message = $_POST['message'];
    $type = $_POST['type'];
    $user = User::ID();

    // On lance la création de l'envoi
    $link = Configuration::read('db.link');
    $query = 'INSERT INTO `envois` (`compte_id`,
                                    `envoi_type`,
                                    `envoi_time`,
                                    `envoi_titre`,
                                    `envoi_texte`)
              VALUES (:compte,
                      :type,
                      NOW(),
                      :titre,
                      :texte)';
    $query = $link->prepare($query);
    $query->bindValue(':compte', $user, PDO::PARAM_INT);
    $query->bindValue(':type', $type);
    $query->bindValue(':titre', $objet);
    $query->bindValue(':texte', $message);
    $query->execute();
} else {
    http_response_code(403);
}
