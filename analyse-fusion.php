<?php

    require_once 'includes.php';
    
    // On règle d'abord les paramètres
if (isset($_GET['fichier'])) { $file = $_GET['fichier']; 
} else { exit; 
}
    $tag = array('militant', 'parti');


    // On lance la lecture du fichier
    $data = $csv->lectureFichier('csv/' . $file . '.csv');
    $lignes = count($data);
    
    
    // On prépare un tableau des anomalies
    $anomalies = array();
    $afficher = array();
    
    // On lance l'analyse ligne par ligne
    foreach ($data as $key => $line) :
    
        if ($key >= 0) :
        
            $donnees = array('nom'        => trim($line[3]),
              'prenom'    => trim($line[4]),
              'fixe'        => trim($line[7]),
              'mobile'    => trim($line[9]),
              'email'    => trim($line[8]),
              'adresse'    => trim($line[10]),
              'numero'    => null,
              'rue'        => null,
              'cp'        => trim($line[11]),
              'ville'    => trim($line[12]));

            // On retraite les numéros de téléphone pour retirer tout ce qui n'est pas des chiffres
            $donnees['fixe'] = preg_replace('`[^0-9]`', '', $donnees['fixe']);
            $donnees['mobile'] = preg_replace('`[^0-9]`', '', $donnees['mobile']);
            
            // On recherche les informations sur la fiche concernée
            $query = 'SELECT * FROM `contacts` WHERE (`contact_nom` LIKE "%' . $core->formatageRecherche($donnees['nom']) . '%" OR `contact_nom_usage` LIKE "%' . $core->formatageRecherche($donnees['nom']) . '%") AND `contact_prenoms` LIKE "%' . $core->formatageRecherche($donnees['prenom']) . '%" AND `contact_tags` LIKE "%' . implode(',', $tag) . '%"';
            $sql = $db->query($query);
            
            if ($sql->num_rows == 1) :
                
                // On récupère les informations de la fiche
                $row = $sql->fetch_assoc();
                
                if ($row['contact_mobile'] == '0000000000') { $row['contact_mobile'] = ''; 
                }
                if ($row['contact_telephone'] == '0000000000') { $row['contact_telephone'] = ''; 
                }
                
                // On regarde si des informations diffères et on créé un rapport
                if ($row['contact_mobile'] != $donnees['mobile']) { $anomalies[] = array($row['contact_id'], 'mobile', trim($line[9])); 
                }
                if ($row['contact_telephone'] != $donnees['fixe']) { $anomalies[] = array($row['contact_id'], 'fixe', trim($line[7])); 
                }
                if ($row['contact_email'] != $donnees['email']) { $anomalies[] = array($row['contact_id'], 'email', trim($line[8])); 
                }
                
            endif;
        
        else :
        
            break;
            
        endif;
    
    endforeach;
    
    // On prend toutes les anomalies, et on les rentre dans la base
    foreach ($anomalies as $anomalie) :
    
        $query = 'INSERT INTO `fusion_erreurs` (`contact_id`, `fusion_erreur_case`, `fusion_erreur_entree`) VALUES (' . $anomalie[0] . ', "' . $anomalie[1] . '", "' . $anomalie[2] . '")';
        $db->query($query);
    
    endforeach;
    
    // On supprime les numéros de téléphone qui correspondent à 00 00 00 00 00
    $db->query('UPDATE `contacts` SET `contact_mobile` = NULL WHERE `contact_mobile` = 0000000000');
    $db->query('UPDATE `contacts` SET `contact_telephone` = NULL WHERE `contact_telephone` = 0000000000');
    
    $core->debug($anomalies);
?>