<?php

/*
	Classe du noyau central du système LeQG
*/


class fiche extends core {
	
	// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $fiches; // tableau des informations disponibles à propos des fiches ouvertes
	public $fiche_ouverte = null;
	
	
	// Définition des méthodes
	
	public function __construct($db) {
		$this->db = $db;
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
	public	function infos($colonne) { echo utf8_encode($this->fiche_ouverte['contact_' . $colonne]); }
	public	function get_infos($colonne) { return utf8_encode($this->fiche_ouverte['contact_' . $colonne]); }
	
	
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
	
		if (!empty($nom)) { $affichage = $begin . mb_convert_case($nom, MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($nom_usage)) { $affichage .= ' ' . $begin . mb_convert_case($nom_usage, MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($prenoms)) { $affichage .= ' ' . $begin . mb_convert_case($prenoms, MB_CASE_TITLE, 'utf-8') . $end; }
		
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
	    
	    if ($return) : return $age; else : echo $age . ' ans'; endif;
	}
	
	
	// Méthode permettant l'affichage du lieu de naissance s'il existe dans la base de données
	public	function lieu_de_naissance($before = null, $return = false) {
		// on récupère le département de naissance
		if ($this->get_infos('naissance_commune_id')) {
			$ville = $this->get_infos('naissance_commune_id');
			
			$sql = $this->db->query('SELECT commune_nom FROM communes WHERE commune_id = ' . $ville);
			$row = $sql->fetch_array();
			$ville = $row[0];
			
			if ($before) echo $before . ' ';
			
			echo $ville . ' (' . $this->get_infos('naissance_departement') . ')';
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
		$ville = mb_convert_case($this->get_infos('adresse_ville'), MB_CASE_UPPER, 'utf-8');
		
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
	
	
	// modificationAdresse ( int , array , array , array ) permet de modifier l'adresse d'une fiche utilisateur sélectionnée
	public	function modificationAdresse( $contact , $ville , $rue , $immeuble ) {
		// On vérifie le format des informations entrées
			if (!is_numeric($contact) && !is_array($ville) && !is_array($rue) && !is_array($immeuble)) { return false; }
				
		// On prépare la requête BDD
			$query = 'UPDATE		contacts
					  SET		commune_id = ' . $ville['id'] . ',
					  			rue_id = ' . $rue['id'] . ',
					  			bureau_id = ' . $immeuble['bureau_id'] . ',
					  			immeuble_id = ' . $immeuble['id'] . ',
					  			canton_id = ' . $immeuble['canton_id'] . ',
					  			contact_adresse_ville = "' . $ville['nom'] . '",
					  			contact_adresse_rue = "' . $rue['nom'] . '",
					  			contact_adresse_numero = "' . $immeuble['numero'] . '"
					  WHERE		contact_id = ' . $contact;
			
		// On effectue la requête dans la BDD et on retourne le résultat
			$sql = $this->db->query($query);
			return $sql;
	}
}

?>