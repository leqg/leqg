<?php
/**
 * Suppression d'un numéro à appeler dans une mission de phoning
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

if (isset($_POST['mission'])) {
    // On ouvre la fiche concernée
    $link = Configuration::read('db.link');

    // On effectue la modification
    $query = $link->prepare(
        'UPDATE `argumentaires`
         SET `argumentaire_statut` = 0
         WHERE `argumentaire_id` = :id'
    );
    $query->bindParam(':id', $_POST['mission'], PDO::PARAM_INT);
    $query->execute();

    // On supprime tous les rappels non fait de cette mission
    $query = $link->prepare(
        'DELETE FROM `rappels`
         WHERE `rappel_statut` = 0
         AND `argumentaire_id` = :id'
    );
    $query->bindParam(':id', $_POST['mission'], PDO::PARAM_INT);
    $query->execute();

    // On redirige vers les dossiers
    Core::tpl_go_to('rappels', true);
}
