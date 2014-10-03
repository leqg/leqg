<?php
/**
 * La classe core représente le noyau central du système LeQG
 * 
 * Cette classe comprend l'ensemble des méthodes nécessaires à tous les scripts de la plateforme.
 * Elle contient des classes de débogage, de templating ou encore de formatage des données.
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */
class core {
	
	/**
	 * @var	object	$db		Propriété concenant le lien vers la base de données de l'utilisateur
	 * @var	object	$noyau	Propriété contenant le lien vers la base de données globale LeQG
	 * @var	string	$url		Propriété contenant l'URL du serveur
	 */
	private $db, $noyau, $url;
	

	/**
	 * Cette méthode permet la construction de la classe _core_
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
	 * _debug()_ permet le débogage des scripts de la plateforme en affichant le contenu d'un objet PHP
	 * 
	 * Cette fonction permet d'afficher par l'intermédiaire d'un _print_r()_ le contenu d'un objet permettant
	 * son débogage puis d'arrêter ou non, à la demande, l'exécution d'un script.
	 * Cette fonction assure un rendu des objets PHP adapté selon leur type.
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	mixed $objet L'objet à déboger
	 * @param	bool $exit Arrêt du script
	 * @return	void
	 */
	 
	public	function debug($objet, $exit = true) {
		echo '<pre class="nowrap">';
	
		// On affiche le tableau préformaté s'il s'agit d'un tableau
		if (is_array($objet)) { print_r($objet); }
		
		else if (is_object($objet)) { print_r($objet); }
		
		else if (is_bool($objet)) { echo ucwords(gettype($objet)).'<br>(<span class="wrap">' . var_dump($objet) . '</span>)'; }
		
		else if (is_numeric($objet)) { echo 'Numeric<br>(<span class="wrap">' . $objet . '</span>)'; }
		
		// On affiche une phrase d'erreur s'il ne s'agit pas d'un tableau avec le contenu de la variable en question
		else {
			echo ucwords(gettype($objet)).'<br>(<span class="wrap">';
				echo $objet;
			echo '</span>)';
		}
		
		echo '</pre>';
		
		// On regarde si on arrête ou non l'exécution du script à la demande
		if ($exit) { exit; }
	}
	
	
	/**
	 * *securisation_string()* permet de sécuriser les données qui doivent transiter par la base de données
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $string Chaîne à retraiter
	 * @param	string $charset Jeu de caractère de la chaîne à traiter
	 * @return	string
	 */

	public	function securisation_string($string, $charset = 'utf-8') {
		// On transforme les caractères spéciaux en entités HTML
			$string = htmlentities($string, ENT_QUOTES, $charset);
		
		// On retourne la chaîne de caractères sécurisée
			return $string;
	}
	
	
	/**
	 * *formatage_recherche() permet de préparer une chaîne de caractère à être lancé dans une recherche SQL via LIKE
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $string Chaîne à retraiter
	 * @param	string $charset Jeu de caractère de la chaîne à traiter
	 * @return	string
	 */

	public	function formatage_recherche($string, $charset = 'utf-8') {
		// On vérifie que le texte entré est bien un champ texte
			if (!is_string($string)) return false;
		
		// On sécurise le contenu envoyé
			$string = $this->securisation_string($string, $charset);
			
		// On fait une liste de caractères spéciaux à remplacer basiquement par des jokers
			$char = array(' ', '_', '.', ',');
		
		// On remplace cette liste de caractères dans la chaîne
			$string = str_replace($char, '%', $string);
		
		// On retire tous les caractères spéciaux
			$string = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $string);
		    $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string); // pour les ligatures e.g. '&oelig;'
			$string = preg_replace('#&[^;]+;#', '%', $string); // supprime les autres caractères
			
		// On retourne le contenu final près à une recherche
			return $string;
	}
		
	
	/**
	 * *formatage_donnees() permet de retourner le tableau entré sans les préfixes BDD
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	array
	 * @return	array
	 */

	public	function formatage_donnees(array $array) {
		if (!is_array($array)) return $array;
		
		// On initialise le nouveau tableau
		$keys = array_keys($array);
		
		// On détecte quel est le premier segment BDD à retirer du nom de la clé
		$segment = explode('_', $keys[0]);
		$segment = $segment[0];
		
		foreach ($keys as $key) {
			// On détecte les segments de la clé entrée
			$seg = explode('_', $key);
			
			// Si le premier segment correspond au segment à retirer, on le vire du tableau
			if ( $seg[0] == $segment ) :
				unset($seg[0]);
				$new_key = implode('_', $seg);
			else :
				$new_key = implode('_', $seg);
			endif;
			
			// On transforme la clé
			if ($new_key != $key) :
				$array[$new_key] = $array[$key];
				unset($array[$key]);
			endif;
		}
		
		return $array;
	}
	
	
	/**
	 * *triParColonne()* permet de trier des tableaux multidimentionnels d'après une clé demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	array $arr Tableau à trier
	 * @param	string $col Colonne à utiliser pour le tri
	 * @param	string $dir	Sens du tri (SORT_ASC ou SORT_DESC)
	 * @return	array
	 */

	public	function triParColonne( &$arr , $col , $dir = SORT_ASC ) {
		// On prépare le tableau de tri
			$sort_col = array();
			
		// On effectue une sélection des colonnes à trier
			foreach ($arr as $key => $row) $sort_col[$key] = $row[$col];
		
		// On effectue le tri multidimensionnel
			array_multisort($sort_col, $dir, $arr);
	}
		
	
	/**
	 * *sortie()* permet de traiter un contenu puis de l'afficher
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $texte Texte à retraiter et afficher
	 * @return	void
	 */

	public	function sortie( $texte ) {
		// On retraite le contenu envoyé pour l'affichage
		$texte = stripslashes($texte);
		
		// On l'affiche
		echo $texte;
	}
	
	
	/**
	 * *tpl_load()* permet de charger un fichier de template
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $slug Nom du module appelé
	 * @param	string $nom Nom du fichier au sein du module demandé
	 * @return	void
	 */

	public	function tpl_load( $slug , $nom = null, $globale = null) {
		if (is_null($globale)) {
			global $db, $noyau, $config, $core, $csv, $user, $fiche, $tache, $dossier, $historique, $fichier, $carto, $mission, $notification;
		} else {
			global $db, $noyau, $config, $core, $csv, $user, $fiche, $tache, $dossier, $historique, $fichier, $carto, $mission, $notification, $globale;
		}
	
		if (empty($nom)) :
			require 'tpl/' . $slug . '.tpl.php';
		else :
			require 'tpl/' . $slug . '-' . $nom . '.tpl.php';
		endif;
	}
	
	
	/**
	 * *tpl_header()* permet de charger le header de la page demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $nom Nom du module demandé
	 * @return	void
	 */

	public	function tpl_header( $nom = null ) { $this->tpl_load('header', $nom); }
	
	
	/**
	 * *tpl_footer()* permet de charger le footer de la page demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $nom Nom du module demandé
	 * @return	void
	 */

	public	function tpl_footer( $nom = null ) { $this->tpl_load('footer', $nom); }
	
	
	/**
	 * *tpl_aside()* permet de charger le aside de la page demandée
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $nom Nom du module demandé
	 * @return	void
	 */

	public	function tpl_aside( $nom = null ) { $this->tpl_load('aside', $nom); }
	
	
	/**
	 * *tpl_redirection()* permet d'effectuer le renvoi de l'utisateur vers une autre page
	 *
	 * @author		Damien Senger <mail@damiensenger.me>
	 * @version		1.0
	 * @deprecated	1.0 Cette méthode est obsolète et sera supprimée dans le futur.
	 * @see			Core::tpl_go_to Nouvelle méthode de redirection
	 *
	 * @param		string $page Page à appeler
	 * @param		string $valeur Valeur de l'attribut à appeler
	 * @param		string $attribut Attribut à appeler
	 * @return		void
	 */

	public	function tpl_redirection( $page = null , $valeur = null , $attribut = null ) {
		if (!empty($page) && !empty($attribut) && !empty($valeur)) {
			header( 'Location: index.php?page=' . $page . '&' . $attribut . '=' . $valeur );
		} else if (!empty($page) && !empty($valeur)) {
			header( 'Location: index.php?page=' . $page . '&id=' . $valeur );
		} else if (!empty($page)) {
			header( 'Location: index.php?page=' . $page );
		} else {
			header( 'Location: index.php' );
		}
	}
	
	
	/**
	 * *tpl_go_to()* permet d'effectuer une redirection de l'utilisateur
	 *
	 * Cette méthode permet d'effectuer une redirection de l'utilisateur vers une page demandée 
	 * en suivant le format de nomage des URL du site, tout en ayant une multitude d'arguments
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string $page Page appelée
	 * @param	array $arguments Tableau des arguments GET à ajouter à l'appel de la page
	 * @param	bool $redirect Déclenche la redirection ou afficher le lien
	 * @return	void
	 */

	public	function tpl_go_to( $page = null , $arguments = array() , $redirect = false ) {
		// Si $page == true, on demande une redirection immédiate vers la page d'accueil
		if (is_bool($page) && $page === true) header('Location: index.php');
		
		// Si $arguments == true, on va directement vers la page demandée
		if (!empty($page) && is_bool($arguments) && $arguments === true) header('Location: index.php?page=' . $page);
	
		// On vérifie que les arguments sont bien sous la forme d'un tableau
		if (!is_array($arguments)) return false;
		
		if ( !empty( $page ) ) {
			// On prépare l'adresse de la page d'arrivée
			$adresse = 'index.php?page=' . $page;
			 
			// On fait une boucle selon le nombre d'arguments
			foreach ($arguments as $key => $value) {
				$adresse .= '&' . $key . '=' . $value;
			}
			
			// On lance la redirection
			if ($redirect) {
				header('Location: ' . $adresse );
			} else {
				echo $adresse;
			}
		} else {
			if ($redirect) {
				header('Location: index.php');
			} else {
				echo 'index.php';
			}
		}
	}
	
	
	/**
	 * *tpl_return_url()* permet de retourner l'URL d'une page demandée
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string $page Page appelée
	 * @param	string $valeur Valeur du premier attribut à ajouter
	 * @param	string $attribut Premier attribut à ajouter à l'URL
	 * @param	string $valeur2 Valeur du premier attribut à ajouter
	 * @param	string $attribut2 Premier attribut à ajouter à l'URL
	 * @return	string
	 */

	public	function tpl_return_url( $page = null , $valeur = null , $attribut = null, $valeur2 = null, $attribut2 = null) {
		if (!empty($page) && !empty($attribut) && !empty($valeur) && !empty($attribut2) && !empty($valeur2)) {
			$url = 'index.php?page=' . $page . '&' . $attribut . '=' . $valeur . '&' . $attribut2 . '=' . $valeur2;
		} else if (!empty($page) && !empty($attribut) && !empty($valeur) && empty($attribut2) && empty($valeur2)) {
			$url = 'index.php?page=' . $page . '&' . $attribut . '=' . $valeur;
		} else if (!empty($page) && is_null($attribut) && !empty($valeur) && empty($attribut2) && empty($valeur2)) {
			$url = 'index.php?page=' . $page . '&id=' . $valeur;
		} else if (!empty($page) && is_null($attribut) && is_null($valeur) && empty($attribut2) && empty($valeur2)) {
			$url = 'index.php?page=' . $page;
		} else {
			$url = 'index.php';
		}
		
		return $url;

	}

	
	/**
	 * *tpl_get_url()* permet d'afficher l'URL d'une page demandée
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string $page Page appelée
	 * @param	string $valeur Valeur du premier attribut à ajouter
	 * @param	string $attribut Premier attribut à ajouter à l'URL
	 * @param	string $valeur2 Valeur du premier attribut à ajouter
	 * @param	string $attribut2 Premier attribut à ajouter à l'URL
	 * @return	void
	 */

	public	function tpl_get_url( $page = null , $valeur = null , $attribut = null, $valeur2 = null, $attribut2 = null) { echo $this->tpl_return_url($page, $valeur, $attribut, $valeur2, $attribut2); }
	
	
	/**
	 * *tpl_domaine()* permet de retourner le nom de domaine du site
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @return	string
	 */

	public	function tpl_return_domain() {
		$domain = 'http://' . $this->url;
		return $domain;
	}
	
	
	/**
	 * *tpl_get_domain()* permet d'afficher le nom de domaine du site
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @return	void
	 */

	public	function tpl_get_domain() { echo $this->tpl_return_domain(); }
	
	
	/**
	 * *tpl_phone()* permet d'afficher un numéro de téléphone formaté
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string $numero Numéro de téléphone à formater
	 * @return	void
	 */

	public	function tpl_phone( $numero ) { if (!empty($numero)) echo $numero{0}.$numero{1}.' '.$numero{2}.$numero{3}.' '.$numero{4}.$numero{5}.' '.$numero{6}.$numero{7}.' '.$numero{8}.$numero{9}; }
	
	
	/**
	 * *get_tpl_phone()* permet de retourner un numéro de téléphone formaté
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string $numero Numéro de téléphone à formater
	 * @return	string
	 */

	public	function get_tpl_phone( $numero ) { if (!empty($numero)) return $numero{0}.$numero{1}.' '.$numero{2}.$numero{3}.' '.$numero{4}.$numero{5}.' '.$numero{6}.$numero{7}.' '.$numero{8}.$numero{9}; }


	
	
	/**
	 * *tpl_transform_texte()* permet de transformer le contenu d'un texte
	 *
	 * Cette méthode permet de transformer un texte pour le formater à la lecture, en traitant les
	 * adresses de rues pour les rendre plus compréhensible
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	string $affichage Texte à formater
	 * @return	string
	 */

	public	function tpl_transform_texte($affichage) {
	
		$affichage = strtolower($affichage);
				
		// Avant les majuscules, on décale juste les lettre apostrophes
		$affichage = str_replace(' l\'', ' l\' ', $affichage);
		$affichage = str_replace(' d\'', ' d\' ', $affichage);
		
		// On mets en place des majuscules automatiques
		$affichage = ucwords($affichage);
		
		// On ajoute un espace au début pour les rues sans numéro
		$affichage = ' ' . $affichage;
		
		// On remplace certaines abbréviations par leur signification
		$affichage = str_replace(' Pce ', ' place ', $affichage);
		
		// On retire certaines majuscules
		$affichage = str_replace(' Rue ', ' rue ', $affichage);
		$affichage = str_replace(' Quai ', ' quai ', $affichage);
		$affichage = str_replace(' Pte ', ' petite ', $affichage);
		$affichage = str_replace(' Bd ', ' boulevard ', $affichage);
		
		$affichage = str_replace(' D ', ' du ', $affichage);
		$affichage = str_replace(' De ', ' de ', $affichage);
		$affichage = str_replace(' Du ', ' du ', $affichage);
		$affichage = str_replace(' Des ', ' des ', $affichage);
		$affichage = str_replace(' Aux ', ' aux ', $affichage);
		$affichage = str_replace(' Le ', ' le ', $affichage);
		$affichage = str_replace(' La ', ' la ', $affichage);
		$affichage = str_replace(' Les ', ' les ', $affichage);
		$affichage = str_replace(' L\' ', ' l\'', $affichage);
		$affichage = str_replace(' D\' ', ' d\'', $affichage);
		$affichage = str_replace(' Bv ', ' BV ', $affichage);
		
		// Lorsqu'on a un tiret suivi d'un espace, on retire l'espace
		$affichage = str_replace('- ', '-', $affichage);
		
		// On remplace certaines données pour la carte
		$affichage = str_replace('Vingt-deux', '22', $affichage);

		return $affichage;
	}
}

?>