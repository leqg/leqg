<?php

/*
	Classe du système de gestion des utilisateurs
*/


class User {
		
	/**
	 * Protège en page en vérifiant la connexion et les droits
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param   string  $cookie  Cookie à vérifier
	 * @param   string  $domaine Domaine de connexion ($_SERVER['SERVER_NAME'])
	 * @param   string  $auth    Niveau d'autorisation minimal demandé
	 *
	 * @return	bool    Statut du cookie (true or false)
	 */
	
	public static function protection($auth = 1) {
		// On prépare la connexion à la BDD
		$link = Configuration::read('db.core');
		
		// On regarde si un cookie existe
		if (isset($_COOKIE['leqg'], $_COOKIE['time']) && !empty($_COOKIE['time']) && !empty($_COOKIE['leqg'])) {
			
			// On recherche l'existence du compte
			$query = $link->prepare('SELECT `client`, `auth_level`, `last_reinit` FROM `user` WHERE SHA2(`id`, 256) = :cookie');
			$query->bindParam(':cookie', $_COOKIE['leqg']);
			$query->execute();
			
			// Si un compte existe
			if ($query->rowCount() == 1)
			{
				// On récupère les informations liées au cookie
				$infos = $query->fetch(PDO::FETCH_ASSOC);
				
				// On vérifie si l'utilisateur est sur le bon serveur
				if ($_SERVER['SERVER_NAME'] == $infos['client'] . '.leqg.info' || ($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == '127.0.0.1')) {
				
					// On vérifie que l'utilisateur dispose des droits suffisant pour cette page
					if ($infos['auth_level'] >= $auth) {
						return true;
					}
					
					// Sinon, on redirige vers l'accueil de son compte
					else {
						header('Location: http://' . $infos['client'] . '.leqg.info');
					}
					
				}
				
				// Sinon, on redirige vers son serveur
				else {
					header('Location: http://' . $infos['client'] . '.leqg.info' . $_SERVER['PHP_SELF']);
				}
			}
			
			// Si aucun compte n'existe, on détruit le cookie et on redirige vers le login
			else {
				setcookie('leqg', null, time(), '/', 'leqg.info');
				setcookie('time', null, time(), '/', 'leqg.info');
				header('Location: http://auth.leqg.info');
			}
		}
		
		// Sinon, on supprime au cas où ce cookie et on redirige
		else {
			setcookie('leqg', null, time(), '/', 'leqg.info');
			setcookie('time', null, time(), '/', 'leqg.info');
			header('Location: http://auth.leqg.info');
		}	
	}
	
	
	/**
	 * Détermine le niveau d'accréditation du compte connecté
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @return  int   Niveau d'accréditation du compte connecté
	 */
	
	public static function auth_level() {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		
		// On vérifie s'il existe un cookie
		if (isset($_COOKIE['leqg']) && !empty($_COOKIE['leqg'])) {
			
			// On effectue la recherche du niveau d'accréditation
			$query = $link->prepare('SELECT `auth_level` FROM `user` WHERE SHA2(`id`, 256) = :cookie');
			$query->bindParam(':cookie', $_COOKIE['leqg']);
			$query->execute();
			
			// On vérifie qu'il n'y a qu'un compte qui correspond au cookie
			if ($query->rowCount() == 1) {
				// On retourne le niveau d'accréditation
				$accreditation = $query->fetch(PDO::FETCH_NUM);
				return $accreditation[0];
			}
			
			// Sinon, le niveau d'accréditation est nul
			else { return 0; }
			
		}
		
		// Sinon le niveau d'accréditation est nul
		else { return 0; }
	}
	
	
	/**
	 * Récupère l'ID en clair de la personne connectée
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @return  int   ID de la personne connectée
	 */
	
	public static function ID() {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		
		// On vérifie s'il existe un cookie
		if (isset($_COOKIE['leqg']) && !empty($_COOKIE['leqg'])) {
			
			// On effectue la recherche de l'ID en clair
			$query = $link->prepare('SELECT `id` FROM `user` WHERE SHA2(`id`, 256) = :cookie');
			$query->bindParam(':cookie', $_COOKIE['leqg']);
			$query->execute();
			
			// On vérifie qu'il n'y a qu'un compte qui correspond au cookie
			if ($query->rowCount() == 1) {
				// On retourne l'ID en clair
				$id = $query->fetch(PDO::FETCH_NUM);
				return $id[0];
			}
			
			// Sinon, l'ID est nul
			else { return 0; }
			
		}
		
		// Sinon l'ID est nul
		else { return 0; }
	}
	
	
	/**
	 * Liste les comptes associé au client
	 *
	 * @author  Damien Senger
	 * @version 1.0
	 * 
	 * @param   int     $auth_level     Niveau d'accréditation minimal des personnes recherchées
	 * 
	 * @return  array   tableau comprenant les informations sur les différents comptes du client
	 */
	
	public static function liste( $auth_level = 5 ) {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		$client = Configuration::read('ini')['LEQG']['compte'];
		Core::debug($client);
		// On effectue la recherche
		$query = $link->prepare('SELECT `id`, `email`, `firstname`, `lastname`, `telephone` FROM `user` WHERE `client` = :client AND `auth_level` >= :authlevel AND `auth_level` < 9 ORDER BY `firstname`, `lastname` ASC');
		$query->bindParam(':client', $client);
		$query->bindParam(':authlevel', $auth_level, PDO::PARAM_INT);
		$query->execute();
		
		// On retourne la tableau contenant les informations
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Récupère le login d'un compte selon son ID
	 *
	 * @author  Damien Senger
	 * @version 1.0
	 * 
	 * @param   int     $id     Identifiant du compte dont nous souhaitons récupérer le nom
	 * 
	 * @return  string          Nom du compte
	 */
	 
	public static function get_login_by_ID( $id ) {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		
		// On effectue la recherche
		$query = $link->prepare('SELECT `firstname`, `lastname` FROM `user` WHERE `id` = :id');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->execute();
		
		// On récupère les résultats et on affiche le nom et le prénom
		$data = $query->fetch(PDO::FETCH_NUM);
		return mb_convert_case($data[0], MB_CASE_TITLE) . ' ' . mb_convert_case($data[1], MB_CASE_UPPER);
	}
	
	
	/**
	 * Récupère les infos selon un ID
	 *
	 * @author  Damien Senger
	 * @version 1.0
	 * 
	 * @param   int     $id     Identifiant du compte dont nous souhaitons récupérer les infos
	 * 
	 * @return  array           Informations
	 */
	 
	public static function infos( $id ) {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		$client = Configuration::read('ini')['LEQG']['compte'];
		
		// On effectue la recherche
		$query = $link->prepare('SELECT * FROM `user` WHERE `id` = :id AND `client` = :client');
		$query->bindParam(':id', $id, PDO::PARAM_INT);
		$query->bindParam(':client', $client);
		$query->execute();
		
		// On récupère les résultats et on affiche le nom et le prénom
		if ($query->rowCount()) {
    		return $query->fetch(PDO::FETCH_ASSOC);
		} else {
    		return false;
		}
	}
	
	
	/**
	 * Déconnecte l'utilisateur actuel
	 *
	 * @author  Damien Senger
	 * @version 1.0
	 * 
	 * @return  string          Nom du compte
	 */
	
	public static function logout() {
		setcookie('leqg', 0, time());
		setcookie('time', 0, time());
		
		header('Location: http://auth.leqg.info/');
	}
	
	
	/**
     * Charge la liste des comptes du client sauf ceux listés
	 *
	 * @author  Damien Senger
	 * @version 1.0
	 * 
	 * @param   array           Liste des personnes à ne pas récupérer
	 * @return  array           Liste des personnes demandées
	 */
    
    public static function sauf( $sauf ) {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		$client = Configuration::read('ini')['LEQG']['compte'];
		
		if (empty($sauf)) {
    		$query = $link->prepare('SELECT `id`, `email`, `firstname`, `lastname`, `telephone` FROM `user` WHERE `client` = :client');
		}
		
		else {
    		$notUsers = implode(',', $sauf);
    		$query = $link->prepare('SELECT `id`, `email`, `firstname`, `lastname`, `telephone` FROM `user` WHERE `client` = :client AND `id` NOT IN (' . $notUsers . ')');
		}
		
		$query->bindParam(':client', $client);
		$query->execute();
		
		// On retourne la tableau contenant les informations
		return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public static function auth_lvl() {
		// On prépare le lien à la base de données centrale
		$link = Configuration::read('db.core');
		$client = Configuration::read('ini')['LEQG']['compte'];
		$user = $_COOKIE['leqg'];

		// On recherche l'existence du compte
		$query = $link->prepare('SELECT `client`, `auth_level`, `last_reinit` FROM `user` WHERE SHA2(`id`, 256) = :cookie');
		$query->bindParam(':cookie', $user);
		$query->execute();
        $compte = $query->fetch(PDO::FETCH_ASSOC);
        
        return $compte['auth_level'];
    }

}

?>
