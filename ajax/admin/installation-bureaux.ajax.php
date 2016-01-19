<?php
/**
 * Installation de la liste des bureaux de votes d'après un CSV
 *
 * PHP version 5
 *
 * @category Installation
 * @package  Installation
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

$data = $csv->lectureFichier('data/bureaux.csv');

// On initialise le tableau des clés
$keys = array();

foreach ($data as $ligne => $line) {
    // S'il s'agit de la première ligne, on récupère les informations
    if ($ligne == 0) {
        // On fait la boucle des entrées pour récupérer les clés
        foreach ($line as $key) {
            $keys[] = $key;
        }
        echo '<pre>'; print_r($keys); echo '</pre>';
    } else {
        // Sinon, on enregistre les informations dans la base de données
        echo '<pre>'; print_r($line); echo '</pre>';
        // On commence par rechercher la ville correspondante à l'enregistrement
        $infos['ville'] = 67482;
        // On regarde si le canton existe déjà dans les informations issues de la
        // liste électorale
        $query = 'SELECT bureau_id FROM bureaux WHERE bureau_numero = ' . $line[4];
        $query .= ' AND commune_id = ' . $infos['ville'];
        $sql = $db->query($query);
        // Si le bureau de vote existe, on le met à jour
        if ($sql->num_rows == 1) {
            $infos['bureau'] = $db->query($query)->fetch_array()[0];
            // On met alors à jour les informations concernant ce bureau de vote
            $db->query(
                'UPDATE bureaux
                 SET    bureau_nom = "' . $line[5] . '",
                        bureau_adresse = "' . $line[6] . '",
                        bureau_cp = "' . $line[7] . '"
                 WHERE  bureau_id = ' . $infos['bureau']
            );
        } else {
            // On créé le bureau de vote
            $db->query(
                'INSERT INTO bureaux (`commune_id`,
                                      `bureau_numero`,
                                      `bureau_nom`,
                                      `bureau_adresse`,
                                      `bureau_cp`)
                 VALUES ( ' . $infos['ville'] . ',
                          ' . $line[4] . ',
                         "' . $line[5] . '",
                         "' . $line[6] . '",
                         "' . $line[7] . '")'
            );
        }
    }
}

echo 'Fini !';
