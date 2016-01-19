<?php
/**
 * Installation de la liste des régions d'après un CSV
 *
 * PHP version 5
 *
 * @category Installation
 * @package  Installation
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$data = $csv->lectureFichier('data/regions.csv');

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
        $region = array();

        // On reformate les clés
        foreach ($line as $key => $val) {
            $region[$keys[$key]] = $val;
        }

        // On prépare l'enregistrement des informations dans la base de données
        $query = 'INSERT INTO `regions` (`region_id`,
                                         `region_nom`,
                                         `region_chef_lieu`)
                  VALUES (' . $region['REGION'] . ',
                         "' . htmlentities($region['NCCENR']) . '",
                          ' . $region['CHEFLIEU'] . ')';

        // On enregistre l'information dans la base de données et on redirige vers
        // les informations principales
        $db->query($query);
    }

    // On fini par incrémenter le numéro de la ligne
    $row++;
}

echo 'Fini !';
