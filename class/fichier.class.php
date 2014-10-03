<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des fichiers sur le SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */


class fichier extends core {
	
	/**
	 * @var	object	$db			Propriété concenant le lien vers la base de données de l'utilisateur
	 * @var	string	$url		Propriété contenant l'URL du serveur
	 * @var	string	$compte		Propriété contenant les informations sur le compte connecté
	 */
	private $db, $url, $compte;
	

	/**
	 * Cette méthode permet la construction de la classe fichier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	object	$db			Lien vers la base de données de l'utilisateur
	 * @param	string	$compte		Informations concernant l'utilisateur connecté
	 * @param	string	$url		URL du serveur
	 * @return	void
	 */
	 
	public	function __construct($db, $compte, $url) {
		$this->db = $db;
		$this->compte = $compte;
		$this->url = $url;
	}
	

	/**
	 * Cette méthode permet de gérer l'upload de fichier de manière générique
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$index			Nom du fichier au sein du formulaire
	 * @param	string	$destination 	Répertoire de destination finale du fichier
	 * @param	int		$maxsize		Taille maximal du fichier uploadé
	 * @param	array	$extensions		Extensions acceptées
	 * @result	bool
	 */

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
	
	
	/**
	 * Cette méthode permet d'enregistrer un fichier dans la base de données
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$url		URL du fichier à enregistrer
	 * @param	string	$donnees 	Données associées au fichier
	 * @result	int					ID du fichier enregistré
	 */

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
	
	
	/**
	 * Cette méthode permet de retourner l'extension d'un nom de fichier qui lui est soumis
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$fichier	URL du fichier demandé
	 * @result	string				Extension du fichier
	 */

	public	function retourExtension( $fichier ) {
		return substr(strrchr($fichier, '.'), 1);
	}
	
	
	/**
	 * Cette méthode permet de préparer un texte pour en faire un nom de fichier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$chaine		Chaîne à retraiter
	 * @result	string				Chaîne retraitée
	 */

	public	function preparationNomFichier( $chaine ) {
		$chaine = preg_replace("#[^a-z0-9]#", "", strtolower($chaine));
		
		return $chaine;
	}
	
	
	/**
	 * Cette méthode permet de rechercher tous les fichiers par objet
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$objet		Type d'objet recherché
	 * @param	int		$id 		ID de l'objet recherché
	 * @result	array				Tableau des fichiers associés trouvés
	 */

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
	
	
	/**
	 * Cette méthode permet de calculer le nombre de fichiers associés à un objet
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string	$objet		Type d'objet recherché
	 * @param	int		$id 		ID de l'objet recherché
	 * @result	int					Nombre de fichiers associés
	 */

	public	function nombreFichiers( $objet , $id ) {
		// On récupère la liste des fichiers
			$fichiers = $this->listeFichiers( $objet , $id );
	
		// On calcule le nombre de fichiers auquel ça correspond
			$nombre = count($fichiers);
		
		// On renvoit ce résultat
			return $nombre;
	}
	
	
	/**
	 * Cette méthode permet de renvoyer un tableau de toutes les informations disponibles sur un fichier
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	int		$id 		ID de l'objet recherché
	 * @result	array				Tableau des informations trouvées
	 */

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
	
	
	/**
	 * Cette méthode permet de renvoyer le type de fichier selon l'extension à partir d'un ID
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	int		$id 		ID de l'objet recherché
	 * @result	string				Type de fichier
	 */

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