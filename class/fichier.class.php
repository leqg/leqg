<?php

/*
	Classe du noyau central du système LeQG
*/


class fichier extends core {
	
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
		if (!isset($donnees['objet'])) $donnees['objet'] = 0;
		if (!isset($donnees['dossier'])) $donnees['dossier'] = 0;
		
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
										"' . $donnees['dossier'] . '",
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
			$objets = array('contact', 'interaction', 'dossier');
		
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
	
	
	// informations( int ) permet de renvoyer un tableau de toutes les informations disponibles sur un fichier
	public	function informations( $id ) {
		// On vérifie que l'entrée est bien une donnée numérique
		if (!is_numeric($id)) return false;
		
		// On prépare la requête SQL
		$query = 'SELECT * FROM fichiers WHERE fichier_id = ' . $id;
		
		// On exécute la requête
		$sql = $this->db->query($query);
		$informations = $this->formatage_donnees($sql->fetch_assoc());
		
		// On retourne le tableau contenant les informations disponibles
		return $informations;
	}
	
	
	// extension ( int ) permet de renvoyer le type de fichier selon l'extension à partir d'un ID
	public	function extension ( $id ) {
		if (!is_numeric($id)) return false;
		
		// On récupère les informations sur le fichier
		$i = $this->informations($id);
		$extension = $this->retourExtension($i['url']);
		
		// On prépare le tableau des équivalences
		$extensions = array('pdf' => 'pdf',
							'doc' => 'texte',
							'docx' => 'texte',
							'odt' => 'texte',
							'txt' => 'texte',
							'xls' => 'tableur',
							'xlsx' => 'tableur',
							'ods' => 'tableur',
							'csv' => 'tableur',
							'ppt' => 'presentation',
							'pptx' => 'presentation',
							'opd' => 'presentation',
							'jpg' => 'image',
							'jpeg' => 'image',
							'png' => 'image',
							'gif' => 'image',
							'tif' => 'image',
							'tiff' => 'image',
							'bmp' => 'image',
							'zip' => 'archive',
							'gzip' => 'archive',
							'rar' => 'archive',
							'tar' => 'archive',
							'avi' => 'video',
							'mpeg' => 'video',
							'mpg' => 'video',
							'mp4' => 'video',
							'flv' => 'video',
							'mp3' => 'audio');
		
		return $extensions[$extension];
							
	}
}
?>