<?php
/**
 * Estimation du nombre de contacts à rappeler
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère les informations envoyées
if (isset($_POST['age'], $_POST['bureaux'], $_POST['thema'])) {
    // On fabrique un tableau d'arguments
    $args = array(
        'age' => $_POST['age'],
        'bureaux' => $_POST['bureaux'],
        'thema' => $_POST['thema']
    );

    // On récupère l'estimation
    $estimation = Rappel::estimation($args);

    // On retourne cette estimation
    echo $estimation;
}
