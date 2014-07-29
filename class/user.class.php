<?php

/*
	Classe du système de gestion des utilisateurs
*/


class user extends core {
	
	// Définition des propriétés
	private $db; // Lien à la base de données
	private	$user; // Informations liées à l'utilisateur
	
	
	// Définition des méthodes	
	
	// Méthode permettant de vérifier si un utilisateur est connecté ou non
	
	public	function __construct($db) {
		$this->db = $db;
	}
	
	public	function statut_connexion() {
		if (isset($_COOKIE['leqg-user'])) {
			// La connexion existe, on construit les propriétés
			$query = "SELECT * FROM compte WHERE id = '" . $_COOKIE['leqg-user'] . "'";
			$sql = $this->db->query($query);
			$donnees = $sql->fetch_assoc();
			
			// On vérifie si une demande de réinitialisation de la connexion n'a pas été demandée
			if ($_COOKIE['leqg-date'] >= strtotime($donnees['demande_reinitialisation'])) {
				
				$user = array(	'id'		=> $donnees['id'] ,
								'login'		=> $donnees['login'] ,
								'nickname'	=> $donnees['nickname'] ,
								'email'		=> $donnees['email'] ,
								'phone'		=> $donnees['phone'] );
				
				$this->user = $user;
				
			return true;
			} else {
				// Dans ce cas, si une réinitialisation a été demandée depuis, on demande la déconnexion et on renvoit sur la page d'accueil
				$this->deconnexion();
				$this->tpl_redirection();
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	// Méthodes permettant d'afficher les données de l'utilisateur
	
	public	function the_id() { echo $this->user['id']; }
	public	function get_the_id() { return $this->user['id']; }
	
	public	function the_login() { echo $this->user['login']; }
	public	function get_the_login() { return $this->user['login']; }
	
	public	function the_nickname() { echo $this->user['nickname']; }
	public	function get_the_nickname() { return $this->user['nickname']; }
	
	public	function the_email() { echo $this->user['email']; }
	public	function get_the_email() { return $this->user['email']; }
	
	public	function the_phone($espaces = false) { if ($espaces) { echo $this->tpl_phone($this->user['phone']); } else { echo $this->user['phone']; } }
	public	function get_the_phone($espaces = false) { if ($espaces) { return $this->tpl_phone($this->user['phone']); } else { return $this->user['phone']; } }
	
	
	// Méthode de vérification de l'exitence d'un login 
	
	private	function existence_login($login) {
		$query = "SELECT login FROM compte WHERE login = '" . $login . "'";
		$sql = $this->db->query($query);
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
		$query = "SELECT pass FROM compte WHERE login = '" . $login . "'";
		$sql = $this->db->query($query);
		$donnees = $sql->fetch_assoc();
		
		$pass_envoye = $this->encrypt_pass($pass);
		
		if ($donnees['pass'] == $pass_envoye) : return true; else : return false; endif;
	}
	
	
	// Méthode permettant de trouver l'ID d'un utilisateur à partir de son login
	
	public	function get_id_by_login($login) {
		$query = "SELECT id FROM compte WHERE login = '" . $login . "'";
		$sql = $this->db->query($query);
		$donnees = $sql->fetch_assoc();
		
		return $donnees['id'];
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
				setcookie('leqg-date', time(), $expire);  // cookie de sécurité, pour permettre la réinitialisation à distance
				
				// On défini le timestamp dans la base de données
				$this->db->query('UPDATE compte SET derniere_connexion = NOW() WHERE compte.id = ' . $id_user);
				
				$this->tpl_redirection();
			}
			else {
				$this->tpl_redirection('login', 'pass', 'erreur');
			}
		}
		else {
			$this->tpl_redirection('login', 'login', 'erreur');
		}
	}
	
	
	// méthode de déconnexion des comptes à l'interface
	
	public	function deconnexion() {
		// On détruit tout simplement le cookie
		
		if (setcookie('leqg-user', 0, time()-1)) {
			setcookie('leqg-date', 0, time()-1);
			return true;
		} else {
			return false;
		}
	}
	
	
	// méthode de demande de rénitialisation de toutes les connexions au compte
	
	public	function reinitialisation() {
		// On lance une demande de réinitialisation à l'instant T
		$this->db->query('UPDATE compte SET demande_reinitialisation = NOW() WHERE compte.id = ' . $_COOKIE['leqg-user']);
		
		$this->redirection();
	}
}

?>