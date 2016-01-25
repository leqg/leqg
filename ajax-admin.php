<?php
/**
 * AJAX admin script call file
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On intègre les fonctions essentielles et l'appel aux classes LeQG
require_once 'includes.php';


// On initialise le tableau listant les scripts AJAX créés
$scripts = array();

// On tente d'ouvrir le dossier AJAX pour connaître
// le contenu des appels AJAX créés
if ($dossier = opendir('./ajax/admin')) {
    // On vérifie que l'ouverture et la lecture du dossier
    // n'a pas retourné d'erreur
    while (false !== ($file = readdir($dossier))) {
        // On analyse le nom du fichier
        $file = explode('.', $file);
        // On vérifie que le fichier est bien un script .ajax.php
        if ($file[1] == 'ajax' && $file[2] == 'php') {
            // Si oui, on rajoute le script à la liste des scripts
            $scripts[] = $file[0];
        }
    }
}

// On fait la liste des différents scripts pouvant être appelés ci-après
// On vérifie que le script demandé existe
$script = $core->securisationString($_GET['script']);
if (!in_array($script, $scripts)) {
    // Si le script demandé n'existe pas, on arrête l'exécution de la page ici.
    exit;
}

// On lance l'appel des différents scripts
require_once 'ajax/admin/' . $script . '.ajax.php';
