<?php

/*
 *	Fichier d'appel des scripts AJAX
 */

// On lance le système de statistique des temps de chargement
$loading['begin'] = microtime();

// On intègre les fonctions essentielles et l'appel aux classes LeQG
    require_once 'includes.php';


// On initialise le tableau listant les scripts AJAX créés
    $scripts = array();

    // On tente d'ouvrir le dossier AJAX pour connaître le contenu des appels AJAX créés
    if ($doss = opendir('./ajax')) :
    
        // On vérifie que l'ouverture et la lecture du dossier n'a pas retourné d'erreur
        while (false !== ($file = readdir($doss))) :
                    
            // On analyse le nom du fichier
            $file = explode('.', $file);
            
            // On vérifie que le fichier est bien un script .ajax.php
            if (isset($file[1], $file[2])) :
                if ($file[1] == 'ajax' && $file[2] == 'php') :
                
                    // Si oui, on rajoute le script à la liste des scripts
                    $scripts[] = $file[0];
                
                endif;
                
            endif;
        
        endwhile;
    
    endif;

    // On fait la liste des différents scripts pouvant être appelés ci-après

    // On vérifie que le script demandé existe
    $script = $core->securisation_string($_GET['script']);
    
    if (!in_array($script, $scripts)) { exit; // Si le script demandé n'existe pas, on arrête l'exécution de la page ici.
    }    
    
    // On lance l'appel des différents scripts

    require_once 'ajax/' . $script . '.ajax.php';
    
    

    // Une fois les templates chargés, on met en place la purge et on calcule le temps nécessaire au chargement de la page à des fins de statistique
    $loading['end'] = microtime();
    $loading['time'] = $loading['end'] - $loading['begin'];
    $loading['time-sql'] = number_format($loading['time'], 6, '.', '');

    // On prépare la requête d'analyse du temps de chargement
    $page = (isset($_GET['page'])) ? $_GET['page'] : 'index';
    $query = 'INSERT INTO	`chargements` (`user_id`,
									   `chargement_page`,
									   `chargement_plateforme`,
									   `chargement_temps`)
							VALUES	  (' . $_COOKIE['leqg-user'] . ',
									   "' . $page . '",
									   "mobile",
									   "' . $loading['time-sql'] . '")';

    // On exécute la requête d'enregistrement du temps de chargement
    $noyau->query($query);

    // On lance la purge
    
    require_once 'purge.php';

?>