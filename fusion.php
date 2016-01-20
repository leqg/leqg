<?php

    require_once 'includes.php';
    
    // On règle d'abord les paramètres
if (isset($_GET['fichier'])) { $file = $_GET['fichier']; 
} else { exit; 
}
    $tag = array('parti', 'militant', 'parti2014');


    // On lance la lecture du fichier
    $data = $csv->lectureFichier('csv/' . $file . '.csv', ',');
    $lignes = count($data);
    
    
    // On prépare un tableau des anomalies
    $anomalies = array();
    $afficher = array();
    Core::debug($data[0], false);
    // On lance l'analyse ligne par ligne
    foreach ($data as $key => $line) :
    
        // On ne lance l'analyse que s'il ne s'agit pas de la première ligne d'entête
        if ($key > 0) :
        
            $donnees = array('nom'        => trim($line[0]),
              'prenom'    => trim($line[1]),
              'fixe'        => trim($line[5]),
              'mobile'    => trim($line[6]),
              'telephone'=> null,
              'email'    => trim($line[4]),
              'adresse'    => trim($line[13]),
              'numero'    => null,
              'rue'        => null,
              'cp'        => null,
              'ville'    => null,
              'organisme'=> null,
              'function' => null,
              'tagsplus' => array($line[2],$line[3]));
                        
                             
            // On commence par retraiter l'adresse
            if (!is_null($donnees['adresse'])) {
                $rue = explode(' ', $donnees['adresse'], 3);
                $bis = array('a', 'b', 'c', 'bis', 'ter');
                if (in_array(strtolower($rue[1]), $bis)) {
                    $donnees['numero'] = $rue[0].$rue[1];
                    $donnees['rue'] = $rue[2];
                } elseif (isset($rue[2])) {
                    $donnees['numero'] = $rue[0];
                    $donnees['rue'] = $rue[1] . ' ' . $rue[2];
                } else {
                    $donnees['numero'] = $rue[0];
                    $donnees['rue'] = $rue[1];
                }
            }
                
            // On regarde s'il existe un code postal et une ville dans le nom de la rue existant
            if (preg_match("`(.*) ([0-9]{5}) (.*)`s", $donnees['rue'], $analyse)) {
                $donnees['rue'] = $analyse[1];
                $donnees['cp'] = $analyse[2];
                $donnees['ville'] = $analyse[3];
            }
            
            // On retraite les numéros de téléphone pour retirer tout ce qui n'est pas des chiffres
            $donnees['fixe'] = preg_replace('`[^0-9]`', '', $donnees['fixe']);
            $donnees['mobile'] = preg_replace('`[^0-9]`', '', $donnees['mobile']);
                
        
            // En cas de numéro global, on retraite pour détecter s'il s'agit d'un portable ou d'un fixe
            if (!is_null($donnees['telephone'])) {
                    
                // On commence par supprimer les espaces et tout ce qui n'est pas des chiffres
                $phone = preg_replace('`[^0-9]`', '', $donnees['telephone']);
                    
                // On regarde s'il commence ou non par un 0, et sinon on rajoute un 0
                $phone = str_split($phone);
                if ($phone[0] != 0) { array_unshift($phone, 0); 
                }
                    
                // On regarde s'il possède bien 10 chiffres, sinon on supprime le contenu de l'entrée téléphone ou on divise en deux
                if (count($phone) != 10) {
                    if (count($phone) == 20) {
                        $phone = array_chunk($phone, 10);
                        $phone1 = $phone[0];
                        $phone2 = $phone[1];
                            
                        if ($phone1[1] == 6 || $phone1[2] == 7) {
                            $donnees['mobile'] = implode('', $phone1);
                            $donnees['fixe'] = implode('', $phone2);
                            $donnees['telephone'] = null;
                        } else {
                            $donnees['mobile'] = implode('', $phone2);
                            $donnees['fixe'] = implode('', $phone1);
                            $donnees['telephone'] = null;
                        }
                    } else {
                        $donnees['telephone'] = null;
                    }
                }
                else {
                    // On regarde s'il s'agit d'un mobile ou d'un fixe
                    if ($phone[1] == 6 || $phone[2] == 7) {
                        $donnees['mobile'] = implode('', $phone);
                        $donnees['telephone'] = null;
                    } else {
                        $donnees['fixe'] = implode('', $phone);
                        $donnees['telephone'] = null;
                    }
                }
            }
            
            // On retraite juste le nom de la rue pour qu'il existe sans les abréviations standard
            if (is_null($donnees['adresse'])) {
                $rue = $donnees['rue'];
                $rue = str_replace('Chem ', 'Chemin ', $rue);
                $rue = str_replace('Pce ', 'Place ', $rue);
                $rue = str_replace('Plc ', 'Place ', $rue);
                $rue = str_replace('Pl ', 'Place ', $rue);
                $rue = str_replace('Pte ', 'petite ', $rue);
                $rue = str_replace('Rte ', 'Route ', $rue);
                $rue = str_replace('Imp ', 'Impasse ', $rue);
                $rue = str_replace('Rle ', 'Ruelle ', $rue);
                $rue = str_replace('Ave ', 'Avenue ', $rue);
                $rue = str_replace('Bd ', 'Boulevard ', $rue);
                $rue = str_replace('All ', 'Allée ', $rue);
                $rue = str_replace('Fbg ', 'Faubourg ', $rue);
                $rue = str_replace('Fb ', 'Faubourg ', $rue);
                    
                $donnees['rue'] = $rue;
            }
                
            // On commence d'abord par récupérer l'ID de la ville en question
            $query = 'SELECT * FROM communes WHERE commune_nom LIKE "' . $core->formatageRecherche($donnees['ville']) . '" LIMIT 0,1';
            $sql = $db->query($query); $row = $sql->fetch_assoc();
            $code['ville'] = $row['commune_id'];

                
            // On continu en vérifiant si la rue existe déjà
            $query = 'SELECT * FROM rues WHERE commune_id = ' . $code['ville'] . ' AND rue_nom LIKE "%' . $core->formatageRecherche($donnees['rue']) . '%" LIMIT 0,1';
            $sql = $db->query($query);
            
            // S'il existe déjà une rue dans la base de données, on récupère l'identifiant
            if ($sql->num_rows == 1) {
                $row = $sql->fetch_assoc();
                $code['rue'] = $row['rue_id'];
            } else {
                // On rajoute la rue dans la base de données
                $query = 'INSERT INTO rues (commune_id, rue_nom) VALUES (' . $code['ville'] . ', "' . $donnees['rue'] . '")';
                $db->query($query);
                $code['rue'] = $db->insert_id;
            }
        
        
            // On regarde ensuite si l'immeuble existe déjà dans cette rue
            $query = 'SELECT * FROM immeubles WHERE rue_id = ' . $code['rue'] . ' AND immeuble_numero LIKE "' . $donnees['numero'] . '" LIMIT 0,1';
            $sql = $db->query($query); $nb = $sql->num_rows;
                
            // S'il existe déjà un immeuble dans la base de données, on récupère l'identifiant
            if ($nb == 1) {
                $row = $sql->fetch_assoc();
                $code['immeuble'] = $row['immeuble_id'];
            } else {
                // On rajoute l'immeuble dans la base de données
                $query = 'INSERT INTO immeubles (bureau_id, rue_id, immeuble_numero) VALUES ("", ' . $code['rue'] . ', "' . $donnees['numero'] . '")';
                $db->query($query);
                $code['immeuble'] = $db->insert_id;
            }
                
            
            // On recherche maintenant une fiche similaire
            $query = 'SELECT contact_id, immeuble_id FROM contacts WHERE ( contact_nom LIKE "%' . $core->formatageRecherche($donnees['nom']) . '%" OR contact_nom_usage LIKE "' . $core->formatageRecherche($donnees['nom']) . '" ) AND contact_prenoms LIKE "' . $core->formatageRecherche($donnees['prenom']) . '%" ORDER BY contact_nom, contact_nom_usage, contact_prenoms ASC';
            $sql = $db->query($query);
                
            if ($sql->num_rows >= 1) :
                
                if ($sql->num_rows == 1) :
                    
                    $row = $sql->fetch_assoc();
                    $code['fiche'] = $row['contact_id'];
                        
             else :
                    
                    // On prépare un tableau pour voir quels sont les éléments qui répondent à la même discrimination
                    $discri = array();
                    
                    // On fait la boucle pour voir s'il existe des critères de discrimination communs
                    while ($row = $sql->fetch_assoc()) : if ($row['immeuble_id'] == $code['immeuble']) { $discri[] = $row['contact_id']; 
                    } 
                    endwhile;
                        
                    // On regarde s'il existe des fiches qui résistent à la discrimination
                    if (count($discri) > 0) :
                            
                        // S'il existe une fiche qui résiste, on récupère l'ID
                        if (count($discri) == 1) :
                            
                            $code['fiche'] = $row['contact_id'];
                            
                            // S'il existe plusieurs fiches, on note l'anomalie
                     else :
                                
                            // On cherche si dans la base il existe exactement le même prénom pour les fiches où la recherche donne trop de noms
                            $query = 'SELECT * FROM `contacts` WHERE ( `contact_nom` = "' . $core->formatageRecherche($donnees['nom']) . '" OR `contact_nom_usage`= "' . $core->formatageRecherche($donnees['nom']) . '" ) AND `contact_prenoms` LIKE "' . $core->formatageRecherche($donnees['prenom']) . ' %"';
                            $sql = $db->query($query);

                            // Si une fiche seule est trouvée
                            if ($sql->num_rows == 1) :
                                
                                $row = $sql->fetch_assoc();
                                $code['fiche'] = $row['contact_id'];
                                    
                      elseif ($sql->num_rows > 1) :
                                
                            $code['fiche'] = null;
                            $anomalies[$key] = array('multi-nom-strict', $donnees, $code);
                                    
                      else :
                                
                            $code['fiche'] = null;
                            $anomalies[$key] = array('multi-adresse', $donnees, $code);
                                
                      endif;
                                                            
                     endif;
                        
                        // Si aucune fiche ne correspond
              else :
                        
                    // On cherche si dans la base il existe exactement le même prénom pour les fiches où la recherche donne trop de noms
                    $query = 'SELECT * FROM `contacts` WHERE ( `contact_nom` = "' . $core->formatageRecherche($donnees['nom']) . '" OR `contact_nom_usage`= "' . $core->formatageRecherche($donnees['nom']) . '" ) AND ( `contact_prenoms` LIKE "' . $core->formatageRecherche($donnees['prenom']) . '" OR `contact_prenoms` LIKE "' . $core->formatageRecherche($donnees['prenom']) . ' %" )';
                    $sql = $db->query($query);

                    // Si une fiche seule est trouvée
                    if ($sql->num_rows == 1) :
                            
                        $row = $sql->fetch_assoc();
                        $code['fiche'] = $row['contact_id'];
                                
               elseif ($sql->num_rows > 1) :
                            
                    $code['fiche'] = null;
                    $anomalies[$key] = array('multi-nom-strict', $donnees, $code);
                                
               else :
                            
                    $code['fiche'] = null;
                    $anomalies[$key] = array('multi-nom', $donnees, $code);
                            
               endif;
                        
              endif;
                        
                // On vide la boucle des discriminés
                unset($discri);
                    
             endif;
                    
          else :
                
                // Aucune fiche
                $code['fiche'] = null;
                
          endif;
                
                
            // Une fois que l'on possède les informations sur la fiche à fusionner, on regarde ce qu'on peut faire
            if (!is_null($code['fiche'])) :
                
                // On recherche des informations sur la fiche du fichier électoral
                $query = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $code['fiche'];
                $sql = $db->query($query);
                $row = $sql->fetch_assoc();
                    
                // On va récupérer les tags et ajouter les tags demandés
                $tags = explode(',', $row['contact_tags']);
                foreach ($tag as $t) { $tags[] = $t; 
                }
                foreach ($donnees['tagsplus'] as $t) { $tags[] = $t; 
                }
                $tags = trim(implode(',', $tags), ',');
                    
                // On va préparer le fusion des données dans un tableau $modifs
                $modifs = array();
                if (preg_match('`^[0-9]{9,10}$`', $donnees['fixe'])) { $modifs[] = '`contact_telephone` = ' . $donnees['fixe']; 
                }
                if (preg_match('`^[0-9]{9,10}$`', $donnees['mobile'])) { $modifs[] = '`contact_mobile` = ' . $donnees['mobile']; 
                }
                if (!is_null($donnees['email']) && !empty($donnees['email'])) { $modifs[] = '`contact_email` = "' . $donnees['email'] . '"'; 
                }
                if (!is_null($code['immeuble']) && !empty($code['immeuble'])) { $modifs[] = '`adresse_id` = ' . $code['immeuble']; 
                }
                if (!is_null($donnees['organisme'])) { $modifs[] = '`contact_organisme` = "' . $donnees['organisme'] . '"'; 
                }
                if (!is_null($donnees['fonction'])) { $modifs[] = '`contact_fonction` = "' . $donnees['fonction'] . '"'; 
                }
                    
                // Dans tous les cas, on ajoute les tags
                $modifs[] = '`contact_tags` = "' . $tags . '"';
                    
                $condition = '';
                    
                foreach ($modifs as $key => $modif) :
                    
                    $condition.= ($key == 0) ? ' SET ' : ' , ';
                        
                    $condition.= $modif;
                    
                endforeach;
                    
                $query = 'UPDATE `contacts`' . $condition . ' WHERE `contact_id` = ' . $code['fiche'];
                $db->query($query);
                    
                unset($condition);
                unset($tags);
                
          else :
                
                if (!preg_match('`^[0-9]{9,10}$`', $donnees['fixe'])) { $donnees['fixe'] = ''; 
                }
                if (!preg_match('`^[0-9]{9,10}$`', $donnees['mobile'])) { $donnees['mobile'] = ''; 
                }

                // On fusionne les tags généraux et les tags spécifique
                $tags = array();
                foreach ($tag as $t) { $tags[] = $t; 
                }
                foreach ($donnees['tagsplus'] as $t) { $tags[] = $t; 
                }

                // Si aucune fiche n'existe, on créé cette fiche
                $informations = array('immeuble' => $code['immeuble'],
                                          'nom' => $core->securisationString($donnees['nom']),
                                          'nom-usage' => '',
                                          'prenoms' => $core->securisationString($donnees['prenom']),
                                          'sexe' => 'i',
                                          'date-naissance' => '',
                                          'mobile' => $donnees['mobile'],
                                          'telephone' => $donnees['fixe'],
                                          'email' => $donnees['email'],
                                          'tags' => trim(implode(',', $tags), ','),
                                          'organisme' => $donnees['organisme'],
                                          'fonction' => $donnees['fonction']);
                $id_fiche = $fiche->creerContact($informations);
    
                // Si la fiche existe dans le tableau des anomalies, on créé un rapport de doublon
                if (array_key_exists($key, $anomalies)) { $db->query('INSERT INTO `doublons` (`contact_id`) VALUES (' . $id_fiche . ')'); 
                }
                
          endif;
                
            $afficher[] = array_merge($donnees, $code);
        
        elseif ($key != 0) :
        
            break;
        
        endif;
    
    endforeach;
?>
<pre><?php print_r($afficher); ?></pre>

<a href="analyse-fusion.php?fichier=<?php echo $_GET['fichier']; ?>">On passe à l'analyse des données du fichier</a>