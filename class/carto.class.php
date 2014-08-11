<?php

/*
	Classe du noyau cartographique du site
*/


class carto extends core {
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte qui utilise à l'instant donné la plateforme
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte) {
		$this->db = $db;
		$this->compte = $compte;
	}
		
	
// Méthodes liées à la classe sélectionnée
	
	// recherche_ville( string ) permet de renvoyer une liste de toutes les villes répondant à la recherche proposée
	public	function recherche_ville( $search ) {
		// On sécurise la recherche
			$search = $this->securisation_string($search);
			
		// On prépare le tableau de destination finale des résultats
			$villes = array();
			
		// On prépare la requête de recherche exacte
			$query = 'SELECT		*
					  FROM		communes
					  WHERE		commune_nom = "' . $search . '"
					  ORDER BY	commune_nom ASC';
		
		// On lance la requête et on tri les résultats dans un tableau $villes
			$sql = $this->db->query($query);
			
			while ($row = $sql->fetch_assoc()) $villes[] = $this->formatage_donnees($row);
		
		// On prépare la requête de recherche approximative (mais en excluant les correspondances exactes trouvées plus haut
			$query = 'SELECT		*
					  FROM		communes
					  WHERE		commune_nom LIKE "%' . $search . '%"
					  AND		commune_nom != "'.$search.'"
					  ORDER BY	commune_nom ASC
					  LIMIT		0, 25';
		
		// On lance la requête et on tri les résultats dans un tableau $villes
			$sql = $this->db->query($query);
			
			while ($row = $sql->fetch_assoc()) $villes[] = $this->formatage_donnees($row);

		// On retourne le tableau
			return $villes;
	}
	
	
	// recherche_rue( int , string ) permet de renvoyer une liste de toutes les rues répondant à une recherche proposée
	public	function recherche_rue( $ville , $search = '' ) {
		// On sécurise la recherche
			$search = $this->securisation_string($search);
		
		// On vérifie que la ville entrée est bien un champ numérique
		if (!is_numeric($ville)) return false;
		
		// On prépare le tableau de destination finale des résultats
			$rues = array();
		
		// On prépare la requête de recherche exacte
			$query = 'SELECT		*
					  FROM		rues
					  WHERE		commune_id = ' . $ville . '
					  AND		rue_nom = "' . $search . '"';
			
		// On lance la requête et on tri les résultats dans un tableau $rues
			$sql = $this->db->query($query);
			
			while ($row = $sql->fetch_assoc()) $rues[] = $this->formatage_donnees($row);
		
		// On prépare la requête de recherche approximatinve (mais en excluant les correspondances exactes trouvées plus haut
			$query = 'SELECT		*
					  FROM		rues
					  WHERE		commune_id = ' . $ville . '
					  AND		rue_nom LIKE "%' . $search . '%"
					  AND		rue_nom != "' . $search . '"
					  ORDER BY	rue_nom ASC
					  LIMIT		0, 25';
		
		// On lance la requête et on tri les résultats dans le tableau $rues
			$sql = $this->db->query($query);
			
			while ($row = $sql->fetch_assoc()) $rues[] = $this->formatage_donnees($row);
		
		// On retourne le tableau
			return $rues;
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
	
	
	// afficherImmeuble( int [ , bool ] ) permet d'afficher le numéro d'un immeuble grâce à son ID
	public	function afficherImmeuble( $id , $return = false ) {
		// On lance la recherche d'informations
			$immeuble = $this->immeuble($id);
			
		// On retourne le résultat demandé
			if ($return) : return $immeuble['numero']; else : echo $immeuble['numero']; endif;
	}
	
	
	// listeImmeubles( int , int ) permet de retourner la liste des immeubles 
	public	function listeImmeubles( $ville , $rue ) {
		// On vérifie que les arguments sont bien des éléments numériques
			if (!is_numeric($ville) && !is_numeric($rue)) return false;
		
		// On prépare la requête de récupération des immeubles correspondant
			$query = 'SELECT		*
					  FROM		immeubles
					  WHERE		commune_id = ' . $ville . '
					  AND		rue_id = ' . $rue . '
					  ORDER BY	immeuble_numero ASC';
		
		// On prépare le tableau qui permettra de renvoyer les données
			$immeubles = array();
		
		// On effectue la requête BDD et on affiche les résultats dans un tableau $immeubles
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) $immeubles[] = $this->formatage_donnees($row);
				
		// On trie le tableau pour des résultats dans l'ordre logique
			$this->triParColonne($immeubles, 'numero');
		
		// On retourne les données
			return $immeubles;
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
}
?>