<?php

/*
	Classe du système de gestion des utilisateurs
*/


class user extends core {
	
	// Définition des propriétés
	private $db; // Lien à la base de données
	private $noyau; // Lien vers la base de données centrale du système
	private $url; // le nom de domaine du serveur utilisé
	private	$user; // Informations liées à l'utilisateur
	
	
	// Définition des méthodes	
	
	// Méthode permettant de vérifier si un utilisateur est connecté ou non
	
	public	function __construct($db, $noyau, $url) {
		$this->db = $db;
		$this->noyau = $noyau;
		$this->url = $url;
	}
	
	public	function statut_connexion() {
		if (isset($_COOKIE['leqg-user'])) {
			// La connexion existe, on construit les propriétés
			$query = "SELECT * FROM users WHERE user_id = " . $_COOKIE['leqg-user'];
			$sql = $this->noyau->query($query);
			$donnees = $sql->fetch_assoc();
			
			// On vérifie si une demande de réinitialisation de la connexion n'a pas été demandée
			if (strtotime($donnees['user_lasttime']) >= strtotime($donnees['user_reinit'])) {
				
				// On prépare le tableau des informations
				$user = $this->formatage_donnees($donnees);
				
				// On y supprime le mot de passe crypté et la date de dernière réinitialisation
				unset($user['password']);
				unset($user['reinit']);
				
				// On retourne le reste des informations dans les paramètres globaux de la classe
				$this->user = $user;
				
				return true;
				
			} else {
				// Dans ce cas, si une réinitialisation a été demandée depuis, on demande la déconnexion et on renvoit sur la page d'accueil
				$this->deconnexion();
				$this->tpl_go_to(true);
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	// Méthodes permettant d'afficher les données de l'utilisateur
	
	public	function get_the_id() { return $this->user['id']; }
	public	function the_id() { echo $this->get_the_id(); }
	
	public	function get_the_login() { return $this->user['email']; }
	public	function the_login() { echo $this->get_the_login(); }
	
	public	function get_the_nickname() { return $this->user['firstname'] . ' ' . $this->user['lastname']; }
	public	function the_nickname() { echo $this->get_the_nickname(); }
	
	public	function get_the_email() { return $this->user['email']; }
	public	function the_email() { echo $this->get_the_email(); }
	
	public	function get_the_phone($espaces = false) { if ($espaces) { return $this->tpl_phone($this->user['phone']); } else { return $this->user['phone']; } }
	public	function the_phone($espaces = false) { echo $this->get_the_phone($espaces); }
	
	
	// Méthode de vérification de l'exitence d'un login 
	
	private	function existence_login($login) {
		$query = "SELECT user_email FROM users WHERE user_email = '" . $login . "'";
		$sql = $this->noyau->query($query);
		$nb_login = $sql->num_rows;
		
		if ($nb_login == 1) : return true; else : return false; endif;
	}
	
	
	// Méthode permettant d'encrypter les mots de passe
	
	public	function encrypt_pass($pass) {
		return md5(sha1(md5(md5($pass))));
	}
	
	
	// Méthode permettant de comparer un mot de passe avec celui de l'utilisateur demandé (login)
	
	public	function verification_pass($pass, $login) {
		// On recherche le mot de passe du compte demandé
		$query = "SELECT user_password FROM users WHERE user_email = '" . $login . "'";
		$sql = $this->noyau->query($query);
		$donnees = $sql->fetch_assoc();
		
		$pass_envoye = $this->encrypt_pass($pass);
		
		if ($donnees['user_password'] == $pass_envoye) : return true; else : return false; endif;
	}
	
	
	// Méthode permettant de trouver l'ID d'un utilisateur à partir de son login
	
	public	function get_id_by_login($login) {
		$query = "SELECT user_id FROM users WHERE user_email = '" . $login . "'";
		$sql = $this->noyau->query($query);
		$donnees = $sql->fetch_assoc();
		
		return $donnees['user_id'];
	}
	
	
	// Méthode permettant de trouver le login d'un utilisateur à partir de son ID
	
	public	function get_login_by_ID($id) {
		$query = "SELECT * FROM users WHERE user_id = '" . $id . "'";
		$sql = $this->noyau->query($query);
		$donnees = $sql->fetch_assoc();
		
		return $donnees['user_firstname'] . ' ' . $donnees['user_lastname'];
	}
	
	
	// Méthode de connexion des comptes à l'interface
	
	public	function connexion($login, $pass) {
		// On vérifie si le login existe
		if ($this->existence_login($login)) {
			if ($this->verification_pass($pass, $login)) {
				// Dans ce cas, on créé le cookie et on envoit vers la page index.php
				$id_user = $this->get_id_by_login($login);
				$expire = time()+60*60*24*5; // Le cookie expire dans 5 jours
				
				// On lance le cookie
				setcookie('leqg-user', $id_user, $expire);
				
				// On défini le timestamp dans la base de données
				$this->noyau->query('UPDATE users SET user_lasttime = NOW() WHERE user_id = ' . $id_user);
				
				$this->tpl_go_to(true);
			}
			else {
				$this->tpl_go_to('login', array( 'erreur' => 'pass' ), true);
			}
		}
		else {
			$this->tpl_go_to('login', array( 'erreur' => 'login' ), true);
		}
	}
	
	
	// méthode de déconnexion des comptes à l'interface
	
	public	function deconnexion() {
		// On détruit tout simplement le cookie
		
		if (setcookie('leqg-user', 0, time()-1)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	// méthode de demande de rénitialisation de toutes les connexions au compte
	
	public	function reinitialisation( $compte ) {
		if (!is_numeric($compte)) return false;
		
		// On lance une demande de réinitialisation à l'instant T
		$this->noyau->query('UPDATE users SET user_reinit = NOW() WHERE user_id = ' . $compte);
		
		$this->tpl_go_to(true);
	}
	
	
	// client( ) est une méthode permettant de renvoyer les informations du compte client pour un utilisateur demandé
	public	function client( $user = null ) {
		if (is_null($user)) $user = $_COOKIE['leqg-user'];
		
		$query = 'SELECT * FROM users WHERE user_id = ' . $user;
		$sql = $this->noyau->query($query);
		$infos = $sql->fetch_assoc();
		
		$query = 'SELECT * FROM clients WHERE client_id = ' . $infos['client_id'];
		$sql = $this->noyau->query($query);
		
		return $this->formatage_donnees($sql->fetch_assoc());
	}
	
	
	// liste( ) est une méthode renvoyant la liste des comptes associés au compte en cours
	public	function liste() {
		$compte = $this->client();
		$compte = $compte['id'];
		
		$query = 'SELECT * FROM users WHERE client_id = ' . $compte . ' ORDER BY user_firstname, user_lastname ASC';
		$sql = $this->noyau->query($query);
		
		$users = array();
		
		while ($row = $sql->fetch_assoc()) $users[] = $this->formatage_donnees($row);
		
		return $users; 
	}
}

?>