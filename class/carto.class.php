<?php
/**
 * La classe carto représente le noyau cartographique du système LeQG
 * 
 * Cette classe comprend l'ensemble des méthodes nécessaires à la récupération d'informations
 * tirées du module géographique du système leQG.
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class carto extends core {
	
	/**
	 * @var	object	$db		Propriété concenant le lien vers la base de données de l'utilisateur
	 * @var	object	$noyau	Propriété contenant le lien vers la base de données globale LeQG
	 * @var	string	$url		Propriété contenant l'URL du serveur
	 */
	private $db, $noyau, $url;
	

	/**
	 * Cette méthode permet la construction de la classe carto
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	object $db Lien vers la base de données de l'utilisateur
	 * @param	object $noyau Lien vers la base de données globale LeQG
	 * @param	string $url URL du serveur
	 */
	 
	public	function __construct($db, $noyau, $url) {
		$this->db = $db;
		$this->noyau = $noyau;
		$this->url = $url;
	}
	
	
	/**
	 * Cette méthode permet de renvoyer une liste de toutes les villes répondant à la recherche lancée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $search Ville à rechercher
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer une liste de toutes les rues répondant à la recherche lancée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$ville		ID de la ville dans laquelle effectuer la recherche
	 * @param	string	$search		Rue à rechercher
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer une liste de tous les cantons répondant à la recherche lancée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$search		Canton à rechercher
	 * @return	array
	 */

	public	function recherche_canton( $search = '' ) {
		// On sécurise la recherche
		$search = $this->formatage_recherche($search);
		
		// On prépare le tableau des résultats
		$cantons = array();
		
		// On prépare la requête de recherche
		$query = 'SELECT	*
				  FROM		cantons
				  WHERE		canton_nom LIKE "%' . $search . '%"
				  ORDER BY	canton_nom ASC';
		
		// On exécute la recherche
		$sql = $this->db->query($query);
		
		// On affecte les résultats au tableau
		while ($row = $sql->fetch_assoc()) $cantons[] = $this->formatage_donnees($row);
		
		// On retourne le tableau
		return $cantons;
	}
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à une région demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID de la région demandée
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à un département demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID du département demandé
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à un arrondissement demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID de l'arrondissement demandé
	 * @return	array
	 */

	public	function arrondissement( $id ) {
		// On sécurise la recherche
			$id = $this->securisation_string($id);
		
		// On prépare la requête de recherche des informations
			$query = 'SELECT	*
					  FROM		arrondissements
					  WHERE		arrondissement_id = ' . $id;
		
		// On effectue la requête
			$sql = $this->db->query($query);
		
		// On traite les résultats
			$result = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne les résultats
			return $result;
	}
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à un canton demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID du canton demandé
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à un bureau de vote demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID du bureau de vote demandé
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à une ville demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID de la ville demandée
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à une rue demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID de la rue demandée
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer les informations relatives à un immeuble demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id		ID de l'immeuble demandé
	 * @return	array
	 */

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
	
	
	/**
	 * Cette méthode permet d'afficher le nom d'un arrondissement grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID de l'arrondissement demandé
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le nom de l'arrondissement ou rien en fonction de $return
	 */

	public	function afficherArrondissement( $id , $return = false ) {
		// On lance la recherche d'informations
			$arrondissement = $this->arrondissement($id);
		
		// On retourne le résultat demandé
			if ($return) : return $arrondissement['nom']; else : echo $arrondissement['nom']; endif;
	}
	
	
	/**
	 * Cette méthode permet d'afficher le nom d'un canton grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID du canton demandé
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le nom du canton ou rien en fonction de $return
	 */

	public	function afficherCanton( $id , $return = false ) {
		// On lance la recherche d'informations
			$canton = $this->canton($id);
		
		// On retourne le résultat demandé
			if ($return) : return $canton['nom']; else : echo $canton['nom']; endif;
	}
	
	
	/**
	 * Cette méthode permet d'afficher le nom d'une ville grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID de la ville demandée
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le nom de la ville ou rien en fonction de $return
	 */

	public	function afficherVille( $id , $return = false ) {
		// On lance la recherche d'informations
			$ville = $this->ville($id);
		
		// On retourne le résultat demandé
			if ($return) : return $ville['nom']; else : echo $ville['nom']; endif;
	}
	
	
	/**
	 * Cette méthode permet d'afficher le nom d'une rue grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID de la rue demandée
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le nom de la rue ou rien en fonction de $return
	 */

	public	function afficherRue( $id , $return = false ) {
		// On lance la recherche d'informations
			$rue = $this->rue($id);
		
		// On retourne le résultat demandé
			if ($return) : return $rue['nom']; else : echo $rue['nom']; endif;
	}
	
	
	/**
	 * Cette méthode permet d'afficher le nom d'un département grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID du département demandé
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le nom du département ou rien en fonction de $return
	 */

	public	function afficherDepartement( $id , $return = false ) {
		// On lance la recherche dans la base de données pour retrouver le département
		$query = 'SELECT * FROM departements WHERE departement_id = ' . $id;
		$sql = $this->db->query($query);
		$data = $sql->fetch_assoc();
		
		// On retourne l'information
		if (!$return) echo $data['departement_nom'];
		
		return $data['departement_nom'];
	}
	
	
	/**
	 * Cette méthode permet d'afficher le nom d'une région grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID de la région demandée
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le nom de la région ou rien en fonction de $return
	 */

	public	function afficherRegion( $id , $return = false ) {
		// On lance la recherche dans la base de données pour retrouver la région
		$query = 'SELECT * FROM regions WHERE region_id = ' . $id;
		$sql = $this->db->query($query);
		$data = $sql->fetch_assoc();
		
		// On retourne l'information
		if (!$return) echo $data['region_nom'];
		
		return $data['region_nom'];
	}
	
	
	/**
	 * Cette méthode permet d'afficher le numéro d'un immeuble grâce à son ID
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$id			ID de l'immeuble demandé
	 * @param	bool 	$return		Méthode de retour de l'information demandée
	 * @return	string|void			Le numéro de l'immeuble ou rien en fonction de $return
	 */

	public	function afficherImmeuble( $id , $return = false ) {
		// On lance la recherche d'informations
			$immeuble = $this->immeuble($id);
			
		// On retourne le résultat demandé
			if ($return) : return $immeuble['numero']; else : echo $immeuble['numero']; endif;
	}
	
	
	/**
	 * Cette méthode permet de récupérer une liste de bureaux de vote par ville
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$ville		ID de la ville contenant les bureaux de vote demandés
	 * @return	array				La liste des bureaux de vote dans la ville demandée
	 */

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
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $bureaux
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $bureaux[] = $this->formatage_donnees($row);
		
		// On retourne les données
			return $bureaux;
	}
	
	
	/**
	 * Cette méthode permet de récupérer la liste de tous les bureaux de vote connus
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @return	array	La liste des bureaux de vote connus
	 */

	public	function listeTousBureaux( ) {
		// On prépare la requête de récupération des immeubles correspondant
			$query = 'SELECT	*
					  FROM		`bureaux`
					  ORDER BY	`bureau_numero` ASC';
		
		// On prépare le tableau chargé d'accueillir les résultats
			$bureaux = array();
			
		// On effectue la requête BDD et on affiche les résultats dans un tableau $bureaux
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $bureaux[] = $this->formatage_donnees($row);
			
		// On retourne les résultats
			return $bureaux;
	}
	
	
	/**
	 * Cette méthode permet de récupérer une liste de rues par ville
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$ville		ID de la ville demandée
	 * @return	array				La liste des rues dans la ville demandée
	 */

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
	
	
	/**
	 * Cette méthode permet de récupérer une liste des immeubles dans une rue demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$rue		ID de la rue demandée
	 * @return	array			La liste des immeubles dans la rue demandée
	 */

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
	
	
	/**
	 * Cette méthode permet de récupérer une liste des électeurs d'un immeuble
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble		ID de l'immeuble demandé
	 * @return	array					La liste des électeurs dans l'immeuble demandé
	 */

	public	function listeElecteurs( $immeuble ) {
		// On vérifie que les arguments sont bien des élements numériques
			if (!is_numeric($immeuble)) return false;
		
		// On prépare la requête de récupération des électeurs correspondant
			$query = 'SELECT	*
					  FROM		contacts
					  WHERE		immeuble_id = ' . $immeuble . '
					  AND		contact_electeur  = 1
					  ORDER BY	contact_nom, contact_nom_usage, contact_prenoms ASC';
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $electeurs
			$electeurs = array();
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $electeurs[] = $this->formatage_donnees($row);
		
		// On retourne les données
			return $electeurs;
	}
	
	
	/**
	 * Cette méthode permet de connaître le nombre d'électeurs dans un immeuble
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble concerné par le comptage
	 * @return	int					Le nombre d'électeur dans l'immeuble demandé
	 */

	public	function nombreElecteursParImmeuble( $immeuble ) {		
		// On retourne les données
			$electeurs = $this->listeElecteurs($immeuble);
		
			return count($electeurs);
	}
	
	
	/**
	 * Cette méthode permet de connaître le nombre d'électeurs dans un bureau de vote
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$bureau			ID du bureau de vote concerné par le comptage
	 * @param	bool 	$coordonnees 	Si true ne compte que les électeurs dont le système 
	 * 									connait des coordonnées
	 * @return	int						Le nombre d'électeur dans le bureau de vote demandé
	 */

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
	
	
	/**
	 * Cette méthode permet de connaître le bureau de vote d'un immeuble demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble demandé
	 * @return	int					ID du bureau de vote relatif à l'immeuble demandé
	 */

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
	
	
	/**
	 * Cette méthode permet de connaître la ville correspondante à un immeuble
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble concerné par la demande
	 * @return	int					ID de la ville trouvée pour l'immeuble
	 */

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
	
	
	/**
	 * Cette méthode permet de connaître le canton correspondant à un immeuble
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble concerné par la demande
	 * @return	int					ID du canton trouvé pour l'immeuble
	 */

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
	
	
	/**
	 * Cette méthode permet de récupérer un tableau contenant toutes les informations
	 * géographiques disponibles pour un immeuble
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble concerné par la demande
	 * @return	array				Données géographiques trouvées
	 */

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
	
	
	/**
	 * Cette méthode permet d'afficher une adresse postale complète à partir d'un immeuble demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble concerné par la demande
	 * @param	string	$separateur	Séparateur HTML entre les composants de l'adresse
	 * @param	bool 	$return		Si oui, retourne l'information plutôt que de l'afficher
	 * @return	string|void			Affiche ou retourne l'adresse postale selon $return
	 */

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
	
	
	/**
	 * Cette méthode permet d'obtenir des informations sur le bureau de vote d'un immeuble
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$immeuble	ID de l'immeuble concerné par la demande
	 * @param	bool 	$return		Si oui, retourne l'information plutôt que de l'afficher
	 * @param	bool 	$mini		Si oui, prépare une version réduite des informations
	 * @return	string|void			Affiche ou retourne l'adresse postale selon $return
	 */

	public	function bureauDeVote( $immeuble , $return = false , $mini = false ) {
		// On vérifie le format de l'id de l'immeuble pour continuer
		if (!is_numeric($immeuble)) return false;
		
		// On récupère toutes les informations nécessaires par rapport à cet immeuble et donc son bureau de vote
		$informations = $this->detailAdresse( $immeuble );

		// On retraite les informations
		$bureau['numero'] = $informations['bureau_numero'];
		$bureau['nom'] = mb_convert_case($informations['bureau_nom'], MB_CASE_TITLE, 'utf-8');
		$bureau['ville'] = $this->tpl_transform_texte($informations['commune_nom']);
		
		// On prépare le rendu 
		if ($mini) {
			$affichage = 'Bureau ' . $bureau['numero'] . ' – ' . $bureau['nom'];
		} else {
			$affichage = 'Bureau ' . $bureau['numero'] . ' – ' . $bureau['ville'] . '<br>' . $bureau['nom'];
		}

		// On affiche le rendu si demandé
		if (!$return) echo $affichage;
		
		// On retourne dans tous les cas le rendu
		return $affichage;
	}
	
	
	/**
	 * Cette méthode permet d'ajouter une nouvelle rue à la base de données
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$ville		ID de la ville dans laquelle se trouve la rue
	 * @param	string 	$rue			Nom de la rue à ajouter dans la base de données
	 * @return	int					ID de la rue ajoutée
	 */

	public	function ajoutRue( $ville , $rue ) {
		// On protège les informations pour la BDD
		$rue = $this->securisation_string($rue);

		// On prépare la requête SQL
		$query = 'INSERT INTO rues ( commune_id , rue_nom ) VALUES ( ' . $ville . ' , "' . $rue . '" )';
		
		// On exécute la requête et on retourne l'ID de l'entrée
		$this->db->query($query);
		return $this->db->insert_id;
	}
	
	
	/**
	 * Cette méthode permet d'ajouter un nouvel immeuble à une rue
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	array	$infos		Informations relatives au nouvel immeuble
	 * @return	int					ID de l'immeuble ajouté
	 */

	public	function ajoutImmeuble( array $infos ) {
		// On vérifie que l'entrée est bien un tableau
		if (!is_array($infos)) return false;
		
		// On prépare la requête
		$query = 'INSERT INTO immeubles (`rue_id`, `immeuble_numero`) VALUES (' . $infos['rue'] . ', "' . $infos['numero'] . '")';
		
		// On enregistre le tout dans la base de données puis on retourne l'ID de l'insertion
		$this->db->query($query);
		return $this->db->insert_id;
	}
	
	
	/**
	 * Cette méthode permet d'estimer le nombre d'électeur pour un découpage géographique demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$branche			Découpage géographique concerné par l'estimation
	 * @param	int	 	$id				ID du découpage géographique concerné par l'estimation
	 * @param	string	$coordonnees 	Permet de restreindre l'estimation aux électeurs dont 
	 *									certaines coordonnées sont connues
	 * @return	int						Nombre d'électeur trouvé
	 */

	public	function nombreElecteurs( string $branche , int $id , $coordonnees = null ) {
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
	
	
	/**
	 * Cette méthode permet de savoir s'il existe dans un immeuble des fiches où des coordonnées
	 * sont connues
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int	$immeuble	Immeuble concerné par la recherche
	 * @return	int				Nombre de fiches trouvées
	 */

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
	
	
	/**
	 * Cette méthode permet de savoir s'il existe dans un bureau de vote des fiches où des 
	 * coordonnées sont connues
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int	$bureau		Bureau de vote concerné par la recherche
	 * @return	int				Nombre de fiches trouvées
	 */

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