<?php

/*
	Classe du noyau central du système LeQG
*/


class fichier extends core {
	
// Définition des propriétés
	private $db; // Lien vers la base MySQL
	private $compte; // ID du compte qui utilise à l'instant donné la plateforme
	
	
// Définition des méthodes
	
	public	function __construct($db, $compte) {
		$this->db = $db;
		$this->compte = $compte;
	}
		
	
// Méthodes liées à la classe sélectionnée
	
	// upload( string , string , int , array ) permet de gérer l'upload de fichier de manière générique
	public	function upload( $index , $destination , $maxsize = false , $extensions = false ) {
		// test1 : on vérifie que le fichier s'est uploadé correctement
		if (!isset($_FILES[$index]) || $_FILES[$index]['error'] > 0) return false;
		
		// test2 : on vérifie si on ne dépasse pas la taille limite
		if ($maxsize !== FALSE && $_FILES[$index]['size'] > $maxsize) return false;
		
		// test3 : on vérifie si l'extension est autorisée
		$extension = $this->retourExtension($_FILES[$index]['name']);
		if ($extensions !== FALSE && !in_array($extension, $extensions)) return false;
		
		// Dans ce cas, on déplace le fichier à sa destination finale
		return move_uploaded_file($_FILES[$index]['tmp_name'], $destination);
	}
	
	
	// enregistrement( string , array ) permet d'enregistrer un fichier dans la base de données
	public	function enregistrement( $url , $donnees ) {
		$query = 'INSERT INTO fichiers (contact_id,
										compte_id,
										interaction_id,
										dossier_id,
										fichier_nom,
										fichier_labels,
										fichier_description,
										fichier_url,
										fichier_reference,
										fichier_timestamp )
								VALUES (	"' . $donnees['contact'] . '",
										"' . $this->compte . '",
										"' . $donnees['objet'] . '",
										"",
										"' . $donnees['nom'] . '",
										"' . implode(',' , $donnees['labels']) . '",
										"' . $donnees['description'] . '",
										"' . $url . '",
										"' . $donnees['reference'] . '",
										NOW() )';
		
		$sql = $this->db->query($query);
		
		if ($sql) : return $this->db->insert_id; else : return false; endif;
	}
	
	
	// retourExtension( string ) permet de retourner l'extension d'un nom de fichier qui lui est soumis
	public	function retourExtension( $fichier ) {
		return substr(strrchr($fichier, '.'), 1);
	}
	
	
	// preparationNomFichier( string ) permet de préparer un texte pour en faire un nom de fichier
	public	function preparationNomFichier( $chaine ) {
		$chaine = preg_replace("#[^a-z0-9]#", "", strtolower($chaine));
		
		return $chaine;
	}
	
	
	// listeFichiers( string , int ) permet de rechercher tous les fichiers par objet
	public	function listeFichiers( $objet , $id ) {
		// On défini tout d'abord la liste des objets possibles
			$objets = array('contact', 'interaction');
		
		// On vérifie que l'objet demandé est autorisé
			if (!in_array($objet, $objets)) return false;
		
		// On prépare la requête
			$query = 'SELECT *
					  FROM fichiers
					  WHERE ' . $objet . '_id = ' . $id . '
					  ORDER BY fichier_timestamp ASC';
			
		// On lance la requête
			$sql = $this->db->query($query);
			
		// On prépare le tableau des résultats
			$fichiers = array();
			
		// On récupère les résultats
			while ($row = $sql->fetch_assoc()) :
				$fichiers[] = $this->formatage_donnees($row);
			endwhile;
			
		// On renvoit le tableau des résultats
			return $fichiers;
	}
	
	
	// nombreFichiers( string , int ) permet de calculer le nombre de fichiers associés à un objet
	public	function nombreFichiers( $objet , $id ) {
		// On récupère la liste des fichiers
			$fichiers = $this->listeFichiers( $objet , $id );
	
		// On calcule le nombre de fichiers auquel ça correspond
			$nombre = count($fichiers);
		
		// On renvoit ce résultat
			return $nombre;
	}
}
?>