<?php
    
    // On importe les données de configuration
    require_once('includes.php');


    // On exécute la requête de récupération des cents premières personnes à importer
    $query = $link->query('SELECT * FROM `imports` WHERE `import_statut` = 0 ORDER BY `import_id` ASC LIMIT 0, 500');
    $query->execute();
    
    
    // On récupère les informations sur ces fiches
    $fiches = $query->fetchAll(PDO::FETCH_ASSOC);
    
    
    // On fait la boucle des fiches pour les ajouter à la base de données
    foreach ($fiches as $fiche) {
        // On retraite les noms d'usages et noms, pour éviter d'entrer deux fois les mêmes informations
        if ($fiche['import_nom_usage'] == $fiche['import_nom']) {
            $fiche['import_nom_usage'] = '';
        }
        
        
        // On retraite la date de naissance
        $date = DateTime::createFromFormat('m/d/Y', $fiche['import_naissance_date']);
        $fiche['import_naissance_date'] = $date->format('Y-m-d');
        
        
        // On retraite les numéros de téléphones et mails dans la section `coordonnées`
        if (!empty($fiche['import_tel1'])) {
            // On retraite le numéro de téléphone
            $fiche['coordonnees'][] = preg_replace('`[^0-9]`', '', $fiche['import_tel1']);
        }
        
        if (!empty($fiche['import_tel2'])) {
            // On retraite le numéro de téléphone
            $fiche['coordonnees'][] = preg_replace('`[^0-9]`', '', $fiche['import_tel2']);
        }
        
        if (!empty($fiche['import_tel3'])) {
            // On retraite le numéro de téléphone
            $fiche['coordonnees'][] = preg_replace('`[^0-9]`', '', $fiche['import_tel3']);
        }
        
        if (!empty($fiche['import_mail'])) {
            // On retraite le numéro de téléphone
            $fiche['coordonnees'][] = $fiche['import_mail'];
        }
        
        
        // On formate l'adresse, notamment le nom de la rue
        $fiche['import_adresse_rue_format'] = '%'.preg_replace('#[^A-Za-z]#', '%', $fiche['import_adresse_rue']).'%';
        $fiche['import_adresse_commune_format'] = '%'.preg_replace('#[^A-Za-z]#', '%', $fiche['import_adresse_commune']).'%';
        
        
        // On essaye de récupérer l'identifiant de l'adresse où habite l'électeur
        $query = $link->prepare('SELECT `adresse_code` FROM `bano` WHERE `adresse_numero` = :numero AND `adresse_rue` LIKE :rue AND `adresse_code_postal` = :codepostal AND `adresse_commune` LIKE :commune');
        $query->bindParam(':numero', $fiche['import_adresse_numero']);
        $query->bindParam(':rue', $fiche['import_adresse_rue_format']);
        $query->bindParam(':codepostal', $fiche['import_adresse_code_postal']);
        $query->bindParam(':commune', $fiche['import_adresse_commune_format']);
        $query->execute();
  
        
        // On regarde si une réponse a été trouvée
        if ($query->rowCount()) {
            // On récupère les informations
            $adresse = $query->fetch(PDO::FETCH_ASSOC);
            $fiche['import_adresse_id'] = $adresse['adresse_code'];
        }
        
        
        // Sinon, on recherche dans la base de données Nominatim
        else {
            // On lance une requête AJAX Nominatim pour récupérer les latitudes et longitudes
            $url = 'http://nominatim.openstreetmap.org/search/?format=json&limit=1';
            $adresse_numero = urlencode($fiche['import_adresse_numero']);
            $adresse_rue = urlencode($fiche['import_adresse_rue']);
            $adresse_code_postal = urlencode($fiche['import_adresse_code_postal']);
            $adresse_commune = urlencode($fiche['import_adresse_commune']);
            $uri = $url . '&q=' . $adresse_numero . '+' . $adresse_rue . '+' . $adresse_code_postal . '+' . $adresse_commune;
            $json = file_get_contents($uri);
            $nominatim = json_decode( $json );
            $lat = ($nominatim) ? $nominatim[0]->lat : 0;
            $lon = ($nominatim) ? $nominatim[0]->lon : 0;
            
            // On rajoute l'adresse au sein de BANO
            $query = $link->prepare('INSERT INTO `bano` (`adresse_id`, `adresse_numero`, `adresse_rue`, `adresse_code_postal`, `adresse_commune`, `adresse_source`, `adresse_lat`, `adresse_lon`)
                                     VALUES             ("0",          :numero,          :rue,          :codepostal,           :commune,          "fichier",        :lat,          :lon)');
            
            // On retraite les informations pour les enregistrer
            $rue = mb_convert_case($fiche['import_adresse_rue'], MB_CASE_TITLE);
            $ville = mb_convert_case($fiche['import_adresse_commune'], MB_CASE_TITLE);
            
            // On met en place les données
            $query->bindParam(':numero', $fiche['import_adresse_numero']);
            $query->bindParam(':rue', $rue);
            $query->bindParam(':codepostal', $fiche['import_adresse_code_postal']);
            $query->bindParam(':commune', $ville);
            $query->bindParam(':lat', $lat);
            $query->bindParam(':lon', $lon);
            
            // On registre les informations
            $query->execute();
            
            // On récupère l'identifiant de l'adresse
            $fiche['import_adresse_id'] = $link->lastInsertId();
        }
        
        
        // On récupère le numéro identifiant de la ville de naissance si c'est en France
        if ($fiche['import_naissance_departement'] != 99) {
            $ville = '%' . preg_replace('#[^A-Za-z]#', '%', $fiche['import_naissance_commune']) . '%';
            $query = $link->prepare('SELECT * FROM `communes` WHERE `commune_nom_propre` LIKE :commune LIMIT 0, 1');
            $query->bindParam(':commune', $ville);
            $query->execute();
            
            if ($query->rowCount()) {
                $ville = $query->fetch(PDO::FETCH_ASSOC);
                $fiche['import_naissance_lieu'] = $ville['commune_id'];
            } else {
                $fiche['import_naissance_lieu'] = 99001;
            }
        }
        
        
        // Sinon, on défini la commune comme "Étranger" 
        else {
            $fiche['import_naissance_lieu'] = 99000;
        }
        
        
        // On retraite le statut d'électeur
        if ($fiche['import_electeur_statut'] == 'Fr') {
            $fiche['electeur'] = 1;
            $fiche['electeur_europeen'] = 0;
        } else {
            $fiche['electeur'] = 1;
            $fiche['electeur_europeen'] = 1;
        }
        
        
        // On regarde si le canton est déjà enregistré ou non
        $departement = $fiche['import_adresse_code_postal']{0} . $fiche['import_adresse_code_postal']{1};
        $query = $link->prepare('SELECT * FROM `bureaux` WHERE `bureau_id` = :bureau AND `canton_id` = :canton AND `circonscription_id` = :circonscription AND `departement_id` = :departement');
        $query->bindParam(':bureau', $fiche['import_bureau_numero']);
        $query->bindParam(':canton', $fiche['import_bureau_canton']);
        $query->bindParam(':circonscription', $fiche['import_bureau_circo']);
        $query->bindParam(':departement', $departement);
        $query->execute();
        
        // S'il est déjà enregistré, on récupère le numéro du bureau
        if ($query->rowCount()) {
            $bureau = $query->fetch(PDO::FETCH_ASSOC);
            $fiche['import_bureau_id'] = $bureau['bureau_code'];
        }
        
        // Sinon, on lance la création du bureau de vote
        else {
            $query = $link->prepare('INSERT INTO `bureaux` (`bureau_id`, `canton_id`, `circonscription_id`, `departement_id`, `bureau_nom`, `bureau_cp`)
                                     VALUES                (:bureau,     :canton,     :circonscription,     :departement,     :nom,         :codepostal)');
            $query->bindParam(':bureau', $fiche['import_bureau_numero']);
            $query->bindParam(':canton', $fiche['import_bureau_canton']);
            $query->bindParam(':circonscription', $fiche['import_bureau_circo']);
            $query->bindParam(':departement', $departement);
            $query->bindParam(':nom', $fiche['import_bureau_nom']);
            $query->bindParam(':codepostal', $fiche['import_adresse_code_postal']);
            $query->execute();
            
            $fiche['import_bureau_id'] = $link->lastInsertId();
        }
        
        
        // On lance la création de la fiche
        $query = $link->prepare('INSERT INTO `contacts` (`immeuble_id`, `bureau_id`, `contact_nom`, `contact_nom_usage`, `contact_prenoms`, `contact_naissance_date`, `contact_naissance_commune_id`, `contact_sexe`, `contact_electeur`, `contact_electeur_europeen`, `contact_electeur_numero`) 
                                 VALUES                 (:immeuble,     :bureau,     :nom,          :nomusage,           :prenoms,          :date,                    :lieu,                          :sexe,          :electeur,          :europe,                     :numero)');
        $query->bindParam(':immeuble', $fiche['import_adresse_id']);
        $query->bindParam(':bureau', $fiche['import_bureau_id']);
        $query->bindParam(':nom', $fiche['import_nom']);
        $query->bindParam(':nomusage', $fiche['import_nom_usage']);
        $query->bindParam(':prenoms', $fiche['import_prenoms']);
        $query->bindParam(':date', $fiche['import_naissance_date']);
        $query->bindParam(':lieu', $fiche['import_naissance_lieu']);
        $query->bindParam(':sexe', $fiche['import_sexe']);
        $query->bindParam(':electeur', $fiche['electeur']);
        $query->bindParam(':europe', $fiche['electeur_europeen']);
        $query->bindParam(':numero', $fiche['import_electeur_numero']);
        $query->execute();
        
        
        // On récupère l'identifiant de la fiche
        $contact = $link->lastInsertId();
        
        
        // Pour chaque coordonnées connues, on l'ajoute à la table en incrémentant la valeur
        if (array_key_exists('coordonnees', $fiche)) {
            foreach ($fiche['coordonnees'] as $coordonnee) {
                // On regarde s'il s'agit d'un email ou non 
                if (filter_var($coordonnee, FILTER_VALIDATE_EMAIL)) {
                    $type = 'email';
        			$query = $link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_email`) VALUES (:contact, :type, :coordonnees)');
                	$query->bindParam(':contact', $contact);
            		$query->bindParam(':type', $type);
            		$query->bindParam(':coordonnees', $coordonnee);
                    $query->execute();
                }
                
                else {
                    // On regarde s'il s'agit d'un mobile
                    $premiersNums = $coordonnee{0}.$coordonnee{1};
                    $query = $link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_numero`) VALUES (:contact, :type, :coordonnees)');
            		$query->bindParam(':contact', $contact);
            		$query->bindParam(':coordonnees', $coordonnee);
                    
                    if ($premiersNums == '06' || $premiersNums = '07') {
                        $type = 'mobile';
                    } else {
                        $type = 'fixe';
                    }
                    
            		$query->bindParam(':type', $type);
                    $query->execute();
               }
        		
        		// On incrémente le nombre de coordonnées dans la fiche pour le type correspondant
        		$query = $link->prepare('UPDATE `contacts` SET `contact_' . $type . '` = `contact_' . $type . '` + 1 WHERE `contact_id` = :id');
        		$query->bindParam(':id', $contact);
        		$query->execute();
            }
        }
        
        
        // Pour chaque vote, on l'enregistre la participation
        $query = $link->prepare('INSERT INTO `votes` (`contact_id`, `vote_election`, `vote_participation`) VALUES (:contact, "mun2008-1", :mun20081);
                                 INSERT INTO `votes` (`contact_id`, `vote_election`, `vote_participation`) VALUES (:contact, "mun2014-1", :mun20141);
                                 INSERT INTO `votes` (`contact_id`, `vote_election`, `vote_participation`) VALUES (:contact, "mun2014-2", :mun20142);
                                 INSERT INTO `votes` (`contact_id`, `vote_election`, `vote_participation`) VALUES (:contact, "eur2014", :eur2014);');
        $query->bindParam(':contact', $contact);
        $query->bindParam(':mun20081', $fiche['import_vote_2008']);
        $query->bindParam(':mun20141', $fiche['import_vote_mun2014_1']);
        $query->bindParam(':mun20142', $fiche['import_vote_mun2014_2']);
        $query->bindParam(':eur2014', $fiche['import_vote_eur2014']);
        $query->execute();
        
        
        // On termine en déterminant le statut comme fait
        $query = $link->prepare('UPDATE `imports` SET `import_statut` = 1 WHERE `import_id` = :import');
        $query->bindParam(':import', $fiche['import_id']);
        $query->execute();
    }
    
?>