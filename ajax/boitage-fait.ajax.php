<?php
/**
 * Validation d'une mission entière de boîtage
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$parcours = $mission->chargement($_GET['mission']);
$immeuble = $_GET['immeuble'];

$afaire = explode(',', $parcours['a_faire']);
$fait = explode(',', $parcours['fait']);

if (in_array($immeuble, $afaire)) {
    $cle = array_search($immeuble, $afaire);
    unset($afaire[$cle]);

    if (!in_array($immeuble, $fait)) {
        $fait[] = $immeuble;
    }

    $afaire = implode(',', $afaire);
    $fait = implode(',', $fait);

    $query = 'UPDATE `missions`
              SET    `mission_a_faire` = "' . $afaire . '",
                     `mission_fait` = "' . $fait . '"
              WHERE  `mission_id` = ' . $parcours['id'];

    $db->query($query);

    // On informe toutes les fiches de l'immeuble qu'elles ont été boîtées
    $electeurs = $carto->listeElecteurs($immeuble);
    $message = 'Contact boîté';

    foreach ($electeurs as $electeur) {
        $historique->ajout(
            $electeur['id'],
            $_COOKIE['leqg-user'],
            'boite',
            date('d/m/Y'),
            'Domicile',
            $message,
            ''
        );
    }

    $core->tpl_go_to(
        'boite',
        array(
            'action' => 'mission',
            'mission' => $parcours['id']
        ), 
        true
    );
}
