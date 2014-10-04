<?php
/**
 * Cette classe comprend l'ensemble des méthodes de traitement des fiches contact sur le SaaS LeQG
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */


class fiche extends core {
	
	/**
	 * @var	object	$db			Propriété concenant le lien vers la base de données de l'utilisateur
	 * @var	string	$url		Propriété contenant l'URL du serveur
	 * @var	string	$compte		Propriété contenant les informations sur le compte connecté
	 * @var array	$fiches		Tableau des informations disponibles à propos des fiches ouvertes
	 */
	private $db, $url, $compte, $fiches;

	/** @var array	$fiche_ouverte	Talbeau des informations disponibles à propos de la fiche ouverte */
	public	$fiche_ouverte = null;
	

	/**
	 * Cette méthode permet la construction de la classe dossier
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
	 * Cette méthode permet de récupérer toutes les informations sur une fiche contact sans forcément l'ouvrir dans le template
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	int		$contact	ID de la fiche contact à consulter
	 * @return	array				Tableau des informations concernant la fiche contact demandée
	 */

	public	function informations( int $contact ) {
		if (!is_numeric($contact)) return false;
		
		// On prépare la requête de récupération des informations
		$query = 'SELECT	*
				  FROM		contacts
				  WHERE		contact_id = ' . $contact;
				  
		// On effectue la requête et on retourne le tableau des résultats s'il existe un résultat
		$sql = $this->db->query($query);
		
		if ($sql->num_rows == 1) return $this->formatage_donnees($sql->fetch_assoc());
		
		// Sinon on retourne une erreur
		return false;
	}
	

	/**
	 * Cette méthode permet d'accéder aux informations d'une fiche existante
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		int			$id			ID de la fiche contact à laquelle on souhaite accéder
	 * @param		bool		$ouverture	Si true, déclenche l'ouverture de la fiche en question
	 * @return		bool					True si l'accès a été possible, False sinon
	 */

	public	function acces($id, $ouverture = false) {
		$query = "SELECT * FROM contacts WHERE contact_id = " . $id;
		$sql = $this->db->query($query);
		
		// S'il existe une fiche, au moins
		if ($sql->num_rows >= 1) {
			$donnees = $sql->fetch_assoc();
			
			$this->fiches[$id] = $donnees;
		
			if ($ouverture == true) { // Si on ouvre la fiche de suite
				$this->fermeture();
				$this->fiche_ouverte = $donnees;
			}
			
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet d'ouvrir une fiche existante
	 *
	 * Cette méthode permet de transférer les informations d'une fiche existante dans la propriété $fiche_ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		int			$id			ID de la fiche contact à ouvrir
	 * @return		void
	 */

	public function ouverture($id) {
		// On commence par purger toute fiche ouverte
		unset($this->fiche_ouverte);
		
		// On regarde si la fiche n'a pas déjà été recherchée dans la base de données
		if ($this->fiches[$id]) { $this->fiche_ouverte = $this->fiches[$id]; }
		else { $this->acces($id, true); }
	}
	

	/**
	 * Cette méthode permet de fermer une fiche existante
	 *
	 * Cette méthode permet de purger les informations contenues dans le propriété $fiche_ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		void
	 */

	public function fermeture() { $this->fiche_ouverte = NULL; }
	

	/**
	 * Cette méthode permet d'afficher des informations au sujet de la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$colonne		Information à retrouver
	 * @param		bool		$prefix			Si true, l'information à retrouver entrée ne possède pas le préfixe du nom de la table
	 * @return		void
	 */

	public	function infos($colonne, $prefix = true) { if ($prefix) { echo utf8_encode($this->fiche_ouverte['contact_' . $colonne]); } else { echo utf8_encode($this->fiche_ouverte[$colonne]); } }
	

	/**
	 * Cette méthode permet de récupérer des informations au sujet de la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$colonne		Information à retrouver
	 * @param		bool		$prefix			Si true, l'information à retrouver entrée ne possède pas le préfixe du nom de la table
	 * @return		string						Retourne l'information demandée
	 */

	public	function get_infos($colonne, $prefix = true) { if ($prefix) { return utf8_encode($this->fiche_ouverte['contact_' . $colonne]); } else { return utf8_encode($this->fiche_ouverte[$colonne]); } }
	

	/**
	 * Cette méthode permet de retourner l'ID de l'immeuble de l'adresse électorale de l'électeur, très important pour l'accès aux fonctions cartographiques
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		int			ID de l'immeuble
	 */

	public	function get_adresse() {
		return $this->fiche_ouverte['adresse_id'];
	}
	

	/**
	 * Cette méthode permet de retourner l'ID de l'immeuble de l'adresse connue de l'électeur, très important pour l'accès aux fonctions cartographiques
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		int			ID de l'immeuble
	 */

	public	function get_immeuble() {
		return $this->fiche_ouverte['immeuble_id'];
	}
	

	/**
	 * Cette méthode permet de savoir si l'immeuble est bien rattaché à une rue, et donc savoir si l'adresse existe vraiment
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		bool		True si la rue existe, False si la rue n'existe pas réellement
	 */

	public	function is_adresse_fichier() {
		$immeuble = $this->get_immeuble();
		
		// On cherche les informations sur la rue
		$query = 'SELECT * FROM `immeubles` LEFT JOIN `rues` ON `rues`.`rue_id` = `immeubles`.`rue_id` WHERE `immeuble_id` = ' . $immeuble;
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
		
		return (!is_null($row['rue_nom'])) ? true : false;
	}
	

	/**
	 * Cette méthode permet de savoir si une information existe
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		bool		True si l'information existe, False si elle n'existe pas réellement
	 */

	public	function is_info($colonne) {
		return (!empty($this->get_infos($colonne)) && $this->get_infos($colonne) != 0) ? true : false;
	}
	

	/**
	 * Cette méthode permet de récupérer des informations autour des optout de la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$methode		Permet de définir pour quel mode de contact on demande le statut de l'optout
	 * @return		bool						True si l'optout a été demandé, False s'il n'a pas été demandé
	 */

	public	function optout($methode = null) {
		if (empty($methode)) {
			if ($this->fiche_ouverte['contact_optout_global']) : return true; else : return false; endif;
		} else {
			if ($this->fiche_ouverte['contact_' . $methode . '_optout']) : return true; else : return false; endif;
		}
	}
	

	/**
	 * Cette méthode permet de retourner l'ID de la fiche consultée indépendamment de la recherche de paramètre GET
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		int			ID de la fiche consultée
	 */

	public	function get_the_ID() {
		if (is_null($this->fiche_ouverte)) : return false; else :
			return $this->get_infos('id');
		endif;
	}
	

	/**
	 * Cette méthode permet d'afficher l'ID de la fiche consultée indépendamment de la recherche de paramètre GET
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @return		bool|void	Si erreur, retourne un booléen sinon rien.
	 */

	public	function the_ID() {
		if (is_null($this->fiche_ouverte)) : return false; else :
			echo $this->get_the_ID();
		endif;
	}
	

	/**
	 * Cette méthode permettant de calculer le rendu du nom de la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$separateur		Séparateur HTML demandé pour séparer les éléments du nom de la fiche ouverte
	 * @param		bool		$return			True pour retourner le nom, False pour afficher le nom
	 * @return		string|void					Retour ou affichage des informations suivant $return
	 */

	public	function affichage_nom($separateur = null, $return = false) {
		$nom = $this->get_infos('nom'); 
		$nom_usage = $this->get_infos('nom_usage');
		$prenoms = $this->get_infos('prenoms');
	
		if ($separateur) { $begin = '<' . $separateur . '>'; $end = '</' . $separateur . '>'; }
		else { $begin = null; $end = null; }
	
		if (!empty($nom)) { $affichage = $begin . mb_convert_case(html_entity_decode($nom, ENT_NOQUOTES, 'utf-8'), MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($nom_usage)) { $affichage .= ' ' . $begin . mb_convert_case(html_entity_decode($nom_usage, ENT_NOQUOTES, 'utf-8'), MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($prenoms)) { $affichage .= ' ' . $begin . mb_convert_case(html_entity_decode($prenoms, ENT_NOQUOTES, 'utf-8'), MB_CASE_TITLE, 'utf-8') . $end; }
		
		if ($return == false) : echo $affichage; else : return $affichage; endif;
		
		unset($affichage);
	}
	

	/**
	 * Cette méthode permet de retourner le nom mis en forme de la fiche demandée d'après son ID
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		int			$id				ID de la fiche demandée
	 * @param		string		$separateur		Séparateur HTML demandé pour séparer les éléments du nom de la fiche demandée d'après son ID
	 * @param		bool		$return			True pour retourner le nom, False pour afficher le nom
	 * @return		string|void					Retour ou affichage des informations suivant $return
	 */

	public	function affichageNomByID($id, $separateur = null, $return = false) {
		// On récupère les information
		$query = 'SELECT * FROM contacts WHERE contact_id = ' . $id;
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
	
		$nom = $row['contact_nom']; 
		$nom_usage = $row['contact_nom_usage'];
		$prenoms = $row['contact_prenoms'];
	
		if ($separateur) { $begin = '<' . $separateur . '>'; $end = '</' . $separateur . '>'; }
		else { $begin = null; $end = null; }
	
		$affichage = '';
		if (!empty($nom)) { $affichage .= $begin . mb_convert_case(html_entity_decode($nom, ENT_NOQUOTES, 'utf-8'), MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($nom_usage)) { $affichage .= ' ' . $begin . mb_convert_case(html_entity_decode($nom_usage, ENT_NOQUOTES, 'utf-8'), MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($prenoms)) { $affichage .= ' ' . $begin . mb_convert_case(html_entity_decode($prenoms, ENT_NOQUOTES, 'utf-8'), MB_CASE_TITLE, 'utf-8') . $end; }
		
		// S'il n'y a ni nom, ni prénom, on cherche l'organisme à afficher
		if (empty($affichage)) $affichage = $row['contact_organisme'];
		
		if ($return == false) : echo $affichage; else : return $affichage; endif;
		
		unset($affichage);
	}
	

	/**
	 * Cette méthode permet de préparer l'affichage des informations liée à la date de naissance de la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$separateur		Séparateur de date
	 * @param		bool		$return			True pour retourner la date de naissance, False pour l'afficher
	 * @param		string		$date			Si fourni, la date à utiliser
	 * @return		string|void					Retour ou affichage des informations suivant $return
	 */

	public	function date_naissance($separateur='/', $return = false, $date = null) {
		// Si aucune date n'est fourni, on utilise celle de la fiche ouverte
		if (empty($date)) : $date = $this->get_infos('naissance_date'); endif;
		
		$time = strtotime($date);
		$date = strtolower(strftime('%d' . $separateur . '%m' . $separateur . '%Y', $time));

		if ($return) : return $date; else : echo $date; endif;
	}
	

	/**
	 * Cette méthode permet de calculer l'âge d'un individu en fonction de sa date de naissnce
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		string		$date		Si fournie, la date à utiliser
	 * @param		bool		$return		True pour retourner l'âge, False pour l'afficher
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function age($date = null, $return = false) {
		// Si la date n'a pas été entrée, on prend celle de la fiche active
		if (!$date) { $date = $this->get_infos('naissance_date'); }
		
		$time_naissance = strtotime($date);
		
		$arr1 = explode('/', date('d/m/Y', $time_naissance));
		$arr2 = explode('/', date('d/m/Y'));
			
	    if(($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[0] <= $arr2[0]))) { $age = $arr2[2] - $arr1[2]; }
	    else { $age = $arr2[2] - $arr1[2] - 1; }
	    
	    if ($return) : return $age; else : echo $age . '&nbsp;ans'; endif;
	}
	

	/**
	 * Cette méthode permet d'afficher le lieu de naissance s'il existe dans la base de données
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$before		Contenu à intégrer en amont de la ville de naissance
	 * @param		bool		$return		True pour retourner l'âge, False pour l'afficher
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function lieu_de_naissance($before = null, $return = false) {
		// on récupère le département de naissance
		if ($this->get_infos('naissance_commune_id')) {
			$ville = $this->get_infos('naissance_commune_id');
			
			$sql = $this->db->query('SELECT commune_nom, departement_nom FROM communes LEFT JOIN departements ON departements.departement_id = communes.departement_id WHERE commune_id = ' . $ville);
			$row = $sql->fetch_array();
			$ville = $row[0]; 
			$departement = $row[1];
			
			if ($before) echo $before . ' ';
			
			echo utf8_encode($ville) . ' (' . utf8_encode($departement) . ')';
		} else {
			// S'il n'existe pas, on n'affiche rien
			return false;
		}
	}
	

	/**
	 * Cette méthode permet de formater une adresse postale depuis la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$separateur		Séparateur choisi entre les entités de l'adresse postale
	 * @param		bool		$return			True pour retourner l'âge, False pour l'afficher
	 * @return		string|void					Retour ou affichage des informations suivant $return
	 */

	public	function affichage_adresse($separateur = '<br>', $return = false) {
		$numero = $this->get_infos('adresse_numero');
		$adresse = mb_convert_case($this->get_infos('adresse_rue'), MB_CASE_LOWER, 'utf-8');
		$complement = mb_convert_case($this->get_infos('adresse_complement'), MB_CASE_LOWER, 'utf-8');
		$cp = $this->get_infos('adresse_cp');
		$ville = mb_convert_case($this->get_infos('adresse_ville'), MB_CASE_TITLE, 'utf-8');
		
		if (!empty($numero)) : $affichage = $numero . ' '; endif;
		if (!empty($adresse)) : $affichage .= $adresse . $separateur; endif;
		if (!empty($complement)) : $affichage .= $complement . $separateur; endif;
		if (!empty($cp)) : $affichage .= $cp . ' '; endif;
		if (!empty($ville)) : $affichage .= $ville; endif;

		// On remet en forme l'affichage
		$affichage = $this->tpl_transform_texte($affichage);
		
		if ($return) : return $affichage; else : echo $affichage; endif;
		
		return true;
	}
	

	/**
	 * Cette méthode permet d'afficher ou de retourner les informations concernant le canton de la fiche demandée
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		bool		$return		True pour retourner l'âge, False pour l'afficher
	 * @param		int			$id			Si fourni, le canton pour lequel il faut effectuer la recherche
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function canton($return = false, $id = null) {
		// S'il n'est pas fourni un id, on utilise celui de la fiche ouverte
		if (!$id) { $id = $this->fiche_ouverte['canton_id']; }
		
		// On recherche des informations sur le canton en question
		$query = "SELECT * FROM cantons WHERE canton_id = " . $id;
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
		
		// On retourne le nom du canton
		if ($return) : return $row['canton_nom']; else : echo $row['canton_nom']; endif;
		
		return true;
	}
	

	/**
	 * Cette méthode permet d'afficher ou de retourner les informations concernant le bureau de vote de la fiche demandée
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		bool		$lien		Afficher ou non un lien clicable
	 * @param		bool		$return		True pour retourner l'âge, False pour l'afficher
	 * @param		int			$id			Si fourni, le bureau de vote pour lequel il faut effectuer la recherche
	 * @param		int			$ville		Si fourni, la commune pour laquelle il faut effectuer la recherche
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function bureau($lien = null, $return = false, $id = null, $ville = null) {
		// S'il n'est pas fourni un id, on utilise celui de la fiche ouverte
		if (!$id) { $id = $this->fiche_ouverte['bureau_id']; }
		if (!$ville) { $ville = $this->fiche_ouverte['commune_id']; }
		
		// On recherche des informations sur le bureau en question
		$query = "SELECT * FROM bureaux WHERE bureau_numero = " . $id . " AND commune_id = " . $ville;
		$sql = $this->db->query($query);
		$row = $sql->fetch_assoc();
		
		// On retraite les informations
		$numero = $row['bureau_numero'];
		$nom = $row['bureau_nom'];

		// On remet en forme l'affichage
		$nom = $this->tpl_transform_texte($nom);
		
		// On regarde s'il faut mettre un lien vers la fiche du bureau de vote
		if ($lien) {
			$lien_open = '<a href="bureau.php?ville=' . $ville . '&id=' . $id . '">';
			$lien_close = '</a>';
		} else { $lien_open = ''; $lien_close = $lien_open; }
		
		// On retourne le résultat
		if ($return) : return $numero . ' ' . $nom; else : echo $lien_open . ' ' . $nom . $lien_close; endif;
	}
	

	/**
	 * Cette méthode permet d'afficher ou de retourner le sexe de la fiche ouverte
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		bool		$return		True pour retourner le sexe, False pour l'afficher
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function sexe($return = false) {
		// On récupère l'information
		$sexe = $this->get_infos('sexe');
		
		// on retourne l'information
		if ($sexe == 'M') { if ($return) : return 'Homme'; else : echo 'Homme'; endif; }
		else if ($sexe == 'F') { if ($return) : return 'Femme'; else : echo 'Femme'; endif; }
		else { if ($return) : return 'Inconnu'; else : echo 'Inconnu'; endif; }
		
		return true;
	}
	

	/**
	 * Cette méthode permet d'afficher ou de retourner les informations de contact de la fiche demandée
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		string		$type		Type du contact demandé
	 * @param		bool		$lien		Afficher ou non un lien clicable
	 * @param		bool		$return		True pour retourner les contacts, False pour l'afficher
	 * @param		int			$id			Si fourni, la fiche pour laquelle il faut effectuer la recherche
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function contact($type, $lien = false, $return = false, $id = NULL) {
		// Si un ID est fourni, on cherche les infos, sinon on les prend dans la fiche ouverte
		if ($id) {
			$query = "SELECT contact_" . $type . " FROM contacts WHERE contact_id = " . $id;
			$sql = $this->db->query($query);
			$row = $sql->fetch_array();
			
			$contact = $row[0];
		} else {
			$contact = $this->get_infos($type);
		}
		
		// On regarde s'il existe un contenu
		if ($contact) : $exist = true; else : $exist = false; endif;
		
		// On prépare le lien si demandé
		if ($lien && $exist) {
			if ($type == 'email') { $affichage = '<a href="mailto:' . $contact . '">' . $contact . '</a>'; }
			else if ($type == 'twitter') { $affichage = '<a href="http://twitter.com/' . $contact . '">@' . $contact . '</a>'; }
			else { $affichage = '<a href="tel:+33' . substr($contact, 1) . '">' . $this->get_tpl_phone($contact) . '</a>'; }
		} else if (!$lien && $exist) {
			if ($type == 'twitter') { $affichage = '@' . $contact; } else { $affichage = $contact; } 
		} else {
			$affichage = NULL;
		}
		
		// On retourne l'information demandée
		if ($return && $affichage) : return $affichage; else : echo $affichage; endif;
	}
	

	/**
	 * Cette méthode permet d'afficher ou de retourner les tags d'une fiche demandée
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$element	Le type de balise HTML à utiliser pour séparer les différents tags
	 * @param		bool		$return		True pour retourner les tags, False pour l'afficher
	 * @param		int			$id			Si fourni, la fiche contact pour laquelle il faut effectuer la recherche
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function tags($element = 'span', $return = false, $id = null) {
		if (empty($id)) {
			$id = $this->get_infos('id');
			$tags = $this->get_infos('tags');
		} else {
			$query = 'SELECT contact_tags FROM contacts WHERE contact_id = ' . $id;
			$sql = $this->db->query($query);
			$row = $sql->fetch_array();
			$tags = $row[0];
		}
		
		if (empty($tags)) {
			$affichage = '';
		} else {
			// On transforme l'information en tableau
			$tags = explode(',', $tags);
			
			// On prépare l'affichage
			$affichage = '';
			
			// On affiche le résultat
			foreach ($tags as $key => $tag) {
				$affichage .= '<' . $element . ' class="tag" id="tag-' . $key . '">' . $tag . '</' . $element . '>'; 
			}
		}
		
		if ($return) : return $affichage; else : echo $affichage; endif;
	}
	

	/**
	 * Cette méthode permet de mettre à jour une information d'une fiche contact
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		string		$info		Information à modifier
	 * @param		string		$valeur		Valeur à enregistrer
	 * @return		bool
	 */

	public	function update_contact( $info , $valeur ) {
		if (!empty($this->fiche_ouverte['contact_id'])) {
			$query = 'UPDATE contacts SET contact_' . $info . ' = "' . $valeur . '" WHERE contact_id = ' . $this->fiche_ouverte['contact_id'];
			if ($this->db->query($query)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet de savoir combien de tâches sont affectées à une fiche
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Tache
	 *
	 * @param		int			$id			Si fourni, permet de savoir pour quelle fiche lancer le calcul
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function taches_liees($id = null) {
		if (is_null($id)) $id = $this->get_infos('id');
		
		$query = "SELECT * FROM taches WHERE tache_contacts LIKE '%" . $id . "%' AND tache_terminee = 0";
		$sql = $this->db->query($query);
		
		$nb = $sql->num_rows;
		
		if ($nb > 0) {
			// On initialise les tâches contenant notre fiche
			$taches = array();
		
			while ($row = $sql->fetch_assoc()) {
				// on décompose les fiches liées à cette tâche pour savoir s'il y a vraiment notre fiche
				$contacts = explode(',', trim($row['tache_contacts'], ','));
				
				if (in_array($id, $contacts)) {
					$taches[] = $row; 
				}
			}
			
			return $taches;
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet d'extraire les dossiers liés à une fiche contact
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Dossier
	 *
	 * @param		int			$id			Si fourni, la fiche contact pour laquelle il faut effectuer la recherche
	 * @return		string|void				Retour ou affichage des informations suivant $return
	 */

	public	function dossiers_lies($id = null) {
		if (is_null($id)) $id = $this->get_infos('id');
		
		$query = "SELECT * FROM dossiers WHERE dossier_contacts LIKE '%" . $id . "%' ORDER BY dossier_statut DESC, dossier_date_ouverture DESC";
		$sql = $this->db->query($query);
		
		$nb = $sql->num_rows;
		
		if ($nb > 0 ) {
			// On initialise le traitement des dossiers concernant notre fiche
			$dossiers = array();
			
			while ($row = $sql->fetch_assoc()) {
				// On décompose les contacts liées à ce dossier pour savoir s'il y a vraiment notre fiche à l'intérieur
				$contacts = explode(',', trim($row['dossier_contacts'], ','));
				
				if (in_array($id, $contacts)) {
					$dossiers[] = $row;
				}
			}

			return $dossiers;
		} else {
			return false;
		}
	}
	

	/**
	 * Cette méthode permet l'ajout d'un dossier à la base de données du site
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Dossier
	 *
	 * @param		string			$nom			Nom du dossier à créer
	 * @param		string			$description	Description du dossier à créer
	 * @param		string|array	$fiches			Fiches concernées par la création du dossier
	 * @param		bool			$return			True pour retourner les contacts, False pour l'afficher
	 * @return		int|void						Retour ou affichage de l'identifiant du dossier créé
	 */

	public	function dossier_ajout($nom, $description, $fiches = null, $return = true) {
		// On retraite le tableau des fiches pour l'ajout à la base de données
		if (is_array($fiches)) {
			// Si les données sont envoyées sous le format d'un tableau, on implode les fiches en une variable sous le format CSV
			$fiches = implode(',', $fiches);
		}
		
		// On vérifie le format des données
		$nom = $this->securisation_string($nom); if (!is_string($nom)) return false;
		$description = $this->securisation_string($description); if (!is_string($description)) return false;
		$fiches = $this->securisation_string($fiches); if (!is_string($fiches)) return false;
		
		
		if (is_string($nom) && is_string($description) && is_string($fiches)) {
			// On prépare l'ajout à la base de données du dit fichier
			$query =   "INSERT INTO dossiers (	dossier_nom,
												dossier_description,
												dossier_contacts )
						VALUES				  (	'" . $nom . "',
												'" . $description . "',
												'" . $fiches . "' ) ";
												
			// On ajoute les données à la base de données
			$this->db->query($query);
			
			// On retourne l'ID du dossier ajouté pour information
			if ($return == true) {
				return $this->db->insert_id;
			} else {
				echo $this->db->insert_id;
			}
		}
	}
	

	/**
	 * Cette méthode permet d'extraire les informations liées à un dossier demandé
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 *
	 * @param		int			$id			ID du dossier recherché
	 * @return		array					Tableau des informations liées au dossier recherché
	 */

	public	function dossier($id) {
		// On retravaille l'ID fourni
		$id = $this->securisation_string($id);
		
		// On recherche dans la base de données les informations
		$query = 'SELECT * FROM dossiers WHERE dossier_id = ' . $id . ' LIMIT 0,1';
		$sql = $this->db->query($query);
		
		if ($sql->num_rows == 1) {
			$infos = $sql->fetch_assoc();
			
			// On transforme le nom des clés du tableau $infos pour retirer le préfixe BDD
			$infos = $this->formatage_donnees($infos);
			
			// On retourne les informations sous forme d'un tableau
			return $infos;
		} else {
			$this->tpl_redirection();
		}
	}
	

	/**
	 * Cette méthode permet d'extraire la liste des dossiers par leur nom
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Dossier
	 *
	 * @param		int			$recherche	Nom à rechercher
	 * @return		array					Tableau des dossiers en correspondance avec la recherche
	 */

	public	function dossier_recherche($recherche) {
		// On recherche dans la base de données les titres similaire à notre recherche si notre recherche est supérieure à 3 caractères
		if (strlen($recherche) > 3) {
			// On prépare la recherche dans la base de données
			$query =  "	SELECT		dossier_id, dossier_nom
						FROM		dossiers
						WHERE		dossier_nom LIKE '%" . $recherche . "%'
						ORDER BY	dossier_nom ASC
						LIMIT		0, 10 ";
			
			// On effectue la requête
			$sql = $this->db->query($query);
			
			// On traite la requête pour récupérer sous forme d'un tableau les différents titres des dossiers recherchés
			$resultats = array();
			
			while ($row = $sql->fetch_assoc()) {
				$resultats[] = array( 'id' => $row['dossier_id'], 'nom' => $row['dossier_nom'] );
			}

			// On finit par retourner la valeur du tableau des résultats s'il y a eu des résultats
			if (count($resultats)) {
				return $resultats;
			} else {
				return array();
			}
		} else {
			return array();
		}
	}
	

	/**
	 * Cette méthode permet de récupérer le nom d'une fiche, formaté, grâce à son identifiant
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		int			$id			ID de la fiche concernée
	 * @param		string		$separateur Séparateur choisi pour les entités du nom
	 * @param		bool		$return		True pour retourner le nom, False pour l'afficher
	 * @return		int|void				Retour ou affichage du nom de la fiche
	 */

	public	function nomByID($id, $separateur = null, $return = false) {
		// On récupère les informations dans la base de données concernant la fiche de demandée
		$query = 'SELECT contact_nom, contact_nom_usage, contact_prenoms FROM contacts WHERE contact_id = "' . $id . '"';
		$sql = $this->db->query($query);
		$infos = $sql->fetch_assoc();
	
		$nom = $infos['contact_nom']; 
		$nom_usage = $infos['contact_nom_usage'];
		$prenoms = $infos['contact_prenoms'];
	
		if ($separateur) { $begin = '<' . $separateur . '>'; $end = '</' . $separateur . '>'; }
		else { $begin = null; $end = null; }
	
		if (!empty($nom)) { $affichage = $begin . mb_convert_case($nom, MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($nom_usage)) { $affichage .= ' ' . $begin . mb_convert_case($nom_usage, MB_CASE_UPPER, 'utf-8') . $end; }
		if (!empty($prenoms)) { $affichage .= ' ' . $begin . mb_convert_case($prenoms, MB_CASE_TITLE, 'utf-8') . $end; }
		
		//if ($return == false) { echo $affichage; } else { return $affichage; }
		if ($return == false) { echo $affichage; } else { return $affichage; }
		
		unset($affichage);
	}
	

	/**
	 * Cette méthode permet d'ajouter une entrée à l'historique d'un utilisateur
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Historique
	 *
	 * @param		int			$fiche		ID de la fiche concernée
	 * @param		string		$type		Type d'entrée
	 * @param		string		$objet		Objet de l'entrée dans l'historique
	 * @param		string		$remarques	Remarques à enregistrer dans l'historique
	 * @return		void
	 */

	public	function historique_ajout($fiche, $type, $objet, $remarques = 'Entrée automatique du système') {
			$historique = array('fiche'			=> $fiche,
								'type'			=> $type,
								'date'			=> date('Y-m-d'),
								'objet'			=> $objet,
								'remarques'		=> $remarques );
			
			$this->db->query('INSERT INTO historique (contact_id,
												historique_type,	
												historique_date,
												historique_objet,
												historique_remarques)
									VALUES (		"' . $historique['fiche'] . '",
												"' . $historique['type'] . '",
												"' . $historique['date'] . '",
												"' . $historique['objet'] . '",
												"' . $historique['remarques'] . '" )');
	}
	

	/**
	 * Cette méthode permet de modifier l'adresse d'une fiche utilisateur sélectionnée
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		int			$contact	ID de la fiche concernée
	 * @param		int			$immeuble 	Nouvel immeuble à enregistrer pour le contact sélectionné
	 * @return		bool					Booléen selon la réussite ou non de la requête SQL
	 */

	public	function modificationAdresse( $contact , $immeuble ) {
		// On vérifie le format des informations entrées
			if (!is_numeric($contact) && !is_array($immeuble)) { return false; }
				
		// On prépare la requête BDD
			$query = 'UPDATE	contacts
					  SET		adresse_id = ' . $immeuble . '
					  WHERE		contact_id = ' . $contact;
			
		// On effectue la requête dans la BDD et on retourne le résultat
			$sql = $this->db->query($query);
			return $sql;
	}
	

	/**
	 * Cette méthode permet d'effectuer une recherche de fiches selon des critères donnés
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		string		$prenom		Prénoms recherchés
	 * @param		string		$nom		Nom de famille recherché
	 * @param		string		$nom_usage	Nom d'usage recherché
	 * @param		string		$sexe		Sexe recherché
	 * @return		array					Tableau des fiches trouvées correspondant à la recherche
	 */

	public	function recherche( $prenom , $nom , $nom_usage , $sexe = '%' ) {
		// Tout d'abord, on commence par retraiter le sexe entré
			if ($sexe == 'I') $sexe = '%';
		
		// On prépare les données entrées à être mise en place dans une recherche
			$prenom = $this->formatage_recherche($prenom);
			$nom_usage = $this->formatage_recherche($nom_usage);
			$nom = $this->formatage_recherche($nom);
		
		// On vérifie que si le champ est vide, on y met un joker
			if (empty($prenom)) $prenom = '%';
			if (empty($nom_usage)) $nom_usage = '%';
			if (empty($nom)) $nom = '%';
		
		// On prépare le tableau dans lequel les résultats seront affectés, et un tableau de vérification rapide
			$contacts = array();
			$ids = array();
		
		// On prépare la requête de recherche stricte sur les données noms et noms d'usage
			$query = 'SELECT		*
					  FROM		contacts
					  WHERE		contact_nom  LIKE "' . $nom . '"
					  AND		contact_nom_usage LIKE "' . $nom_usage . '"
					  AND		contact_prenoms LIKE "%' . $prenom . '%"
					  AND		( contact_sexe LIKE "' . $sexe . '" OR contact_sexe = "I" )
					  ORDER BY	contact_nom_usage ASC,
					  			contact_nom ASC,
					  			contact_prenoms ASC,
					  			contact_naissance_date DESC';
		
		// On effectue la recherche strict et on affecte les résultats au tableau contacts
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) :
				$contacts[] = $this->formatage_donnees($row);
				$ids[] = $row['contact_id'];
			endwhile;
		
		// On prépare maintenant la recherche permissive sur les données noms et noms d'usage
			$query = 'SELECT		*
					  FROM		contacts
					  WHERE		contact_nom  LIKE "%' . $nom . '%"
					  AND		contact_nom_usage LIKE "%' . $nom_usage . '%"
					  AND		contact_prenoms LIKE "%' . $prenom . '%"
					  AND		( contact_sexe LIKE "' . $sexe . '" OR contact_sexe = "I" )
					  ORDER BY	contact_nom_usage ASC,
					  			contact_nom ASC,
					  			contact_prenoms ASC,
					  			contact_naissance_date DESC
					  LIMIT		0, 25';
		
		// On effectue la recherche permissive et on affecte les résultats au tableau contacts
			$sql = $this->db->query($query);
			while ($row = $sql->fetch_assoc()) :
			
			// Avant d'ajouter dans le tableau des correspondances, on vérifie simplement que l'enregistrement n'y figure pas déjà
				if (!in_array($row['contact_id'], $ids)) $contacts[] = $this->formatage_donnees($row);
			
			endwhile;
		
		// On retourne le tableau des contacts trouvés
			return $contacts;
	}
	

	/**
	 * Cette méthode permet de rajouter un contact dans la base de données
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		array		$infos		Tableau des informations liées à la fiche à créer
	 * @return		int						ID de la fiche créée
	 */

	public	function creerContact( $infos ) {
		// On vérifie que les informations entrées prennent bien la forme d'un tableau et qu'elles contiennent les infos minimales
		if (!is_array($infos) && !isset($infos['nom'], $infos['prenom'])) return false;
		
		// On formate les téléphones, au cas où
		if ($infos['mobile'] == '') { $infos['mobile'] == NULL; }
		if ($infos['telephone'] == '') { $infos['telephone'] == NULL; }
		if (empty($infos['organisme'])) { $infos['organisme'] == ''; }
		if (empty($infos['fonction'])) { $infos['organisme'] == ''; }
		
		// On prépare la requête de création de la fiche
		$query = 'INSERT INTO	contacts (adresse_id,
										  contact_nom,
										  contact_nom_usage,
										  contact_prenoms,
										  contact_sexe,
										  contact_email,
										  contact_mobile,
										  contact_telephone,
										  contact_naissance_date,
										  contact_organisme,
										  contact_fonction)
				  VALUES (' . $infos['immeuble'] . ',
				  		  "' . $infos['nom'] . '",
				  		  "' . $infos['nom-usage'] . '",
				  		  "' . $infos['prenoms'] . '",
				  		  "' . $infos['sexe'] . '",
				  		  NULL,
				  		  NULL,
				  		  NULL,
				  		  "' . $infos['date-naissance'] . '",
				  		  "' . $infos['organisme'] . '",
				  		  "' . $infos['fonction'] . '")';
		
		// On exécute la requête au serveur
		$this->db->query($query);
		
		// On récupère l'id de l'entrée
		$id = $this->db->insert_id;
		
		// On fait les modifications dès qu'on a des informations
		if (!empty($infos['mobile'])) {
			$this->db->query('UPDATE contacts SET contact_mobile = "' .$infos['mobile']. '" WHERE contact_id = ' . $id);
		}
		if (!empty($infos['telephone'])) {
			$this->db->query('UPDATE contacts SET contact_telephone = "' .$infos['telephone']. '" WHERE contact_id = ' . $id);
		}
		if (!empty($infos['email'])) {
			$this->db->query('UPDATE contacts SET contact_email = "' .$infos['email']. '" WHERE contact_id = ' . $id);
		}
		if (!empty($infos['tags'])) {
			$this->db->query('UPDATE contacts SET contact_tags = "' .$infos['tags']. '" WHERE contact_id = ' . $id);
		}
		
		// On renvoit l'id de l'entrée
		return $id; 
	}
	

	/**
	 * Cette méthode permet de renommer une ville pour faire une recherche
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0			Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Carto
	 *
	 * @param		string		$ville_origine	Nom de la ville entrée
	 * @param		string		$dept_origine	Département correspondant à la ville en question
	 * @return		array						Tableau avec le nom modifié
	 */

	public	function renommerVille($ville_origine, $dept_origine) {
		// Pour éviter les problèmes d'apostrophes (dans la BDD souvent des espaces) des villes comme L'isle, on remplace par un joker
		$remplacement = array("'", " ", "OE");
		$ville_origine = str_replace($remplacement, '%', $ville_origine);
		
		// On évite certains caractères spéciaux
		$ville_origine = str_replace('œ', 'oe', $ville_origine);
		
		// Cas particulier de Paris / Marseille / Lyon, si ça commence par ces noms, on affiche juste la ville sans l'arrondissement
		if (preg_match('/^PARIS/', $ville_origine)) { $ville_origine = 'PARIS'; }
		if (preg_match('/^LYON/', $ville_origine)) { $ville_origine = 'LYON'; }
		if (preg_match('/^MARSEILLE/', $ville_origine)) { $ville_origine = 'MARSEILLE'; }
		
		// Cas particulier des DOM-TOM où la recherche doit porter sur 971 au lieu de 97
		if ($dept_origine == 97) { $dept_origine = '97%'; }
		
		// Cas particulier des villes mal orthographiées dans les bases de données
		if ($ville_origine == 'SAINT-DIE' && $dept_origine == 88) { $ville_origine = 'SAINT-DIE-DES-VOSGES'; }
		if ($ville_origine == 'CHERBOURG' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
		if ($ville_origine == 'OCTEVILLE' && $dept_origine == 50) { $ville_origine = 'CHERBOURG-OCTEVILLE'; }
		if ($ville_origine == 'MEULAN' && $dept_origine == 78) { $ville_origine = 'MEULAN-EN-YVELINES'; }
		
		// Cas particulier de Chalons sur Marne qui a changé de nom pour Chalons en Champagne
		if ($ville_origine == 'CHALONS-SUR-MARNE' && $dept_origine == 51) { $ville_origine = 'CHALONS-EN-CHAMPAGNE'; }
		
		// On retourne les données corrigées
		$donnees = array($ville_origine, $dept_origine);
		return $donnees;
	}
	

	/**
	 * Cette méthode permet d'effectuer un export de données en CSV depuis la base de données
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		array		$formulaire 	Arguments de sélection des fiches pour l'export
	 * @param		bool		$simulation		Si true, renvoit une simulation du nombre de fiches, si false export de la liste
	 * @param		bool		$export_liste	Si true, export de la fiche
	 * @return		mixed
	 */

	public	function export( $formulaire , $simulation = false , $export_liste = false) {
		// On commence par vérifier le format des arguments
		if (!is_array($formulaire) || !is_bool($simulation)) return false;
		
		// On prépare les critères géographiques
		$immeubles = array();
		
		if (!empty($formulaire['canton']) || !empty($formulaire['ville']) || !empty($formulaire['rue']) || !empty($formulaire['immeuble'])) {
			if (!empty($formulaire['ville']) && empty($formulaire['rue']) && empty($formulaire['immeuble'])) {
				
				// On recherche tous les immeubles présents 
				$query = 'SELECT	*
						  FROM		immeubles
						  LEFT JOIN	rues
						  ON		rues.rue_id = immeubles.rue_id
						  WHERE		rues.commune_id = ' . $formulaire['ville'];
				$sql = $this->db->query($query);
				
				if ($sql->num_rows > 0) { while($row = $sql->fetch_assoc()) $immeubles[] = $row['immeuble_id']; }
				
			} elseif (!empty($formulaire['ville']) && !empty($formulaire['rue']) && empty($formulaire['immeuble'])) {
				
				// On recherche tous les immeubles présents 
				$query = 'SELECT	*
						  FROM		immeubles
						  WHERE		rue_id = ' . $formulaire['rue'];
				$sql = $this->db->query($query);
				
				if ($sql->num_rows > 0) { while($row = $sql->fetch_assoc()) $immeubles[] = $row['immeuble_id']; }
				
			} else {
				
				// On recherche tous les immeubles présents 
				$query = 'SELECT	*
						  FROM		immeubles
						  WHERE		immeuble_id = ' . $formulaire['immeuble'];
				$sql = $this->db->query($query);
				
				if ($sql->num_rows > 0) { while($row = $sql->fetch_assoc()) $immeubles[] = $row['immeuble_id']; }
				
			}
		}
		
		// On prépare la requête SQL
		$query = 'SELECT	*
				  FROM		contacts';

		// tableau des critères initialisé
		$criteres = array(); //$this->debug($formulaire);
		
		// On calcule les âges mini et maxi en date
		if ($formulaire['age-min'] == 0) { $criteres[] = 'contact_naissance_date <= "' . date('Y-m-d', time()). '"'; } else { $criteres[] = 'contact_naissance_date <= "' . date('Y-m-d', mktime(0, 0, 0, date('n'), date('j'), date('Y')-$formulaire['age-min'])). '"'; }
		if ($formulaire['age-max'] == 0) { $a = ''; } else { $criteres[] = 'contact_naissance_date >= "' . date('Y-m-d', mktime(0, 0, 0, date('n'), date('j')-1, date('Y')-$formulaire['age-max']-1)).'"'; }
				
		if (!empty($formulaire['electeur'])) $criteres[] = 'contact_electeur = ' . $formulaire['electeur'];
		if ($formulaire['sexe'] != 'i') $criteres[] = 'contact_sexe = "' . $formulaire['sexe'] . '"';
		if ($formulaire['email']) $criteres[] = 'contact_email IS NOT NULL AND contact_optout_email = 0';
		if ($formulaire['mobile']) $criteres[] = 'contact_mobile IS NOT NULL AND contact_optout_mobile = 0';
		if ($formulaire['fixe']) $criteres[] = 'contact_telephone IS NOT NULL AND contact_optout_telephone = 0';
		if (!empty($formulaire['tags'])) $criteres[] = 'contact_tags LIKE "%' . $this->securisation_string($formulaire['tags']) . '%"';
		
		// On applique les critères à la requête SQL
		foreach ($criteres as $key => $critere) {
			if ($key == 0) { $query .= ' WHERE '; } else { $query .= ' AND '; }
			$query .= $critere;
		}
		
		// On travaille maintenant les critères géographiques
		if (count($immeubles) > 0) {
			$query.= ' AND (';
		
			foreach ( $immeubles as $key => $batiment ) {
				if ($key > 0) $query.= ' OR';
				$query.= ' immeuble_id = ' . $batiment;
			}
			
			$query.= ' )';
		}
		
		// On applique un tri des contacts
		$query .= ' ORDER BY contact_nom, contact_nom_usage, contact_prenoms ASC';
		
		// On lance la requête
		//$this->debug($query);
		$sql = $this->db->query($query);
		
		
		// Si c'est une simulation, on calcule le nombre de fiches et on retourne l'information
		if ($simulation) {
			if ($export_liste) {
				
				// On fait la liste des différentes fiches dans un tableau qu'on va renvoyer au demandeur
				$exportation = array();
				while ($row = $sql->fetch_assoc()) $exportation[] = $row['contact_id'];
				
				return $exportation;
				
			} else {
				
				return $sql->num_rows;
				
			}
			
		// Sinon, on fait la requête de tous les utilisateurs pour fabriquer le fichier et on le créé
		} else {

			// On prépare le contenu du fichier sous forme de tableau
			$fichier = array();
			
			// On ouvre le fichier
			$nomFichier = 'export-' . $_COOKIE['leqg-user'] . '-' .date('Y-m-d-H\hi'). '-' . time() . '.csv';
			$f = fopen('exports/' . $nomFichier, 'w+');
			
			// On y entre la première ligne du fichier
			$entete = array(   'nom',
							   'nom_usage',
							   'prenoms',
							   'date_naissance',
							   'adresse',
							   'cp',
							   'ville',
							   'sexe',
							   'email',
							   'mobile',
							   'fixe',
							   'electeur');
			
			fputcsv($f, $entete, ';', '"');
			
			
			// On fait la boucle des contacts pour y ajouter les lignes
			while ($contact = $sql->fetch_assoc()) {
				// On commence par rechercher les coordonnées d'après l'immeuble
				$immeuble = $this->db->query('SELECT * FROM immeubles WHERE immeuble_id = ' . $contact['immeuble_id']);
				$immeuble = $this->formatage_donnees($immeuble->fetch_assoc());
				
				if ($immeuble['rue_id'] != $immeuble['bureau_id']) {
					$rue = $this->db->query('SELECT * FROM rues WHERE rue_id = ' . $immeuble['rue_id']);
					$rue = $this->formatage_donnees($rue->fetch_assoc()); 
					
					$ville = $this->db->query('SELECT * FROM communes WHERE commune_id = ' . $rue['commune_id']);
					$ville = $this->formatage_donnees($ville->fetch_assoc());
					
					$cp = $this->db->query('SELECT * FROM codes_postaux WHERE commune_id = ' . $ville['id']);
					$cp = $cp->fetch_assoc();
				} else {
					$rue['nom'] = '';
					$ville['nom'] = '';
					$cp['code_postal'] = '';
				}
				
				// on rassemble les informations qu'on balance dans le fichier
				$ligne = array(    $contact['contact_nom'],
								   $contact['contact_nom_usage'],
								   $contact['contact_prenoms'],
								   date('d/m/Y', strtotime($contact['contact_naissance_date'])),
								   $immeuble['numero'] . ' ' . trim($rue['nom']),
								   $cp['code_postal'],
								   $ville['nom'],
								   $contact['contact_sexe'],
								   $contact['contact_email'],
								   $contact['contact_mobile'],
								   $contact['contact_telephone'],
								   $contact['contact_electeur']);
								   
				fputcsv($f, $ligne, ';', '"');
			}
			
			// On ferme le fichier
			fclose($f);
			
			// On retourne le nom du fichier
			return 'exports/' . $nomFichier;
		}
	}
	

	/**
	 * Cette méthode permet de savoir si nous possédons des coordonnées concernant une fiche
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		int		$contact 	Fiche contact concernée par la recherche de coordonnées
	 * @return		bool
	 */

	public	function coordonneesExistantes( $contact ) {
		if (!is_numeric($contact) && !is_array($contact)) return false;
		
		// Si l'argument est un ID, on récupère les informations
		if (is_numeric($contact)) {
			$contact = $this->informations($contact);
		}
		
		// On vérifie qu'il n'y a pas un optout global
		if ($contact['optout_global']) return false;
		
		// On vérifie maintenant s'il existe des coordonnées non interdites par optout
		if (!is_null($contact['email']) && !$contact['optout_email']) return true;
		if (!is_null($contact['mobile']) && !$contact['optout_mobile']) return true;
		if (!is_null($contact['telephone']) && !$contact['optout_telephone']) return true;
		
		// Si aucune condition n'est remplie jusqu'ici, c'est que nous n'avons pas de coordonnées !
		return false;
	}
	

	/**
	 * Cette méthode permet de retourner ou d'exporter une liste de contacts au format JSON ou tableau PHP selon des conditions entrées en argument
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 *
	 * @param		string	$output	 	Type de rendu des données (JSON | PHP)
	 * @param		array	$args		Arguments de sélection et de tri des fiches
	 * @param		bool	$export		Si true lance un export des fiches, sinon retourne un tableau au format choisi par $output
	 * @param		int		$nombre		Permet de sélectionner un nombre maximal de fiches à afficher
	 * @param		int		$debut		Permet de spécifier à quel fiche débuter le rendu des fiches trouvées selon les conditions établies
	 * @return		mixed
	 */

	public	function liste( $output , $args = null , $export = false , $nombre = false , $debut = false ) {
		// On prépare la requête de recherche des données
		$query = 'SELECT	`contact_id`,
							`immeuble_id`,
							`adresse_id`,
							`bureau_id`,
							`contact_nom`,
							`contact_nom_usage`,
							`contact_prenoms`,
							`contact_naissance_date`,
							`contact_sexe`,
							`contact_email`,
							`contact_mobile`,
							`contact_telephone`,
							`contact_electeur`,
							`contact_tags`
				  FROM		`contacts` ';

		// On lance le traitement des arguments dans un tableau $conditions
		$conditions = array();
		$immeubles = array(); // On prépare également le tableau $immeubles pour le traitement des critères géographiques
		$bureaux = array(); // On prépare également le tableau $buraeux pour le traitement des critères de bureaux électoraux

		if (!is_null($args) && is_array($args)) {
			// On lance une boucle de traitement des arguments
			foreach ($args as $key => $arg) :

				// conditions relatives aux coordonnées
				if ($key == 'contact') {
				
					if ($arg == 'tous') $conditions[] = '( `contact_email` IS NOT NULL OR `contact_mobile` IS NOT NULL OR `contact_telephone` IS NOT NULL )';
					if ($arg == 'email') $conditions[] = '`contact_email` IS NOT NULL';
					if ($arg == 'mobile') $conditions[] = '`contact_mobile` IS NOT NULL';
					if ($arg == 'telephone') $conditions[] = '`contact_telephone` IS NOT NULL';
					
				} elseif ($key == 'bureau') {
					
					// on fait la liste des bureaux dans un tableau bureaux
					$bureaux = $arg;
					
				} elseif ($key == 'tags') {
				
					$conditions[] = '`contact_tags` LIKE "%' . $arg . '%"';
					
				} elseif ($key == 'electoral') {
					
					if ($arg == 'oui') $conditions[] = '`contact_electeur` = 1';
					if ($arg == 'non') $conditions[] = '`contact_electeur` = 0';
				
				}
				
			endforeach;
		}
		
		// On prépare le tableau $conditionSQL qui contient les différentes conditions à installer dans la requête (géographique, coordonnées, divers)
		$conditionSQL = array();
		
		// S'il existe des bureaux de vote sélectionnés, on installe le critère bureau de vote dans la base de données
		if (count($bureaux) > 0) {
			$conditionSQL[] = ' ( `bureau_id` = ' . implode(' OR `bureau_id` = ', $bureaux) . ' ) ';
		}
		
		// S'il existe des immeubles sélectionnés, on installe le critère géographique dans la base de données
		if (count($immeubles) > 0) {
			$conditionSQL[] = ' ( `immeuble_id` = ' . implode(' OR `immeuble_id` = ', $immeubles) . ' ) ';
		}
		
		// S'il existe des conditions, on les reformate et on les ajoute à la requête
		if (count($conditions) > 0) :
		
			// On formate la liste des conditions en SQL
			$conditionSQL[] = ' ( ' . implode(' AND ', $conditions) . ' ) ';
						
		endif;
		
		// S'il existe des conditions à installer dans la requête SQL, on le fait
		if (count($conditionSQL) > 0) {
			$query.= ' WHERE ' . implode(' AND ', $conditionSQL);
		}
		
		// On termine la préparation de la requête
		$query.= 'ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC ';
		if (is_numeric($nombre) && is_numeric($debut)) $query.= 'LIMIT ' . $debut . ', ' . $nombre;
		if (is_numeric($nombre) && !is_numeric($debut)) $query.= 'LIMIT 0, ' . $nombre;

		// On exécute la requête SQL et on l'affecte au tableau $contacts
		$sql = $this->db->query($query);
		$contacts = array();
		
		if ($sql->num_rows > 0) while ($row = $sql->fetch_assoc()) $contacts[] = $this->formatage_donnees($row);

		// Si on demande une sortie des données sans export, on retourne sous format JSON ou tableau PHP les données
		if ($export == false) {
			if ($output == 'JSON') {
				$json = json_encode($contacts);
				//$json = str_replace('null', '', $json);
				echo $json;
			} elseif ($output == 'debug') {
				$this->debug($contacts);
			} else {
				return $contacts;
			}
		
		// Sinon, on procède à un export des données dans un fichier
		} else {
		
			// On prépare le contenu du fichier sous forme de tableau
			$fichier = array();
			
			// On ouvre le fichier
			$nomFichier = 'export-' . $_COOKIE['leqg-user'] . '-' .date('Y-m-d-H\hi'). '-' . rand(1, 100) . '.csv';
			$f = fopen('exports/' . $nomFichier, 'w+');
			
			// On y entre la première ligne du fichier
			$entete = array(   'bureau',
							   'nom',
							   'nom_usage',
							   'prenoms',
							   'organisation',
							   'fonction',
							   'date_naissance',
							   'adresse',
							   'cp',
							   'ville',
							   'sexe',
							   'email',
							   'mobile',
							   'fixe',
							   'electeur');
			
			fputcsv($f, $entete, ';', '"');
			
			
			// On fait la boucle des contacts pour y ajouter les lignes
			foreach ($contacts as $contact) {
				// On commence par rechercher les coordonnées d'après l'immeuble
				$immeuble = $this->db->query('SELECT * FROM immeubles WHERE immeuble_id = ' . $contact['immeuble_id']);
				$immeuble = $this->formatage_donnees($immeuble->fetch_assoc());
				
				if ($immeuble['rue_id'] != $immeuble['bureau_id']) {
					$rue = $this->db->query('SELECT * FROM rues WHERE rue_id = ' . $immeuble['rue_id']);
					$rue = $this->formatage_donnees($rue->fetch_assoc()); 
					
					$ville = $this->db->query('SELECT * FROM communes WHERE commune_id = ' . $rue['commune_id']);
					$ville = $this->formatage_donnees($ville->fetch_assoc());
					
					$cp = $this->db->query('SELECT * FROM codes_postaux WHERE commune_id = ' . $ville['id']);
					$cp = $cp->fetch_assoc();
					
					$bureau = $this->db->query('SELECT `bureau_numero` FROM `bureaux` WHERE `bureau_id` = ' . $contact['bureau_id']);
					$bureau = $bureau->fetch_assoc();
				} else {
					$rue['nom'] = '';
					$ville['nom'] = '';
					$cp['code_postal'] = '';
					$bureau['bureau_numero'] = '0';
				}
				
				// on rassemble les informations qu'on balance dans le fichier
				$ligne = array(    $bureau['bureau_numero'],
								   $contact['nom'],
								   $contact['nom_usage'],
								   $contact['prenoms'],
								   $contact['organisme'],
								   $contact['fonction'],
								   ($contact['naissance_date'] == '0000-00-00') ? '' : date('d/m/Y', strtotime($contact['naissance_date'])),
								   $immeuble['numero'] . ' ' . trim($rue['nom']),
								   $cp['code_postal'],
								   $ville['nom'],
								   ($contact['sexe'] == 'i') ? '' : $contact['sexe'],
								   $contact['email'],
								   $contact['mobile'],
								   $contact['telephone'],
								   $contact['electeur']);
								   
				fputcsv($f, $ligne, ';', '"');
			}
			
			// On ferme le fichier
			fclose($f);
			
			// On retourne le nom du fichier
			return 'exports/' . $nomFichier;
		}
	}
}

?>