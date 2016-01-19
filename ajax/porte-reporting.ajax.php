<?php
/**
 * Reporting papier des portes d'une mission de porte à porte
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On recueille les informations
$electeurs = $_POST;

// On cherche les informations sur la mission
$parcours = $mission->chargement($_GET['mission']);
$afaire = explode(',', $parcours['a_faire']);
$fait = explode(',', $parcours['fait']);

// Pour chaque électeur, on va le déplacer dans la bonne case SQL
// Et on va mettre à jour son historique avec l'interaction
foreach ($electeurs as $electeur => $statut) {
    if ($statut > 0) {
        // On recherche la clé dans le tableau a faire
        $cle = array_search($electeur, $afaire);

        // On la supprime du tableau
        unset($afaire[$cle]);

        // On l'ajoute au tableau fait
        if (!in_array($electeur, $fait)) {
            $fait[] = $electeur;
        }

        // On créé l'élément d'historique
        if ($statut == 2) {
            $message = 'Électeur vu en porte-à-porte';
        } else {
            $message = 'Électeur absent';
        }

        // On enregistre l'élément d'historique
        $historique->ajout(
            $electeur,
            $_COOKIE['leqg-user'],
            'porte',
            date('d/m/Y'),
            'Domicile',
            $message,
            ''
        );
    }
}

// Une fois que tout est fini, on enregistre l'état de ce qui est à faire
// et on redirige
$afaire = implode(',', $afaire);
$fait = implode(',', $fait);

$query = 'UPDATE `missions`
          SET `mission_a_faire` = "' . $afaire . '",
              `mission_fait` = "' . $fait . '"
          WHERE `mission_id` = ' . $parcours['id'];
$db->query($query);
$core->tpl_go_to(
    'porte',
    array('action' => 'mission', 'mission' => $parcours['id']),
    true
);
