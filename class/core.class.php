<?php

/*
	Classe du noyau central du système LeQG
*/


class core {
	
	// Définition des propriétés
	private $db; // lien à la base de données
	private $noyau; // Lien vers la base de données centrale du système
	private $url; // le nom de domaine du serveur utilisé
	
	
	// Définition des méthodes	
	
	public	function __construct($db, $noyau, $url) {
		$this->db = $db;
		$this->noyau = $noyau;
		$this->url = $url;
	}
	
	
	// debug( array ) permet d'afficher un array mis en forme par la fonction print_r et d'arrêter le script à des fins de debug
		public	function debug($array, $htmlentities = false) {
			echo '<pre class="nowrap">';
		
			// On affiche le tableau préformaté s'il s'agit d'un tableau
			if (is_array($array)) { print_r($array); }
			
			else if (is_object($array)) { print_r($array); }
			
			else if (is_bool($array)) { echo ucwords(gettype($array)).'<br>(<span class="wrap">' . var_dump($array) . '</span>)'; }
			
			else if (is_numeric($array)) { echo 'Numeric<br>(<span class="wrap">' . $array . '</span>)'; }
			
			// On affiche une phrase d'erreur s'il ne s'agit pas d'un tableau avec le contenu de la variable en question
			else {
				echo ucwords(gettype($array)).'<br>(<span class="wrap">';
					echo $array;
				echo '</span>)';
			}
			
			echo '</pre>';
			
			// Dans tous les cas, on arrête le script à l'appel de la fonction
			exit;
		}
	
	
	// securisation_string( string ) permet de sécuriser les données qui doivent transiter par la base de données pour éviter les injections
		public	function securisation_string($string, $charset = 'utf-8') {
			// On ajout des antislashes pour les caractères spéciaux
				//$string = addslashes($string);
			
			// On transforme les caractères spéciaux en entités HTML
				$string = htmlentities($string, ENT_QUOTES, $charset);
			
			// On retourne la chaîne de caractères sécurisée
				return $string;
		}
	
	
	// formatage_recherche( string ) permet de préparer un champ texte à être recherché via LIKE MySQL
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
		
	
	// formatage_donnees( array , string ) permet de retourner le tableau entré sans les préfixes BDD
		public	function formatage_donnees($array) {
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
	
	
	// triParColonne( array , string , string ) permet de trier des tableaux multidimentionnels d'après une clé
		public	function triParColonne( &$arr , $col , $dir = SORT_ASC ) {
			// On prépare le tableau de tri
				$sort_col = array();
				
			// On effectue une sélection des colonnes à trier
				foreach ($arr as $key => $row) $sort_col[$key] = $row[$col];
			
			// On effectue le tri multidimensionnel
				array_multisort($sort_col, $dir, $arr);
		}
		
	
	// sortie ( string ) est une méthode permettant de traiter une contenu puis de l'afficher
		public	function sortie( $texte ) {
			// On retraite le contenu envoyé pour l'affichage
			$texte = stripslashes($texte);
			
			// On l'affiche
			echo $texte;
			
			// Petit retour pour la forme
			return true;
		}
	
	
// Méthodes liées au templating
	
	// tpl_load( slug [, nom = null ] )  appelles le fichier slug-nom.tpl.php
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
	
	
	// tpl_header, tpl_aside et tpl_footer permet de charger le header, le aside ou le footer de la page
		public	function tpl_header( $nom = null ) { $this->tpl_load('header', $nom); }
		public	function tpl_footer( $nom = null ) { $this->tpl_load('footer', $nom); }
		public	function tpl_aside( $nom = null ) { $this->tpl_load('aside', $nom); }
	
	
	// tpl_redirection ( $page [ , $valeur = null, $attribut = id ] ) permet d'effectuer le renvoi de l'utilisateur vers une autre page
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
	
	
	// tpl_go_to ( $page [ , $arguments (array) , bool ] permet d'effectuer une redirection vers une page demandée en suivant le format de nomage des pages du site, tout en ayant une multitude d'arguments
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
	
	
	// tpl_get_url ( [ $page = nul , $valeur = null, $attribut = id ] ) permet d'effectuer l'affichage vers l'URL d'une page
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
	
		public	function tpl_get_url( $page = null , $valeur = null , $attribut = null, $valeur2 = null, $attribut2 = null) { echo $this->tpl_return_url($page, $valeur, $attribut, $valeur2, $attribut2); }
	
	
	// tpl_domaine() permet de retourner le nom de domaine du site
		public	function tpl_return_domain() {
			$domain = 'http://' . $this->url;
			return $domain;
		}
	
		public	function tpl_get_domain() { echo $this->tpl_return_domain(); }
	
	// tpl_phone( $numero ) affiche une version mise en forme du numéro de téléphone
		public	function tpl_phone( $numero ) { if (!empty($numero)) echo $numero{0}.$numero{1}.' '.$numero{2}.$numero{3}.' '.$numero{4}.$numero{5}.' '.$numero{6}.$numero{7}.' '.$numero{8}.$numero{9}; }
		public	function get_tpl_phone( $numero ) { if (!empty($numero)) return $numero{0}.$numero{1}.' '.$numero{2}.$numero{3}.' '.$numero{4}.$numero{5}.' '.$numero{6}.$numero{7}.' '.$numero{8}.$numero{9}; }


	// tpl_transform_texte() permet de modifier l'affichage d'un texte (adresse)
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