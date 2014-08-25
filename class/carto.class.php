<?php

/*
	Classe du noyau cartographique du site
*/


class carto extends core {
	
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
		
	
// Méthodes liées à la classe sélectionnée
	
	// recherche_ville( string ) permet de renvoyer une liste de toutes les villes répondant à la recherche proposée
	public	function recherche_ville( $search ) {
		// On sécurise la recherche
			$search = $this->formatage_recherche($search);
			
		// On prépare le tableau de destination finale des résultats
			$villes = array();
			
		// On prépare la requête de recherche approximative (mais en excluant les correspondances exactes trouvées plus haut
			$query = 'SELECT		*
					  FROM		communes
					  WHERE		commune_nom_propre LIKE "%' . $search . '%"
					  ORDER BY	commune_nom ASC
					  LIMIT		0, 25';
		
		// On lance la requête et on tri les résultats dans un tableau $villes
			$sql = $this->db->query($query);
			
			while ($row = $sql->fetch_assoc()) { $villes[] = $this->formatage_donnees($row); }

		// On retourne le tableau
			return $villes;
	}
	
	
	// recherche_rue( int , string ) permet de renvoyer une liste de toutes les rues répondant à une recherche proposée
	public	function recherche_rue( $ville , $search = '' ) {
		// On sécurise la recherche
			$search = $this->formatage_recherche($search);
		
		// On vérifie que la ville entrée est bien un champ numérique
			if (!is_numeric($ville)) return false;
		
		// On prépare le tableau de destination finale des résultats
			$rues = array();
		
		// On prépare la requête de recherche approximatinve (mais en excluant les correspondances exactes trouvées plus haut
			$query = 'SELECT		*
					  FROM		rues
					  WHERE		commune_id = ' . $ville . '
					  AND		rue_nom LIKE "%' . $search . '%"
					  ORDER BY	rue_nom ASC';
		
		// On lance la requête et on tri les résultats dans le tableau $rues
			$sql = $this->db->query($query);
			
			while ($row = $sql->fetch_assoc()) $rues[] = $this->formatage_donnees($row);
			
		// On retourne le tableau
			return $rues;
	}
	
	
	// region( int ) permet de renvoyer toutes les informations relatives à une région demandée par son id
	public	function region( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
		
		// On prépare la requête de recherche des informations
			$query = 'SELECT	*
					  FROM		regions
					  WHERE		region_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	// departement( int ) permet de renvoyer toutes les informations relatives à un département demandé par son id
	public	function departement( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
		
		// On prépare la requête de recherche des informations
			$query = 'SELECT	*
					  FROM		departements
					  WHERE		departement_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	// canton( int ) permet de renvoyer toutes les informations relatives à un canton demandé par son id
	public	function canton( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
		
		// On prépare la requête de recherche des informations
			$query = 'SELECT	*
					  FROM		cantons
					  WHERE		canton_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	// bureau( int ) permet de renvoyer toutes les informations relatives à un bureau de vote demandé par son id
	public	function bureau( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
		
		// On prépare la requête de recherche des informations
			$query = 'SELECT		*
					  FROM		bureaux
					  WHERE		bureau_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	// ville( int ) permet de renvoyer toutes les informations relatives à une ville demandée par son id
	public	function ville( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
		
		// On prépare la requête de recherche des informations
			$query = 'SELECT		*
					  FROM		communes
					  WHERE		commune_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	// rue( int ) permet de renvoyer toutes les inforations relatives à une rue demandée par son id
	public	function rue( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
			
		// On prépare la requête de recherche des informations
			$query = 'SELECT		*
					  FROM		rues
					  WHERE		rue_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
			
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
			
		// On retourne les résultats
			return $result;
	}
	
	
	// immeuble( int ) permet de renvoyer toutes les informations relatives à un immeuble demandé par son id
	public	function immeuble( $id ) {
		// on sécurise la recherche
			$id = $this->securisation_string($id);
			
		// On prépare la requête de recherche des informations
			$query = 'SELECT		*
					  FROM		immeubles
					  WHERE		immeuble_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	// afficherCanton( int [ , bool ] ) permet d'afficher le nom d'un canton grâce à son ID
	public	function afficherCanton( $id , $return = false ) {
		// On lance la recherche d'informations
			$canton = $this->canton($id);
		
		// On retourne le résultat demandé
			if ($return) : return $canton['nom']; else : echo $canton['nom']; endif;
	}
	
	
	// afficherVille( int [ , bool ] ) permet d'afficher le nom d'une ville grâce à son ID
	public	function afficherVille( $id , $return = false ) {
		// On lance la recherche d'informations
			$ville = $this->ville($id);
		
		// On retourne le résultat demandé
			if ($return) : return $ville['nom']; else : echo $ville['nom']; endif;
	}
	
	
	// afficherRue( int [ , bool ] ) permet d'afficher le nom d'une rue grâce à son ID
	public	function afficherRue( $id , $return = false ) {
		// On lance la recherche d'informations
			$rue = $this->rue($id);
		
		// On retourne le résultat demandé
			if ($return) : return $rue['nom']; else : echo $rue['nom']; endif;
	}
	
	
	// afficherDepartement( int , bool ) permet d'afficher le nom du département entré par son ID
	public	function afficherDepartement( $id , $return = false ) {
		// On lance la recherche dans la base de données pour retrouver le département
		$query = 'SELECT * FROM departements WHERE departement_id = ' . $id;
		$sql = $this->db->query($query);
		$data = $sql->fetch_assoc();
		
		// On retourne l'information
		if (!$return) echo $data['departement_nom'];
		
		return $data['departement_nom'];
	}
	
	
	// afficherRegion( int , bool ) permet d'afficher le nom de la région entrée par son ID
	public	function afficherRegion( $id , $return = false ) {
		// On lance la recherche dans la base de données pour retrouver la région
		$query = 'SELECT * FROM regions WHERE region_id = ' . $id;
		$sql = $this->db->query($query);
		$data = $sql->fetch_assoc();
		
		// On retourne l'information
		if (!$return) echo $data['region_nom'];
		
		return $data['region_nom'];
	}
	
	
	// afficherImmeuble( int [ , bool ] ) permet d'afficher le numéro d'un immeuble grâce à son ID
	public	function afficherImmeuble( $id , $return = false ) {
		// On lance la recherche d'informations
			$immeuble = $this->immeuble($id);
			
		// On retourne le résultat demandé
			if ($return) : return $immeuble['numero']; else : echo $immeuble['numero']; endif;
	}
	
	
	// listeBureaux( int ) permet de retourner la liste des bureaux d'une commune 
	public	function listeBureaux( $ville ) {
		// On vérifie que les arguments sont bien des éléments numériques
			if (!is_numeric($ville)) return false;
			
		// On prépare la requête de récupération des immeubles correspondant
			$query = 'SELECT	*
					  FROM		bureaux
					  WHERE		commune_id = ' . $ville . '
					  ORDER BY	bureau_numero ASC';
		
		// On prépare le tableau qui permettra de renvoyer les données
			$bureaux = array();
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $immeubles
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $bureaux[] = $this->formatage_donnees($row);
		
		// On retourne les données
			return $bureaux;
	}
	
	
	// listeRues( int ) permet de retourner la liste des rues d'une commune 
	public	function listeRues( $ville ) {
		// On vérifie que les arguments sont bien des éléments numériques
			if (!is_numeric($ville)) return false;
			
		// On prépare la requête de récupération des immeubles correspondant
			$query = 'SELECT	*
					  FROM		rues
					  WHERE		commune_id = ' . $ville . '
					  ORDER BY	rue_nom ASC';
		
		// On prépare le tableau qui permettra de renvoyer les données
			$rues = array();
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $immeubles
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $rues[] = $this->formatage_donnees($row);
		
		// On retourne les données
			return $rues;
	}
	
	
	// listeImmeubles( int ) permet de retourner la liste des immeubles 
	public	function listeImmeubles( $rue ) {
		// On vérifie que les arguments sont bien des éléments numériques
			if (!is_numeric($rue)) return false;
		
		// On prépare la requête de récupération des immeubles correspondant
			$query = 'SELECT	*
					  FROM		immeubles
					  WHERE		rue_id = ' . $rue . '
					  ORDER BY	immeuble_numero ASC';
		
		// On prépare le tableau qui permettra de renvoyer les données
			$immeubles = array();
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $immeubles
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $immeubles[] = $this->formatage_donnees($row);
		
		// Pour le tri, on retire toutes les lettres de la colonne numéro
			foreach ($immeubles as $key => $immeuble) {
				// On copie la colonne numéro
				$immeuble['numero_safe'] = $immeuble['numero'];
				
				// On retire ce qui n'est pas un chiffre
				$immeuble['numero_safe'] = preg_replace('#[^0-9]#', '', $immeuble['numero_safe']);
				
				// On enregistre ce numéro safe
				$immeubles[$key]['numero_safe'] = $immeuble['numero_safe'];
			}

		// On trie le tableau pour des résultats dans l'ordre logique
			$this->triParColonne($immeubles, 'numero_safe');
		
		// On retourne les données
			return $immeubles;
	}
	
	
	// listeElecteurs( int ) permet de retourner la liste des électeurs pour un immeuble
	public	function listeElecteurs( $immeuble ) {
		// On vérifie que les arguments sont bien des élements numériques
			if (!is_numeric($immeuble)) return false;
		
		// On prépare la requête de récupération des électeurs correspondant
			$query = 'SELECT	*
					  FROM		contacts
					  WHERE		immeuble_id = ' . $immeuble . '
					  ORDER BY	contact_nom, contact_nom_usage, contact_prenoms ASC';
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $electeurs
			$electeurs = array();
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $electeurs[] = $this->formatage_donnees($row);
		
		// On retourne les données
			return $electeurs;
	}
	
	
	// listeElecteursParBureau( int ) permet de retourner la liste des électeurs pour un bureau dont les coordonnées sont ou non connues
	public	function listeElecteursParBureau( $bureau , $coordonnees = false ) {
		// On vérifie que les arguments sont bien des élements numériques
			if (!is_numeric($bureau)) return false;
		
		// On prépare la requête de récupération des électeurs correspondant
			$query = 'SELECT	*
					  FROM		contacts
					  LEFT JOIN	immeubles
					  ON		immeubles.immeuble_id = contacts.immeuble_id
					  LEFT JOIN	bureaux
					  ON		bureaux.bureau_id = immeubles.bureau_id
					  WHERE		bureaux.bureau_id = ' . $bureau;
					  
		// On rajoute la condition 'coordonnees' si demandé
		if ($coordonnees) {
			
			$query .= ' AND	( ( contact_email IS NOT NULL AND contact_optout_email = 0 ) OR	( contact_telephone IS NOT NULL AND contact_optout_telephone = 0 ) OR ( contact_mobile IS NOT NULL AND contact_optout_mobile = 0 ) )';
			$query .= ' AND contact_optout_global = 0';
			
		}
		
		// On rajoute l'ordre nécessaire aux résultats
			$query .= ' ORDER BY	contact_nom, contact_nom_usage, contact_prenoms ASC';
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $electeurs
			$electeurs = array();
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $electeurs[] = $this->formatage_donnees($row);
		
		// On retourne les données
			return $electeurs;
	}
	
	
	// bureauParImmeuble( int ) permet d'afficher l'ID d'un bureau de vote pour un immeuble demandé
	public	function bureauParImmeuble( $immeuble ) {
		// On vérifie que l'argument est bien un ID
		if (!is_numeric($immeuble)) return false;
		
		// On cherche l'information dans la base de données
		$query = 'SELECT * FROM immeubles WHERE immeuble_id = ' . $immeuble;
		$sql = $this->db->query($query);
		$infos = $sql->fetch_assoc();
		
		// On retourne l'id du bureau
		return $infos['bureau_id'];
	}
	
	
	// villeParImmeuble( int ) permet d'afficher l'ID d'une ville pour un immeuble demandé
	public	function villeParImmeuble( $immeuble ) {
		// On vérifie que l'argument est bien un ID
		if (!is_numeric($immeuble)) return false;
		
		// On cherche l'information dans la base de données
		$query = 'SELECT * FROM immeubles LEFT JOIN rues ON rues.rue_id = immeubles.rue_id WHERE immeuble_id = ' . $immeuble;
		$sql = $this->db->query($query);
		$infos = $sql->fetch_assoc();
		
		// On retourne l'id du bureau
		return $infos['commune_id'];
	}
	
	
	// cantonParImmeuble( int ) permet d'afficher l'ID d'un canton pour un immeuble demandé
	public	function cantonParImmeuble( $immeuble ) {
		// On vérifie que l'argument est bien un ID
		if (!is_numeric($immeuble)) return false;
		
		// On cherche l'information dans la base de données
		$query = 'SELECT * FROM immeubles WHERE immeuble_id = ' . $immeuble;
		$sql = $this->db->query($query);
		$infos = $sql->fetch_assoc();
		
		// On retourne l'id du canton
		return $infos['canton_id'];
	}
	
	
	// detailAdresse( int ) permet de récupérer un tableau contenant l'ensemble des informations disponibles à partir d'un immeuble
	public	function detailAdresse( $immeuble ) {
		// On vérifie le format de l'id de l'immeuble pour continuer
		if (!is_numeric($immeuble)) return false;
		
		// On prépare la requête de récupération de toutes les informations disponibles
		$query = 'SELECT		*
				  FROM		immeubles
				  LEFT JOIN rues				ON	rues.rue_id = immeubles.rue_id
				  LEFT JOIN	communes			ON	communes.commune_id = rues.commune_id
				  LEFT JOIN codes_postaux	ON	codes_postaux.commune_id = communes.commune_id
				  LEFT JOIN	bureaux			ON	bureaux.bureau_id = immeubles.bureau_id
				  LEFT JOIN	cantons			ON	cantons.canton_id = bureaux.canton_id
				  LEFT JOIN arrondissements	ON	arrondissements.arrondissement_id = cantons.arrondissement_id
				  LEFT JOIN	departements		ON	departements.departement_id = arrondissements.departement_id
				  LEFT JOIN	regions			ON	regions.region_id = departements.region_id
				  WHERE		immeubles.immeuble_id = ' . $immeuble;

		// On lance la requête BDD MySQL et on prépare le tableau de rendu des résultats
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
		
		// On retourne le tableau complet
		return $row;
	}
	
	
	// adressePostale( int , string , bool ) permet d'afficher une adresse postale complète à partir d'id d'un immeuble
	public	function adressePostale( $immeuble , $separateur = '<br>' , $return = false ) {
		// On vérifie le format de l'id de l'immeuble pour continuer
		if (!is_numeric($immeuble)) return false;

		// On récupère les informations liées à l'adresse de l'immeuble demandée
		$informations = $this->detailAdresse( $immeuble );

		// On formate les composants de l'adresse correctement
		$adresse['numero'] = $informations['immeuble_numero'];
		$adresse['rue'] = mb_convert_case($informations['rue_nom'], MB_CASE_LOWER, 'utf-8');
		$adresse['cp'] = $informations['code_postal'];
		$adresse['ville'] = mb_convert_case($informations['commune_nom'], MB_CASE_UPPER, 'utf-8');
		
		// On prépare la variable d'affichage du rendu
		$affichage = $adresse['numero'] . ' ';
		
		// On affiche conditionnement la suite de l'adresse
		if (!empty($adresse['rue'])) $affichage .= $adresse['rue'] . $separateur;
		if (!empty($adresse['cp'])) $affichage .= $adresse['cp'] . ' ';
		if (!empty($adresse['ville'])) $affichage .= $adresse['ville'] . $separateur;

		// On remet en forme l'affichage
		$affichage = $this->tpl_transform_texte($affichage);
		
		// On retourne les informations demandées
		if (!$return) echo $affichage;
		return $affichage;
	}
	
	
	// bureauDeVote( int ) permet d'afficher les informations relatives à un bureau de vote demandé d'après un immeuble
	public	function bureauDeVote( $immeuble , $return = false ) {
		// On vérifie le format de l'id de l'immeuble pour continuer
		if (!is_numeric($immeuble)) return false;
		
		// On récupère toutes les informations nécessaires par rapport à cet immeuble et donc son bureau de vote
		$informations = $this->detailAdresse( $immeuble );

		// On retraite les informations
		$bureau['numero'] = $informations['bureau_numero'];
		$bureau['nom'] = mb_convert_case($informations['bureau_nom'], MB_CASE_TITLE, 'utf-8');
		$bureau['ville'] = $this->tpl_transform_texte($informations['commune_nom']);
		
		// On prépare le rendu 
		$affichage = 'Bureau ' . $bureau['numero'] . ' – ' . $bureau['ville'] . '<br>' . $bureau['nom'];

		// On affiche le rendu si demandé
		if (!$return) echo $affichage;
		
		// On retourne dans tous les cas le rendu
		return $affichage;
	}
	
	
	// ajoutRue( int , string ) permet d'ajouter une nouvelle rue à la base de données
	public	function ajoutRue( $ville , $rue ) {
		// On protège les informations pour la BDD
		$rue = $this->securisation_string($rue);

		// On prépare la requête SQL
		$query = 'INSERT INTO rues ( commune_id , rue_nom ) VALUES ( ' . $ville . ' , "' . $rue . '" )';
		
		// On exécute la requête et on retourne l'ID de l'entrée
		$this->db->query($query);
		return $this->db->insert_id;
	}
	
	
	// ajoutImmeuble( array ) permet d'ajouter un nouvel immeuble à partir des informations envoyées
	public	function ajoutImmeuble( $infos ) {
		// On vérifie que l'entrée est bien un tableau
		if (!is_array($infos)) return false;
		
		// On prépare la requête
		$query = 'INSERT INTO immeubles (`rue_id`, `immeuble_numero`) VALUES (' . $infos['rue'] . ', "' . $infos['numero'] . '")';
		
		// On enregistre le tout dans la base de données puis on retourne l'ID de l'insertion
		$this->db->query($query);
		return $this->db->insert_id;
	}
	
	
	// nombreElecteurs( string , int ) permet de savoir combien d'électeurs existent dans une ville, une rue ou un immeuble
	public	function nombreElecteurs( $branche , $id , $coordonnees = null ) {
		if (!is_string($branche) || !is_numeric($id)) return false;
		
		if (isset($branche)) {
			// On prépare la requête de calcul du nombre d'électeur dans la commune
			$query = 'SELECT	COUNT(*)
					  AS		nombre
					  FROM		contacts
					  LEFT JOIN immeubles
					  ON		immeubles.immeuble_id = contacts.immeuble_id
					  LEFT JOIN	rues
					  ON		rues.rue_id = immeubles.rue_id
					  LEFT JOIN	communes
					  ON		communes.commune_id = rues.commune_id
					  LEFT JOIN	bureaux
					  ON		bureaux.bureau_id = immeubles.bureau_id
					  LEFT JOIN	cantons
					  ON		cantons.canton_id = bureaux.canton_id ';
					  
			if ($branche == 'bureau') {
				$query .= 'WHERE ' . $branche . 'x.' . $branche . '_id = ' . $id;
			} else {
				$query .= 'WHERE ' . $branche . 's.' . $branche . '_id = ' . $id;
			}
		}
		
		
		// On regarde si on demandait seulement ceux ayant des coordonnées
		if (!is_null($coordonnees) && !empty($query)) {
			$query .= ' AND contacts.contact_' . $coordonnees . ' IS NOT NULL AND contact_optout_' . $coordonnees . ' = 0';
		} else if (is_null($coordonnees) && !empty($query)) {
			$query .= '  AND contacts.contact_electeur = 1';
		}
		
		
		// On vérifie qu'il y a bien une requête de préparée, si oui on l'exécute et on imprime le résultat
		if (!empty($query)) {
			$sql = $this->db->query($query);
			$nombre = $sql->fetch_assoc();
			
			return number_format($nombre['nombre'], 0, ',', '&nbsp;');
		}
		
		return false;
	}
	
	
	// coordonneesDansImmeuble( int ) permet de savoir s'il existe des fiches dont nous possédons les coordonnées dans l'immeuble
	public	function coordonneesDansImmeuble( $immeuble ) {
		// on vérifie le format des arguments
		if (!is_numeric($immeuble)) return false;
		
		// On prépare la requête
		$query = 'SELECT	contact_id
				  FROM		contacts
				  WHERE		immeuble_id = ' . $immeuble . '
				  AND		( 
					  				( contact_email IS NOT NULL AND contact_optout_email = 0 )
					  			 OR	( contact_telephone IS NOT NULL AND contact_optout_telephone = 0 )
					  			 OR ( contact_mobile IS NOT NULL AND contact_optout_mobile = 0 )
				  			 )
				  AND		contact_optout_global = 0';
				  
		// On effectue la requête
		$sql = $this->db->query($query);
		
		// On récupère le résultat
		$resultat = $sql->num_rows;
		
		// On retourne le résultat
		return $resultat;
	}
	
	
	// coordonneesDansBureau( int ) permet de savoir s'il existe des fiches dont nous possédons les coordonnées dans le bureau
	public	function coordonneesDansBureau( $bureau ) {
		// on vérifie le format des arguments
		if (!is_numeric($bureau)) return false;
		
		// On prépare la requête
		$query = 'SELECT	contact_id
				  FROM		contacts
				  LEFT JOIN	immeubles
				  ON		immeubles.immeuble_id = contacts.immeuble_id
				  LEFT JOIN	bureaux
				  ON		bureaux.bureau_id = immeubles.bureau_id
				  WHERE		bureaux.bureau_id = ' . $bureau . '
				  AND		( 
					  				( contact_email IS NOT NULL AND contact_optout_email = 0 )
					  			 OR	( contact_telephone IS NOT NULL AND contact_optout_telephone = 0 )
					  			 OR ( contact_mobile IS NOT NULL AND contact_optout_mobile = 0 )
				  			 )
				  AND		contact_optout_global = 0';
				  
		// On effectue la requête
		$sql = $this->db->query($query);
		
		// On récupère le résultat
		$resultat = $sql->num_rows;
		
		// On retourne le résultat
		return $resultat;
	}
}
?>