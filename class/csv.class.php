<?php
/**
 * CSV file management
 *
 * PHP version 5
 *
 * @category CSV
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * CSV file management
 *
 * PHP version 5
 *
 * @category CSV
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Csv
{
    /**
     * Read a CSV file
     *
     * @param string $fichier    file name
     * @param string $separateur CSV fields separator
     *
     * @return array
     * @static
     */
    static public function lectureFichier(string $fichier, $separateur = ';')
    {
        // On défini les données de démarrage
        $row = 0;
        $line = array();
        $data = array();
        $head = array();

        // On ouvre le fichier, uniquement en lecture
        $file = fopen($fichier, 'r');

        // On calcule la taille du fichier
        $size = filesize($fichier) + 1;

        // On fait une boucle pour chaque ligne du fichier
        while ($line = fgetcsv($file, $size, $separateur)) {
            // On affecte les données de la ligne au tableau général
            $data[$row] = $line;
            $row++;
        }

        // On ferme le fichier
        fclose($file);

        // On retourne le tableau contenant les données du fichier
        return $data;
    }
}
