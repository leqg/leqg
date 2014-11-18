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

class Carto {	
	
	/**
	 * Cette méthode permet de renvoyer une liste de toutes les villes répondant à la recherche lancée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $search Ville à rechercher
	 * @return	array
	 */

	public static function recherche_ville( $search ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On sécurise la recherche
		$search = "%$search%";
			
		// On prépare le tableau de destination finale des résultats
		$villes = array();
			
		// On lance la requête de recherche approximative (mais en excluant les correspondances exactes trouvées plus haut
		$query = $link->prepare('SELECT * FROM `communes` WHERE `commune_nom_propre` LIKE :search ORDER BY `commune_nom` ASC LIMIT 0, 25');
		$query->bindParam(':search', $search);
		$query->execute();
		
		// On retourne le tableau des résultats
		return $query->fetchAll(PDO::FETCH_ASSOC);
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

	public static function recherche_rue( $ville , $search = '' ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On sécurise la recherche
		$search = '%'.$search.'%';
		
		// On vérifie que la ville entrée est bien un champ numérique
		if (!is_numeric($ville)) return false;
		
		// On lance la requête de recherche approximatinve (mais en excluant les correspondances exactes trouvées plus haut
		$query = $link->prepare('SELECT * FROM `rues` WHERE `commune_id` = :ville AND `rue_nom` LIKE :search ORDER BY `rue_nom` ASC LIMIT 0, 30');
		$query->bindParam(':ville', $ville, PDO::PARAM_INT);
		$query->bindParam(':search', $search, PDO::PARAM_STR);
		$query->execute();
		
		// On retourne le tableau des résultats
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de renvoyer une liste de toutes les rues de la base répondant à la recherche lancée au format JSON
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$search		Rue à rechercher, toutes villes confondues
	 * @result	array				Tableau des informations concernant toutes les rues trouvées
	 */
	
	public static function recherche_rue_json($search) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On sécurise la recherche
		$search = '%'.$search.'%';
		
		// On exécute la requête de recherche
		$query = $link->prepare('SELECT * FROM `rues` LEFT JOIN `communes` ON `communes`.`commune_id` = `rues`.`commune_id` WHERE `rue_nom` LIKE :search ORDER BY `rue_nom` ASC LIMIT 0, 30');
		$query->bindParam(':search', $search, PDO::PARAM_STR);
		$query->execute();
		
		// On récupère les résultats
		$rues = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On retourne les résultats au format JSON
		return json_encode($rues);
	}
	
	
	/**
	 * Cette méthode permet de renvoyer une liste de toutes les villes de la base répondant à la recherche lancée au format JSON
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$search		Ville à rechercher
	 * @result	array				Tableau des informations concernant toutes les rues trouvées
	 */
	
	public static function recherche_ville_json($search) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On sécurise la recherche
		$search = '%'.$search.'%';
		
		// On exécute la requête de recherche
		$query = $link->prepare('SELECT * FROM `communes` WHERE `commune_nom_propre` LIKE :search ORDER BY `commune_nom` ASC');
		$query->bindParam(':search', $search, PDO::PARAM_STR);
		$query->execute();
		
		// On retourne le tableau des résultats sous format JSON
		$villes = $query->fetchAll(PDO::FETCH_ASSOC);
		return json_encode($villes);
	}
	
	
	/**
	 * Cette méthode permet de renvoyer une liste de toutes les bureaux de la base répondant à la recherche lancée au format JSON
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$search		Bureau à rechercher, toutes villes confondues
	 * @result	array				Tableau des informations concernant tous les bureaux trouvés
	 */
	
	public static function recherche_bureau_json($search) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On sécurise la recherche
		$search = '%'.$search.'%';
		
		// On exécute la requête de recherche
		$query = $link->prepare('SELECT * FROM `bureaux` WHERE `bureau_numero` LIKE :search OR `bureau_nom` LIKE :search ORDER BY `bureau_numero`, `bureau_nom` ASC');
		$query->bindParam(':search', $search, PDO::PARAM_STR);
		$query->execute();
		
		// On récupère la liste des bureaux correspondants à la recherche
		$bureaux = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// On prépare la liste des informations connues sur les communes pour éviter des recherches redondantes inutiles
		$communes_vues = array();
		$communes_donnees = array();
		
		// Pour chaque bureau, on cherche les informations sur la commune
		foreach ($bureaux as $key => $bureau) {
			// On vérifie d'abord si on possède déjà des informations sur une commune
			if (in_array($bureau['commune_id'], $communes_vues)) {
				$bureaux[$key] = array_merge($bureau, $communes_donnees[$bureau['commune_id']]);
			}
			
			// Sinon, on recherche les données
			else {
				$query = $link->prepare('SELECT * FROM `communes` WHERE `commune_id` = :commune');
				$query->bindParam(':commune', $bureau['commune_id'], PDO::PARAM_INT);
				$query->execute();
				
				// On récupère les informations sur la commune du bureau de vote pour les enregistrer en supprimant les informations des anciennes recherches
				unset($infos);
				$infos = $query->fetch(PDO::FETCH_ASSOC);
				
				// On affecte ces informations aux tableaux pour les retrouver
				$communes_vues[] = $infos['commune_id'];
				$communes_donnees[$infos['commune_id']] = $infos;
				
				$bureaux[$key] = array_merge($bureau, $infos);
			}
		}
		
		// On encode les informations trouvées en JSON et on les retourne au script
		return json_encode($bureaux);
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

	public static function recherche_canton( $search = '' ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On sécurise la recherche
		$search = '%'.$search.'%';
		
		// On exécute la requête de recherche
		$query = $link->prepare('SELECT * FROM `cantons` WHERE `canton_nom` LIKE :search ORDER BY `canton_nom` ASC');
		$query->bindParam(':search', $search, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne le tableau
		return $query->fetchAll(PDO::FETCH_ASSOC);
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

	public static function region( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `regions` WHERE `region_id` = :region');
		$query->bindParam(':region', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function departement( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `departements` WHERE `departement_id` = :departement');
		$query->bindParam(':departement', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function arrondissement( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `arrondissements` WHERE `arrondissement_id` = :id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function canton( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `cantons` WHERE `canton_id` = :id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function bureau( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `bureaux` WHERE `bureau_id` = :id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function ville_secure( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `communes` WHERE SHA2(`commune_id`, 256) = :id');
		$query->bindParam(':id', $id);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function ville( $id ) {
		return self::ville_secure(hash('sha256', $id));
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

	public static function rue( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `rues` WHERE `rue_id` = :id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function immeuble( $id ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête de recherche des informations
		$query = $link->prepare('SELECT * FROM `immeubles` WHERE `immeuble_id` = :id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les résultats
		return $query->fetch(PDO::FETCH_ASSOC);
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

	public static function afficherArrondissement( $id , $return = false ) {
		// On lance la recherche d'informations
		$arrondissement = self::arrondissement($id);
		
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

	public static function afficherCanton( $id , $return = false ) {
		// On lance la recherche d'informations
		$canton = self::canton($id);
		
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

	public static function afficherVille( $id , $return = false ) {
		// On lance la recherche d'informations
		$ville = self::ville($id);
		
		// On retourne le résultat demandé
		if ($return) : return $ville['commune_nom']; else : echo $ville['commune_nom']; endif;
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

	public static function afficherRue( $id , $return = false ) {
		// On lance la recherche d'informations
		$rue = self::rue($id);
		
		// On retourne le résultat demandé
		if ($return) : return $rue['rue_nom']; else : echo $rue['rue_nom']; endif;
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

	public static function afficherDepartement( $id , $return = false ) {
		// On lance la recherche d'informations
		$rue = self::departement($id);
		
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

	public static function afficherRegion( $id , $return = false ) {
		// On lance la recherche d'informations
		$rue = self::region($id);
		
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

	public static function afficherImmeuble( $id , $return = false ) {
		// On lance la recherche d'informations
			$immeuble = self::immeuble($id);
			
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

	public static function listeBureaux( $ville ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
			
		// On exécute la requête de récupération des immeubles correspondant
		$query = $link->prepare('SELET * FROM `bureaux` WHERE `commune_id` = :ville ORDER BY `bureau_numero`, `bureau_nom` ASC');
		$query->bindParam(':ville', $ville, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les données
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de récupérer la liste de tous les bureaux de vote connus
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @return	array	La liste des bureaux de vote connus
	 */

	public static function listeTousBureaux( ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
			
		// On exécute la requête de récupération des immeubles correspondant
		$query = $link->prepare('SELET * FROM `bureaux` ORDER `bureau_numero`, `bureau_nom` ASC');
		$query->execute();

		// On retourne les résultats
		return $query->fetchAll(PDO::FETCH_ASSOC);
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

	public static function listeRues( $ville ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
			
		// On exécute la requête de récupération des immeubles correspondant
		$query = $link->prepare('SELET * FROM `rues` WHERE `commune_id` = :ville ORDER BY `rue_nom` ASC');
		$query->bindParam(':ville', $ville, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les données
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Cette méthode permet de récupérer une liste des immeubles dans une rue demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$rue	ID de la rue demandée
	 * @return	array			La liste des immeubles dans la rue demandée
	 */

	public static function listeImmeubles( $rue ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
			
		// On exécute la requête de récupération des immeubles correspondant
		$query = $link->prepare('SELECT *, `immeuble_id` AS `id`, `immeuble_numero` AS `numero` FROM `immeubles` WHERE `rue_id` = :rue ORDER BY `immeuble_numero` ASC');
		$query->bindParam(':rue', $rue, PDO::PARAM_INT);
		$query->execute();
		
		// On récupère le résultat
		$immeubles = $query->fetchAll(PDO::FETCH_ASSOC);
		
		// Pour le tri, on retire toutes les lettres de la colonne numéro
		foreach ($immeubles as $key => $immeuble) {
			// On enregistre le numéro en retirant tout ce qui n'est pas un chiffre
			$immeubles[$key]['numero_safe'] = preg_replace('#[^0-9]#', '', $immeuble['numero']);
		}
		
		// On trie le tableau pour des résultats dans un ordre logique
		Core::triMultidimentionnel($immeubles, 'numero_safe');
		
		// On retourne le tableau trié
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

	public static function listeElecteurs( $immeuble ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');

		// On exécute la requête de récupération des électeurs correspondant
		$query = $link->prepare('SELECT * FROM `contacts` WHERE `immeuble_id` = :immeuble AND `contact_electeur` = 1 ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
		$query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne les données
		return $query->fetchAll(PDO::FETCH_ASSOC);
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

	public static function nombreElecteursParImmeuble( $immeuble ) {	
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');

		// On exécute la requête de récupération des électeurs correspondant
		$query = $link->prepare('SELECT COUNT(*) AS `nombre FROM `contacts` WHERE `immeuble_id` = :immeuble AND `contact_electeur` = 1 ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
		$query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
		$query->execute();
		
		// On récupère le nombre d'électeurs
		$nombre = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le nombre d'électeur
		return $nombre[0];
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

	public static function listeElecteursParBureau( $bureau , $coordonnees = false ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');

		// On exécute la requête de récupération des électeurs correspondant
		if ($coordonnees) {
			$query = $link->prepare('SELECT * FROM `contacts` WHERE `bureau_id` = :bureau AND ( ( contact_email IS NOT NULL AND contact_optout_email = 0 ) OR	( contact_telephone IS NOT NULL AND contact_optout_telephone = 0 ) OR ( contact_mobile IS NOT NULL AND contact_optout_mobile = 0 ) ) AND contact_optout_global = 0 ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
		} else {
			$query = $link->prepare('SELECT * FROM `contacts` WHERE `bureau_id` = :bureau ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC');
		}
		$query->bindParam(':bureau', $bureau);
		$query->execute();
		
		// On retourne les données
		return $query->fetchAll(PDO::FETCH_ASSOC);
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

	public static function bureauParImmeuble( $immeuble ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');

		// On cherche l'information dans la base de données
		$query = $link->prepare('SELECT `bureau_id` FROM `immeubles` WHERE `immeuble_id` = :immeuble');
		$query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
		$query->execute();
		
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne l'ID du bureau
		return $data[0];
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

	public static function villeParImmeuble( $immeuble ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');

		// On cherche l'information dans la base de données
		$query = $link->prepare('SELECT `rue_id` FROM `immeuble` WHERE `immeuble_id` = :immeuble');
		$query->bindParam(':immeuble', $immeuble, PDO::PARAM_INT);
		$query->execute();
		$immeuble = $query->fetch(PDO::FETCH_NUM);
		
		// On cherche l'information concernant la ville
		$query = $link->prepare('SELECT `commune_id` FROM `rues` WHERE `rue_id` = :rue');
		$query->bindParam(':rue', $immeuble[0], PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne l'ID de la ville
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet de connaître la ville correspondante à une rue
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$rue		ID de la rue concerné par la demande
	 * @return	int					ID de la ville trouvée pour l'immeuble
	 */

	public static function villeParRue( $rue ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On cherche l'information dans la base de données
		$query = $link->prepare('SELECT `commune_id` FROM `rues` WHERE `rue_id` = :rue');
		$query->bindParam(':rue', $rue, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne l'id de la ville
		return $data[0];
	}
	
	
	/**
	 * Cette méthode permet de connaître le département à partir d'une ville
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$ville		ID de la ville concerné par la demande
	 * @return	int					ID du département trouvé pour l'immeuble
	 */

	public static function departementParVille( $ville ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On cherche l'information dans la base de données
		$query = $link->prepare('SELECT `departement_id` FROM `communes` WHERE `commune_id` = :id');
		$query->bindParam(':id', $ville, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne l'id du département
		return $data[0];
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

	public static function cantonParImmeuble( $immeuble ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On cherche l'information dans la base de données
		$query = $link->prepare('SELECT `canton_id` FROM `immeubles` WHERE `immeuble_id` = :id');
		$query->bindParam(':id', $immeuble, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne l'id du canton
		return $data[0];
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

	public static function detailAdresse( $immeuble ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On récupère les données sur l'immeuble
		$query = $link->prepare('SELECT * FROM `immeubles` WHERE `immeuble_id` = :id');
		$query->bindParam(':id', $immeuble, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_ASSOC);

		// On récupère les données sur la rue
		$query = $link->prepare('SELECT * FROM `rues` WHERE `rue_id` = :id');
		$query->bindParam(':id', $data['rue_id'], PDO::PARAM_INT);
		$query->execute();
		$data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

		// On récupère les données sur la ville
		$query = $link->prepare('SELECT * FROM `communes` WHERE `commune_id` = :id');
		$query->bindParam(':id', $data['commune_id'], PDO::PARAM_INT);
		$query->execute();
		$data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));
		
		// On récupère les données sur le code postal
		$query = $link->prepare('SELECT * FROM `codes_postaux` WHERE `commune_id` = :id');
		$query->bindParam(':id', $data['commune_id'], PDO::PARAM_INT);
		$query->execute();
		$data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));

		// On récupère les données sur le département
		$query = $link->prepare('SELECT * FROM `departements` WHERE `departement_id` = :id');
		$query->bindParam(':id', $data['departement_id'], PDO::PARAM_INT);
		$query->execute();
		$data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));
		
		// On récupère les données sur la région
		$query = $link->prepare('SELECT * FROM `regions` WHERE `region_id` = :id');
		$query->bindParam(':id', $data['region_id'], PDO::PARAM_INT);
		$query->execute();
		$data = array_merge($data, $query->fetch(PDO::FETCH_ASSOC));
		
		// On retourne le tableau complet
		return $data;
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

	public static function adressePostale( $immeuble , $separateur = '<br>' , $return = false ) {
		// On récupère les informations liées à l'adresse de l'immeuble demandée
		$informations = self::detailAdresse( $immeuble );

		// On formate les composants de l'adresse correctement
		$adresse['numero'] = $informations['immeuble_numero'];
		$adresse['rue'] = mb_convert_case($informations['rue_nom'], MB_CASE_TITLE);
		$adresse['cp'] = $informations['code_postal'];
		$adresse['ville'] = mb_convert_case($informations['commune_nom'], MB_CASE_UPPER);
		
		// On prépare la variable d'affichage du rendu
		$affichage = $adresse['numero'] . ' ';
		
		// On affiche conditionnement la suite de l'adresse
		if (!empty($adresse['rue'])) $affichage .= $adresse['rue'] . $separateur;
		if (!empty($adresse['cp'])) $affichage .= $adresse['cp'] . ' ';
		if (!empty($adresse['ville'])) $affichage .= $adresse['ville'] . $separateur;

		// On remet en forme l'affichage
		$affichage = Core::tpl_transform_texte($affichage);
		
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

	public static function bureauDeVote( $immeuble , $return = false , $mini = false ) {
		// On récupère toutes les informations nécessaires par rapport à cet immeuble et donc son bureau de vote
		$informations = self::detailAdresse( $immeuble );

		// On retraite les informations
		$bureau['numero'] = $informations['bureau_numero'];
		$bureau['nom'] = mb_convert_case($informations['bureau_nom'], MB_CASE_TITLE);
		$bureau['ville'] = mb_convert_case($informations['commune_nom'], MB_CASE_UPPER);
		
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
	 * @param	int		$ville      ID de la ville dans laquelle se trouve la rue
	 * @param	string 	$rue        Nom de la rue à ajouter dans la base de données
	 * @param   string  $immeuble   Numéro de l'immeuble à créer
	 * @return	int	                ID de la rue ajoutée
	 */

	public static function ajoutRue( $ville , $rue ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On exécute la requête SQL
		$query = $link->prepare('INSERT INTO `rues` (`commune_id`, `rue_nom`) VALUES (:ville, :rue)');
		$query->bindParam(':ville', $ville, PDO::PARAM_INT);
		$query->bindParam(':rue', $rue, PDO::PARAM_STR);
		$query->execute();
		
		// On retourne l'identifiant des informations insérées
		return $link->lastInsertId();
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

	public static function ajoutImmeuble( $infos ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		if (isset($infos['rue'], $infos['numero'])) {
			
			// On exécute la requête
			$query = $link->prepare('INSERT INTO `immeubles` (`rue_id`, `immeuble_numero`) VALUES (:rue, :numero)');
			$query->bindParam(':rue', $infos['rue'], PDO::PARAM_INT);
			$query->bindParam(':numero', $infos['numero'], PDO::PARAM_STR);
			$query->execute();
			
			// On retourne le numéro de l'entrée insérée
			return $link->lastInsertId();
			
		}
		else { return false; }
	}
	
	
	/**
	 * Cette méthode permet d'estimer le nombre d'électeur pour un découpage géographique demandé
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$branche		Découpage géographique concerné par l'estimation
	 * @param	int	 	$id				ID du découpage géographique concerné par l'estimation
	 * @param	string	$coordonnees 	Permet de restreindre l'estimation aux électeurs dont 
	 *									certaines coordonnées sont connues
	 * @return	int						Nombre d'électeur trouvé
	 */

	public static function nombreElecteurs( $branche , $id , $coordonnees = null ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On recherche tous les immeubles si la branche est un bureau
		if ($branche == 'bureau') {
			if (!is_null($coordonnees)) {
				$query = $link->prepare('SELECT COUNT(*) FROM `contacts` WHERE ( contacts.contact_' . $coordonnees . ' IS NOT NULL AND contact_optout_' . $coordonnees . ' = 0 ) AND `bureau_id` = :id');
			} else {
				$query = $link->prepare('SELECT COUNT(*) FROM `contacts` WHERE `contact_electeur` = 1 AND `bureau_id` = :id');
			}
			$query->bindParam(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$data = $query->fetch(PDO::FETCH_NUM);
			$nombre = $data[0];
		} 
		
		// On recherche tous les immeubles si la branche est une ville
		else {
			// On exécute la requête de recherche de toutes les rues de la commune
			$query = $link->prepare('SELECT `rue_id` FROM `rues` WHERE `commune_id` = :id');
			$query->bindParam(':id', $id, PDO::PARAM_INT);
			$query->execute();
			$rues = $query->fetchAll(PDO::FETCH_NUM);
			
			// On formate la liste des rues pour l'insérer dans la recherche SQL des immeubles
			$ids = array(); // Liste des ids des rues de la commune
			foreach ($rues as $rue) { $ids[] = $rue[0]; }
			$rues = implode(',', $ids);

			// On recherche tous les immeubles de chaque rue de cette commune
			$query = $link->query('SELECT `immeuble_id` FROM `immeubles` WHERE `rue_id` IN (' . $rues . ')');
			$immeubles = $query->fetchAll(PDO::FETCH_NUM);

			// On formate la liste des rues pour l'insérer dans la recherche SQL des électeurs
			$ids = array(); // Liste des ids des rues de la commune
			foreach ($immeubles as $immeuble) { $ids[] = $immeuble[0]; }
			$immeubles = implode(',', $ids);

			// On recherche le nombre de contacts, électeurs, dans les immeubles en question
			if (!is_null($coordonnees)) {
				$query = $link->query('SELECT COUNT(*) FROM `contacts` WHERE ( contacts.contact_' . $coordonnees . ' IS NOT NULL AND contact_optout_' . $coordonnees . ' = 0 ) AND `immeuble_id` IN (' . $immeubles . ')');
			} else {
				$query = $link->query('SELECT COUNT(*) FROM `contacts` WHERE `contact_electeur` = 1 AND `immeuble_id` IN (' . $immeubles . ')');
			}
			$data = $query->fetch(PDO::FETCH_NUM);
			$nombre = $data[0];
		}
		
		// On retourne le résultat
		if ($nombre) {
			return $nombre;
		} else {
			return 0;
		}
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

	public static function coordonneesDansImmeuble( $immeuble ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On recherche le nombre de contacts recueillis dans l'immeuble
		$query = $link->prepare('SELECT COUNT(*) FROM `contacts` WHERE ( (`contact_email` IS NOT NULL AND `contact_optout_email` = 0) OR (`contact_telephone` IS NOT NULL AND `contact_optout_telephone` = 0) OR (`contact_mobile` IS NOT NULL AND `contact_optout_mobile` = 0) ) AND `contact_optout_global` = 0 AND `immeuble_id` = :id');
		$query->bindParam(':id', $immeuble, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le résultat
		return $data[0];
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

	public static function coordonneesDansBureau( $bureau ) {
		// On lance la connexion à la base de données
		$link = Configuration::read('db.link');
		
		// On recherche le nombre de contacts recueillis dans l'immeuble
		$query = $link->prepare('SELECT COUNT(*) FROM `contacts` WHERE ( (`contact_email` IS NOT NULL AND `contact_optout_email` = 0) OR (`contact_telephone` IS NOT NULL AND `contact_optout_telephone` = 0) OR (`contact_mobile` IS NOT NULL AND `contact_optout_mobile` = 0) ) AND `contact_optout_global` = 0 AND `bureau_id` = :id');
		$query->bindParam(':id', $bureau, PDO::PARAM_INT);
		$query->execute();
		$data = $query->fetch(PDO::FETCH_NUM);
		
		// On retourne le résultat
		return $data[0];
	}
}
?>