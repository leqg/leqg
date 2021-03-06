<?php
/**
 * Installation de la liste des cantons d'après un CSV
 *
 * PHP version 5
 *
 * @category Installation
 * @package  Installation
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$data = $csv->lectureFichier('data/cantons.csv');

// On initialise le tableau des clés
$keys = array();

$row = 0;
foreach ($data as $line) {
    // S'il s'agit de la première ligne, on récupère les informations
    if ($row == 0) {
        // On fait la boucle des entrées pour récupérer les clés
        foreach ($line as $key) {
            $keys[] = $key;
        }
    } else {
        // Sinon, on enregistre les informations dans la base de données
        // On prépare le tableau des informations
        $information = array();

        // On reformate les clés
        foreach ($line as $key => $val) {
            $information[$keys[$key]] = $val;
        }

        // On calcule le numéro identifiant de l'arrondissement
        $id = $information['DEP'].$information['CANTON'];

        // On détermine le nom de l'arrondissement
        if (!empty($information['ARTMIN'])) {
            $nom = str_replace('(', '', $information['ARTMIN']);
            $nom = str_replace(')', '', $nom);
            if ($information['TNCC'] != 5) {
                $nom .= ' ';
            }
            $nom .= $information['NCCENR'];
        } else {
            $nom = $information['NCCENR'];
        }

        // On prépare l'enregistrement des informations dans la base de données
        $query = 'INSERT INTO `cantons` (`canton_id`,
                                         `arrondissement_id`,
                                         `canton_numero`,
                                         `canton_nom`,
                                         `canton_chef_lieu`)
                  VALUES (' . $id . ',
                          ' . $information['DEP'] . $information['AR'] . ',
                          ' . $information['CANTON'] . ',
                         "' . htmlentities($nom) . '",
                          ' . $information['CHEFLIEU'] . ')';

        // On enregistre l'information dans la base de données et on redirige vers
        // les informations principales
        $db->query($query);
    }
    // On fini par incrémenter le numéro de la ligne
    $row++;
}

echo 'Fini !';
