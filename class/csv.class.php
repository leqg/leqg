<?php
/**
 * La classe csv regroupe les méthodes de traitement des fichiers CSV
 * 
 * @package   leQG
 * @author    Damien Senger <mail@damiensenger.me>
 * @copyright 2014 MSG SAS – LeQG
 */

class Csv
{
    
    /**
     * Cette méthode permet la lecture et le traitement d'un fichier CSV
     * 
     * Cette méthode permet d'ouvrir un fichier CSV, de récupérer l'ensemble de 
     * son contenu et de le retourner sous forme d'un tableau PHP.
     * 
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param  string $fichier    URL du fichier CSV à traiter
     * @param  string $separateur Type de séparateur utilisé dans le fichier CSV
     * @return array                Tableau contenant le contenu du fichier CSV
     */
     
    public static function lectureFichier( $fichier , $separateur = ';' ) 
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
        while ($line = fgetcsv($file, $size, $separateur)) :
        
            // On affecte les données de la ligne au tableau général
            $data[$row] = $line;
            $row++;
        
        endwhile;
        
        // On ferme le fichier
        fclose($file);
        
        // On retourne le tableau contenant les données du fichier
        return $data;
    }
}