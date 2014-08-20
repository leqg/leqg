<?php

/*
	Classe du noyau central du système LeQG
*/


class fiche extends core {
	
// Définition des propriétés
	private $fiches; // tableau des informations disponibles à propos des fiches ouvertes
	public	$fiche_ouverte = null;
	
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte utilisant la classe
	private $url; // Domaine du serveur
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte, $url) {
		$this->db = $db;
		$this->compte = $compte;
		$this->url = $url;
	}
		
	
	// Méthodes liées au templating
	
	// On ouvre l'accès aux informations d'une fiche existante
	public	function acces($id, $ouverture = false) {
		$query = "SELECT * FROM contacts WHERE contact_id = " . $id;
		$sql = $this->db->query($query);
		
		// S'il existe une fiche, au moins
		if ($sql->num_rows >= 1) {
			$donnees = $sql->fetch_assoc();
			
			$this->fiches[$id] = $donnees;
		
			if ($ouverture == true) { // Si on ouvre la fiche de suite
				$this->fermeture();
				$this->fiche_ouverte = $donnees;
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	
	// Méthode d'ouverture d'une fiche
	public function ouverture($id) {
		// On commence par purger toute fiche ouverte
		unset($this->fiche_ouverte);
		
		// On regarde si la fiche n'a pas déjà été recherchée dans la base de données
		if ($this->fiches[$id]) { $this->fiche_ouverte = $this->fiches[$id]; }
		else { $this->acces($id, true); }
	}
	
	
	// Méthode de fermeture de la fiche ouverte
	public function fermeture() {
		//unset($this->fiche_ouverte);
		$this->fiche_ouverte = NULL;
	}
	
	
	// L'ensemble des méthodes liées à l'accès aux informations au sujet de la fiche ouverte
	public	function infos($colonne, $prefix = true) { if ($prefix) { echo utf8_encode($this->fiche_ouverte['contact_' . $colonne]); } else { echo utf8_encode($this->fiche_ouverte[$colonne]); } }
	public	function get_infos($colonne, $prefix = true) { if ($prefix) { return utf8_encode($this->fiche_ouverte['contact_' . $colonne]); } else { return utf8_encode($this->fiche_ouverte[$colonne]); } }
	
	
	// Méthode permettant de retourner l'ID de l'immeuble de l'électeur, très important pour l'accès aux fonctions cartographiques
	public	function get_immeuble() {
		return $this->fiche_ouverte['immeuble_id'];
	}
	
	// Méthode permettant de savoir si une information a été remplie
	public	function is_info($colonne) {
		return (!empty($this->get_infos($colonne)) && $this->get_infos($colonne) != 0) ? true : false;
	}
	
	
	// Méthode d'information autour des optout de la fiche ouverte
	public	function optout($methode = null) {
		if (empty($methode)) {
			if ($this->fiche_ouverte['contact_optout_global']) : return true; else : return false; endif;
		} else {
			if ($this->fiche_ouverte['contact_' . $methode . '_optout']) : return true; else : return false; endif;
		}
	}
	
	
	// the_ID() et get_the_ID() permettent de retourner l'ID de la fiche consultée indépendamment de la recherche de paramètre GET
	public	function get_the_ID() {
		if (is_null($this->fiche_ouverte)) : return false; else :
			return $this->get_infos('id');
		endif;
	}
	public	function the_ID() {
		if (is_null($this->fiche_ouverte)) : return false; else :
			echo $this->get_the_ID();
		endif;
	}
	
	
	// Méthode de calcul du rendu du nom de la fiche ouverte
	public	function affichage_nom($separateur = null, $return = false) {
		$nom = $this->get_infos('nom'); 
		$nom_usage = $this->get_infos('nom_usage');
		$prenoms = $this->get_infos('prenoms');
	
		if ($separateur) { $begin = '<' . $separateur . '>'; $end = '</' . $separateur . '>'; }
		else { $begin = null; $end = null; }
	
		if (!empty($nom)) { $affichage = $begin . mb_convert_case(html_entity_decode($nom, ENT_NOQUOTES, 'utf-8'), MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($nom_usage)) { $affichage .= ' ' . $begin . mb_convert_case(html_entity_decode($nom_usage, ENT_NOQUOTES, 'utf-8'), MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($prenoms)) { $affichage .= ' ' . $begin . mb_convert_case(html_entity_decode($prenoms, ENT_NOQUOTES, 'utf-8'), MB_CASE_TITLE, 'utf-8') . $end; }
		
		if ($return == false) : echo $affichage; else : return $affichage; endif;
		
		unset($affichage);
	}
	
	
	// Méthode de calcul de l'affiche des informations liées à la date de naissance
	public	function date_naissance($separateur='/', $return = false, $date = null) {
		// Si aucune date n'est fourni, on utilise celle de la fiche ouverte
		if (empty($date)) : $date = $this->get_infos('naissance_date'); endif;
		
		$time = strtotime($date);
		$date = strtolower(strftime('%d' . $separateur . '%m' . $separateur . '%Y', $time));

		if ($return) : return $date; else : echo $date; endif;
	}
	
	
	// Méthode de calcul de l'âge d'un individu en fonction de sa date de naissance
	public	function age($date = null, $return = false) {
		// Si la date n'a pas été entrée, on prend celle de la fiche active
		if (!$date) { $date = $this->get_infos('naissance_date'); }
		
		$time_naissance = strtotime($date);
		
		$arr1 = explode('/', date('d/m/Y', $time_naissance));
		$arr2 = explode('/', date('d/m/Y'));
			
	    if(($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[0] <= $arr2[0]))) { $age = $arr2[2] - $arr1[2]; }
	    else { $age = $arr2[2] - $arr1[2] - 1; }
	    
	    if ($return) : return $age; else : echo $age . '&nbsp;ans'; endif;
	}
	
	
	// Méthode permettant l'affichage du lieu de naissance s'il existe dans la base de données
	public	function lieu_de_naissance($before = null, $return = false) {
		// on récupère le département de naissance
		if ($this->get_infos('naissance_commune_id')) {
			$ville = $this->get_infos('naissance_commune_id');
			
			$sql = $this->db->query('SELECT commune_nom, departement_nom FROM communes LEFT JOIN departements ON departements.departement_id = communes.departement_id WHERE commune_id = ' . $ville);
			$row = $sql->fetch_array();
			$ville = $row[0]; 
			$departement = $row[1];
			
			if ($before) echo $before . ' ';
			
			echo utf8_encode($ville) . ' (' . utf8_encode($departement) . ')';
		} else {
			// S'il n'existe pas, on n'affiche rien
			return false;
		}
	}
	
	
	// Méthode de formatage des adresses
	public	function affichage_adresse($separateur = '<br>', $return = false) {
		$numero = $this->get_infos('adresse_numero');
		$adresse = mb_convert_case($this->get_infos('adresse_rue'), MB_CASE_LOWER, 'utf-8');
		$complement = mb_convert_case($this->get_infos('adresse_complement'), MB_CASE_LOWER, 'utf-8');
		$cp = $this->get_infos('adresse_cp');
		$ville = mb_convert_case($this->get_infos('adresse_ville'), MB_CASE_TITLE, 'utf-8');
		
		if (!empty($numero)) : $affichage = $numero . ' '; endif;
		if (!empty($adresse)) : $affichage .= $adresse . $separateur; endif;
		if (!empty($complement)) : $affichage .= $complement . $separateur; endif;
		if (!empty($cp)) : $affichage .= $cp . ' '; endif;
		if (!empty($ville)) : $affichage .= $ville; endif;

		// On remet en forme l'affichage
		$affichage = $this->tpl_transform_texte($affichage);
		
		if ($return) : return $affichage; else : echo $affichage; endif;
		
		return true;
	}
	
	
	// Rendu du canton
	public	function canton($return = false, $id = null) {
		// S'il n'est pas fourni un id, on utilise celui de la fiche ouverte
		if (!$id) { $id = $this->fiche_ouverte['canton_id']; }
		
		// On recherche des informations sur le canton en question
		$query = "SELECT * FROM cantons WHERE canton_id = " . $id;
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
		
		// On retourne le nom du canton
		if ($return) : return $row['canton_nom']; else : echo $row['canton_nom']; endif;
		
		return true;
	}
	
	
	// Rendu du bureau de vote
	public	function bureau($lien = null, $return = false, $id = null, $ville = null) {
		// S'il n'est pas fourni un id, on utilise celui de la fiche ouverte
		if (!$id) { $id = $this->fiche_ouverte['bureau_id']; }
		if (!$ville) { $ville = $this->fiche_ouverte['commune_id']; }
		
		// On recherche des informations sur le bureau en question
		$query = "SELECT * FROM bureaux WHERE bureau_numero = " . $id . " AND commune_id = " . $ville;
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
		
		// On retraite les informations
		$numero = $row['bureau_numero'];
		$nom = $row['bureau_nom'];

		// On remet en forme l'affichage
		$nom = $this->tpl_transform_texte($nom);
		
		// On regarde s'il faut mettre un lien vers la fiche du bureau de vote
		if ($lien) {
			$lien_open = '<a href="bureau.php?ville=' . $ville . '&id=' . $id . '">';
			$lien_close = '</a>';
		} else { $lien_open = ''; $lien_close = $lien_open; }
		
		// On retourne le résultat
		if ($return) : return $numero . ' ' . $nom; else : echo $lien_open . ' ' . $nom . $lien_close; endif;
	}
	
	
	// Rendu du sexe de la fiche ouverte
	public	function sexe($return = false) {
		// On récupère l'information
		$sexe = $this->get_infos('sexe');
		
		// on retourne l'information
		if ($sexe == 'M') { if ($return) : return 'Homme'; else : echo 'Homme'; endif; }
		else if ($sexe == 'F') { if ($return) : return 'Femme'; else : echo 'Femme'; endif; }
		else { if ($return) : return 'Inconnu'; else : echo 'Inconnu'; endif; }
		
		return true;
	}
	
	
	// Rendu des données de contact de la fiche ouverte ou demandée
	public	function contact($type, $lien = false, $return = false, $id = NULL) {
		// Si un ID est fourni, on cherche les infos, sinon on les prend dans la fiche ouverte
		if ($id) {
			$query = "SELECT contact_" . $type . " FROM contacts WHERE contact_id = " . $id;
			$sql = $this->db->query($query);
			$row = $sql->fetch_array();
			
			$contact = $row[0];
		} else {
			$contact = $this->get_infos($type);
		}
		
		// On regarde s'il existe un contenu
		if ($contact) : $exist = true; else : $exist = false; endif;
		
		// On prépare le lien si demandé
		if ($lien && $exist) {
			if ($type == 'email') { $affichage = '<a href="mailto:' . $contact . '">' . $contact . '</a>'; }
			else if ($type == 'twitter') { $affichage = '<a href="http://twitter.com/' . $contact . '">@' . $contact . '</a>'; }
			else { $affichage = '<a href="tel:+33' . substr($contact, 1) . '">' . $this->get_tpl_phone($contact) . '</a>'; }
		} else if (!$lien && $exist) {
			if ($type == 'twitter') { $affichage = '@' . $contact; } else { $affichage = $contact; } 
		} else {
			$affichage = NULL;
		}
		
		// On retourne l'information demandée
		if ($return && $affichage) : return $affichage; else : echo $affichage; endif;
	}
	
	
	// Méthode permettant l'affichage des tags relatifs à une fiche
	public	function tags($element = null, $return = false, $id = null) {
		if (empty($id)) {
			$id = $this->get_infos('id');
			$tags = $this->get_infos('tags');
		} else {
			$query = 'SELECT contact_tags FROM contacts WHERE contact_id = ' . $id;
			$sql = $this->db->query($query);
			$row = $sql->fetch_array();
			$tags = $row[0];
		}
		
		if (empty($tags)) {
			$affichage = '';
		} else {
			// On transforme l'information en tableau
			$tags = explode(',', $tags);
			
			// On prépare l'affichage
			$affichage = '';
			
			// On affiche le résultat
			foreach ($tags as $tag) {
				$affichage .= '<' . $element . ' class="tag">' . $tag . '</' . $element . '>'; 
			}
		}
		
		if ($return) : return $affichage; else : echo $affichage; endif;
	}
	
	
	// Méthode de mise à jour des informations
	public	function update_contact( $info , $valeur ) {
		if (!empty($this->fiche_ouverte['contact_id'])) {
			$query = 'UPDATE contacts SET contact_' . $info . ' = "' . $valeur . '" WHERE contact_id = ' . $this->fiche_ouverte['contact_id'];
			if ($this->db->query($query)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	// Méthode permettant de savoir combien de tâches sont affectées à une fiche
	public	function taches_liees($id = null) {
		if (is_null($id)) $id = $this->get_infos('id');
		
		$query = "SELECT * FROM taches WHERE tache_contacts LIKE '%" . $id . "%' AND tache_terminee = 0";
		$sql = $this->db->query($query);
		
		$nb = $sql->num_rows;
		
		if ($nb > 0) {
			// On initialise les tâches contenant notre fiche
			$taches = array();
		
			while ($row = $sql->fetch_assoc()) {
				// on décompose les fiches liées à cette tâche pour savoir s'il y a vraiment notre fiche
				$contacts = explode(',', trim($row['tache_contacts'], ','));
				
				if (in_array($id, $contacts)) {
					$taches[] = $row; 
				}
			}
			
			return $taches;
		} else {
			return false;
		}
	}
	
	
	// Méthode permettant d'extraire les dossiers liés à une fiche contact
	public	function dossiers_lies($id = null) {
		if (is_null($id)) $id = $this->get_infos('id');
		
		$query = "SELECT * FROM dossiers WHERE dossier_contacts LIKE '%" . $id . "%' ORDER BY dossier_statut DESC, dossier_date_ouverture DESC";
		$sql = $this->db->query($query);
		
		$nb = $sql->num_rows;
		
		if ($nb > 0 ) {
			// On initialise le traitement des dossiers concernant notre fiche
			$dossiers = array();
			
			while ($row = $sql->fetch_assoc()) {
				// On décompose les contacts liées à ce dossier pour savoir s'il y a vraiment notre fiche à l'intérieur
				$contacts = explode(',', trim($row['dossier_contacts'], ','));
				
				if (in_array($id, $contacts)) {
					$dossiers[] = $row;
				}
			}

			return $dossiers;
		} else {
			return false;
		}
	}
	
	
	// Méthode permettant l'ajout d'un dossier à la base de données du site
	public	function dossier_ajout($nom, $description, $fiches = null, $return = true) {
		// On retraite le tableau des fiches pour l'ajout à la base de données
		if (is_array($fiches)) {
			// Si les données sont envoyées sous le format d'un tableau, on implode les fiches en une variable sous le format CSV
			$fiches = implode(',', $fiches);
		}
		
		// On vérifie le format des données
		$nom = $this->securisation_string($nom); if (!is_string($nom)) return false;
		$description = $this->securisation_string($description); if (!is_string($description)) return false;
		$fiches = $this->securisation_string($fiches); if (!is_string($fiches)) return false;
		
		
		if (is_string($nom) && is_string($description) && is_string($fiches)) {
			// On prépare l'ajout à la base de données du dit fichier
			$query =   "INSERT INTO dossiers (	dossier_nom,
												dossier_description,
												dossier_contacts )
						VALUES				  (	'" . $nom . "',
												'" . $description . "',
												'" . $fiches . "' ) ";
												
			// On ajoute les données à la base de données
			$this->db->query($query);
			
			// On retourne l'ID du dossier ajouté pour information
			if ($return == true) {
				return $this->db->insert_id;
			} else {
				echo $this->db->insert_id;
			}
		}
	}
	
	
	// Méthode permettant d'extraire l'ensemble des informations sur le dossier
	public	function dossier($id) {
		// On retravaille l'ID fourni
		$id = $this->securisation_string($id);
		
		// On recherche dans la base de données les informations
		$query = 'SELECT * FROM dossiers WHERE dossier_id = ' . $id . ' LIMIT 0,1';
		$sql = $this->db->query($query);
		
		if ($sql->num_rows == 1) {
			$infos = $sql->fetch_assoc();
			
			// On transforme le nom des clés du tableau $infos pour retirer le préfixe BDD
			$infos = $this->formatage_donnees($infos);
			
			// On retourne les informations sous forme d'un tableau
			return $infos;
		} else {
			$this->tpl_redirection();
		}
	}
	
	
	// Méthode permettant la recherche des dossiers présents dans la base de données du site à partir de leur nom
	public	function dossier_recherche($recherche) {
		// On traite la recherche pour qu'elle soit sécurisée
		//$recherche = $this->securisation_string($recherche);
		
		// On recherche dans la base de données les titres similaire à notre recherche si notre recherche est supérieure à 3 caractères
		if (strlen($recherche) > 3) {
			// On prépare la recherche dans la base de données
			$query =  "	SELECT		dossier_id, dossier_nom
						FROM		dossiers
						WHERE		dossier_nom LIKE '%" . $recherche . "%'
						ORDER BY	dossier_nom ASC
						LIMIT		0, 10 ";
			
			// On effectue la requête
			$sql = $this->db->query($query);
			
			// On traite la requête pour récupérer sous forme d'un tableau les différents titres des dossiers recherchés
			$resultats = array();
			
			while ($row = $sql->fetch_assoc()) {
				$resultats[] = array( 'id' => $row['dossier_id'], 'nom' => $row['dossier_nom'] );
			}

			// On finit par retourner la valeur du tableau des résultats s'il y a eu des résultats
			if (count($resultats)) {
				return $resultats;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}
	
	
	// Récupération d'informations sans ouverture de fiches, grâce à l'ID
	
	public	function nomByID($id, $separateur = null, $return = false) {
		// On récupère les informations dans la base de données concernant la fiche de demandée
		$query = 'SELECT contact_nom, contact_nom_usage, contact_prenoms FROM contacts WHERE contact_id = "' . $id . '"';
		$sql = $this->db->query($query);
		$infos = $sql->fetch_assoc();
	
		$nom = $infos['contact_nom']; 
		$nom_usage = $infos['contact_nom_usage'];
		$prenoms = $infos['contact_prenoms'];
	
		if ($separateur) { $begin = '<' . $separateur . '>'; $end = '</' . $separateur . '>'; }
		else { $begin = null; $end = null; }
	
		if (!empty($nom)) { $affichage = $begin . mb_convert_case($nom, MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($nom_usage)) { $affichage .= ' ' . $begin . mb_convert_case($nom_usage, MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($prenoms)) { $affichage .= ' ' . $begin . mb_convert_case($prenoms, MB_CASE_TITLE, 'utf-8') . $end; }
		
		//if ($return == false) { echo $affichage; } else { return $affichage; }
		if ($return == false) { echo $affichage; } else { return $affichage; }
		
		unset($affichage);
	}
	
	
	// Méthode d'ajout d'une entrée à l'historique d'un utilisateur
	public	function historique_ajout($fiche, $type, $objet, $remarques = 'Entrée automatique du système') {
			$historique = array(	'fiche'			=> $fiche,
								'type'			=> $type,
								'date'			=> date('Y-m-d'),
								'objet'			=> $objet,
								'remarques'		=> $remarques );
			
			$this->db->query('INSERT INTO historique (contact_id,
												historique_type,	
												historique_date,
												historique_objet,
												historique_remarques)
									VALUES (		"' . $historique['fiche'] . '",
												"' . $historique['type'] . '",
												"' . $historique['date'] . '",
												"' . $historique['objet'] . '",
												"' . $historique['remarques'] . '" )');
	}
	
	
	// modificationAdresse ( int , array ) permet de modifier l'adresse d'une fiche utilisateur sélectionnée
	public	function modificationAdresse( $contact , $immeuble ) {
		// On vérifie le format des informations entrées
			if (!is_numeric($contact) && !is_array($immeuble)) { return false; }
				
		// On prépare la requête BDD
			$query = 'UPDATE		contacts
					  SET		immeuble_id = ' . $immeuble . '
					  WHERE		contact_id = ' . $contact;
			
		// On effectue la requête dans la BDD et on retourne le résultat
			$sql = $this->db->query($query);
			return $sql;
	}
	
	
	// recherche( string , string , string [, string] ) permet d'effectuer une recherche de fiches selon des critères donnés
	public	function recherche( $prenom , $nom , $nom_usage , $sexe = '%' ) {
		// Tout d'abord, on commence par retraiter le sexe entré
			if ($sexe == 'I') $sexe = '%';
		
		// On prépare les données entrées à être mise en place dans une recherche
			$prenom = $this->formatage_recherche($prenom);
			$nom_usage = $this->formatage_recherche($nom_usage);
			$nom = $this->formatage_recherche($nom);
		
		// On vérifie que si le champ est vide, on y met un joker
			if (empty($prenom)) $prenom = '%';
			if (empty($nom_usage)) $nom_usage = '%';
			if (empty($nom)) $nom = '%';
		
		// On prépare le tableau dans lequel les résultats seront affectés, et un tableau de vérification rapide
			$contacts = array();
			$ids = array();
		
		// On prépare la requête de recherche stricte sur les données noms et noms d'usage
			$query = 'SELECT		*
					  FROM		contacts
					  WHERE		contact_nom  LIKE "' . $nom . '"
					  AND		contact_nom_usage LIKE "' . $nom_usage . '"
					  AND		contact_prenoms LIKE "%' . $prenom . '%"
					  AND		( contact_sexe LIKE "' . $sexe . '" OR contact_sexe = "I" )
					  ORDER BY	contact_nom_usage ASC,
					  			contact_nom ASC,
					  			contact_prenoms ASC,
					  			contact_naissance_date DESC';
		
		// On effectue la recherche strict et on affecte les résultats au tableau contacts
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) :
				$contacts[] = $this->formatage_donnees($row);
				$ids[] = $row['contact_id'];
			endwhile;
		
		// On prépare maintenant la recherche permissive sur les données noms et noms d'usage
			$query = 'SELECT		*
					  FROM		contacts
					  WHERE		contact_nom  LIKE "%' . $nom . '%"
					  AND		contact_nom_usage LIKE "%' . $nom_usage . '%"
					  AND		contact_prenoms LIKE "%' . $prenom . '%"
					  AND		( contact_sexe LIKE "' . $sexe . '" OR contact_sexe = "I" )
					  ORDER BY	contact_nom_usage ASC,
					  			contact_nom ASC,
					  			contact_prenoms ASC,
					  			contact_naissance_date DESC
					  LIMIT		0, 25';
		
		// On effectue la recherche permissive et on affecte les résultats au tableau contacts
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) :
			
			// Avant d'ajouter dans le tableau des correspondances, on vérifie simplement que l'enregistrement n'y figure pas déjà
				if (!in_array($row['contact_id'], $ids)) $contacts[] = $this->formatage_donnees($row);
			
			endwhile;
		
		// On retourne le tableau des contacts trouvés
			return $contacts;
	}
	
	
	// creerContact( array ) permet de rajouter un contact dans la base de données
	public	function creerContact( $infos ) {
		// On vérifie que les informations entrées prennent bien la forme d'un tableau et qu'elles contiennent les infos minimales
		if (!is_array($infos) && !isset($infos['nom'], $infos['prenom'])) return false;
		
		// On formate les téléphones, au cas où
		if ($infos['mobile'] == '') { $infos['mobile'] == NULL; }
		if ($infos['telephone'] == '') { $infos['telephone'] == NULL; }
		
		// On prépare la requête de création de la fiche
		$query = 'INSERT INTO	contacts (immeuble_id,
										  contact_nom,
										  contact_nom_usage,
										  contact_prenoms,
										  contact_sexe,
										  contact_email,
										  contact_mobile,
										  contact_telephone,
										  contact_naissance_date)
				  VALUES (' . $infos['immeuble'] . ',
				  		  "' . $infos['nom'] . '",
				  		  "' . $infos['nom-usage'] . '",
				  		  "' . $infos['prenoms'] . '",
				  		  "' . $infos['sexe'] . '",
				  		  NULL,
				  		  NULL,
				  		  NULL,
				  		  "' . $infos['date-naissance'] . '")';
		
		// On exécute la requête au serveur
		$this->db->query($query);
		
		// On récupère l'id de l'entrée
		$id = $this->db->insert_id;
		
		// On fait les modifications dès qu'on a des informations
		if (!empty($infos['mobile'])) {
			$this->db->query('UPDATE contacts SET contact_mobile = "' .$infos['mobile']. '" WHERE contact_id = ' . $id);
		}
		if (!empty($infos['telephone'])) {
			$this->db->query('UPDATE contacts SET contact_telephone = "' .$infos['telephone']. '" WHERE contact_id = ' . $id);
		}
		if (!empty($infos['email'])) {
			$this->db->query('UPDATE contacts SET contact_email = "' .$infos['email']. '" WHERE contact_id = ' . $id);
		}
		
		// On renvoit l'id de l'entrée
		return $id; 
	}
	
	
	// renommerVille() permet de renommer une ville pour faire une recherche
	public	function renommerVille($ville_origine, $dept_origine) {
		// Pour éviter les problèmes d'apostrophes (dans la BDD souvent des espaces) des villes comme L'isle, on remplace par un joker
		$remplacement = array("'", " ", "OE");
		$ville_origine = str_replace($remplacement, '%', $ville_origine);
		
		// On évite certains caractères spéciaux
		$ville_origine = str_replace('œ', 'oe', $ville_origine);
		
		// Cas particulier de Paris / Marseille / Lyon, si ça commence par ces noms, on affiche juste la ville sans l'arrondissement
		if (preg_match('/^PARIS/', $ville_origine)) { $ville_origine = 'PARIS'; }
		if (preg_match('/^LYON/', $ville_origine)) { $ville_origine = 'LYON'; }
		if (preg_match('/^MARSEILLE/', $ville_origine)) { $ville_origine = 'MARSEILLE'; }
		
		// Cas particulier des DOM-TOM où la recherche doit porter sur 971 au lieu de 97
		if ($dept_origine == 97) { $dept_origine = '97%'; }
		
		// Cas particulier des villes mal orthographiées dans les bases de données
		if ($ville_origine == 'SAINT-DIE' && $dept_origine == 88) { $ville_origine = 'SAINT-DIE-DES-VOSGES'; }
		if ($ville_origine == 'CHERBOURG' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
		if ($ville_origine == 'OCTEVILLE' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
		if ($ville_origine == 'MEULAN' && $dept_origine == 78) { $ville_origine = 'MEULAN-EN-YVELINES'; }
		
		// Cas particulier de Chalons sur Marne qui a changé de nom pour Chalons en Champagne
		if ($ville_origine == 'CHALONS-SUR-MARNE' && $dept_origine == 51) { $ville_origine = 'CHALONS-EN-CHAMPAGNE'; }
		
		// On retourne les données corrigées
		$donnees = array($ville_origine, $dept_origine);
		return $donnees;
	}
	
	
	// export( array , bool ) permet d'effectuer un export de données en CSV depuis la base de données
	public	function export( $formulaire , $simulation = false ) {
		// On commence par vérifier le format des arguments
		if (!is_array($formulaire) || !is_bool($simulation)) return false;
		
		// On prépare la requête SQL
		$query = 'SELECT	*
				  FROM		contacts
				  LEFT JOIN	immeubles
				  ON		immeubles.immeuble_id = contacts.immeuble_id
				  LEFT JOIN	rues
				  ON		rues.rue_id = immeubles.rue_id
				  LEFT JOIN	communes
				  ON		communes.commune_id = rues.commune_id
				  LEFT JOIN	codes_postaux
				  ON		codes_postaux.commune_id = communes.commune_id';
		
		// tableau des critères initialisé
		$criteres = array();
		
		// On calcule les âges mini et maxi en date
		if ($formulaire['age-min'] > $formulaire['age-max']) { $formulaire['age'] = $formulaire['age-min']; $formulaire['age-min'] = $formulaire['age-max']; $formulaire['age-max'] = $formulaire['age']; unset($formulaire['age']); }
		
		$age_min = mktime(0, 0, 0, date('n'), date('j'), date('Y')-$formulaire['age-min']);
		$age_max = mktime(0, 0, 0, date('n'), date('j'), date('Y')-$formulaire['age-max']);
		
		if (!empty($formulaire['ville'])) $criteres[] = 'commune_id = ' . $formulaire['ville'];
		if (!empty($formulaire['rue'])) $criteres[] = 'rue_id = ' . $formulaire['rue'];
		if (!empty($formulaire['immeuble'])) $criteres[] = 'immeuble_id = ' . $formulaire['immeuble'];
		if (!empty($formulaire['electeur'])) $criteres[] = 'contact_electeur = ' . $formulaire['electeur'];
		if ($formulaire['sexe'] != 'i') $criteres[] = 'contact_sexe = "' . $formulaire['sexe'] . '"';
		if ($formulaire['sexe'] != 'i') $criteres[] = 'contact_sexe = "' . $formulaire['sexe'] . '"';
		if ($formulaire['age-min'] > 0) $criteres[] = 'contact_naissance_date <= "' . date('Y-m-d', $age_min) . '"';
		if ($formulaire['age-max'] > 0) $criteres[] = 'contact_naissance_date >= "' . date('Y-m-d', $age_max) . '"';
		if ($formulaire['email']) $criteres[] = 'contact_email IS NOT NULL AND contact_optout_email = 0';
		if ($formulaire['mobile']) $criteres[] = 'contact_mobile IS NOT NULL AND contact_optout_mobile = 0';
		if ($formulaire['fixe']) $criteres[] = 'contact_telephone IS NOT NULL AND contact_optout_telephone = 0';
		
		// On applique les critères à la requête SQL
		foreach ($criteres as $key => $critere) {
			if ($key == 0) { $query .= ' WHERE '; } else { $query .= ' AND '; }
			$query .= $critere;
		}
		
		// On applique un tri des contacts
		$query .= ' ORDER BY contact_nom, contact_nom_usage, contact_prenoms ASC';
		
		// On lance la requête
		$this->debug($query);
		$sql = $this->db->query($query);
		
		// Si c'est une simulation, on calcule le nombre de fiches et on retourne l'information
		if ($simulation) {
			return $sql->num_rows;
			
		// Sinon, on fait la requête de tous les utilisateurs pour fabriquer le fichier et on le créé
		} else {
			// On prépare le contenu du fichier sous forme de tableau
			$fichier = array();
			
			// On y entre la première ligne du fichier
			$fichier[] = array('nom',
							   'nom_usage',
							   'prenoms',
							   'date_naissance',
							   'adresse',
							   'cp',
							   'ville',
							   'sexe',
							   'email',
							   'mobile',
							   'fixe',
							   'electeur');
		
			while ($contact = $sql->fetch_assoc()) {
				// on rassemble les informations qu'on balance dans le fichier
				$fichier[] = array($contact['contact_nom'],
								   $contact['contact_nom_usage'],
								   $contact['contact_prenoms'],
								   date('d/m/Y', strtotime($contact['contact_naissance_date'])),
								   $contact['immeuble_numero'] . ' ' . $contact['rue_nom'],
								   $contact['code_postal'],
								   $contact['commune_nom_propre'],
								   $contact['contact_sexe'],
								   $contact['contact_email'],
								   $contact['contact_mobile'],
								   $contact['contact_telephone'],
								   $contact['contact_electeur']);
			}
			
			// On créé le fichier
			$nomFichier = 'export-' . $_COOKIE['leqg-user'] . '-' .date('Y-m-d-H\hi'). '-' . time() . '.csv';
			$f = fopen('exports/' . $nomFichier, 'w+');
			
				// On ajoute les lignes dans le fichier
				foreach ($fichier as $ligne) { fputcsv($f, $ligne, ';', '"'); }
			
			// On ferme le fichier
			fclose($f);
			
			// On retourne le nom du fichier
			return $nomFichier;
		}
	}
}

?>