<?php

/*
	Classe du noyau central du système LeQG
*/


class core {
	
	// Définition des propriétés
	private $db; // lien à la base de données
	
	
	// Définition des méthodes	
	
	public	function __construct($db) {
		$this->db = $db;
	}
	
	
	// debug( array ) permet d'afficher un array mis en forme par la fonction print_r et d'arrêter le script à des fins de debug
	public	function debug($array) {
		echo '<pre>';
	
		// On affiche le tableau préformaté s'il s'agit d'un tableau
		if (is_array($array)) { print_r($array); }
		
		else if (is_object($array)) { print_r($array); }
		
		else if (is_bool($array)) { if ($array == true) { echo ucwords(gettype($array)).'<br>(<span>true</span>)'; } else { echo ucwords(gettype($array)).'<br>(<span>false</span>)'; } }
		
		// On affiche une phrase d'erreur s'il ne s'agit pas d'un tableau avec le contenu de la variable en question
		else {
			echo ucwords(gettype($array)).'<br>(<span>';
				echo $array;
			echo '</span>)';
		}
		
		echo '</pre>';
		
		// Dans tous les cas, on arrête le script à l'appel de la fonction
		exit;
	}
	
	
	// securisation_string( string ) permet de sécuriser les données qui doivent transiter par la base de données pour éviter les injections
	public	function securisation_string($string) {
		// On ajout des antislashes pour les caractères spéciaux
		$string = addslashes($string);
		// On transforme les caractères spéciaux en entités HTML
		$string = htmlentities($string);
		
		// On retourne la chaîne de caractères sécurisée
		return $string;
	}
	
	
	// formatage_donnees( array , string ) permet de retourner le tableau entré sans les préfixes BDD
	public	function formatage_donnees($array) {
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
		
	
// Méthodes liées au templating
	
	// tpl_load( slug [, nom = null ] )  appelles le fichier slug-nom.tpl.php
	public	function tpl_load( $slug , $nom = null, $globale = null) {
		if (is_null($globale)) {
			global $db, $core, $user, $fiche, $tache, $dossier, $historique;
		} else {
			global $db, $core, $user, $fiche, $tache, $dossier, $historique, $globale;
		}
	
		if (empty($nom)) {
			require 'tpl/' . $slug . '.tpl.php';
		} else {
			require 'tpl/' . $slug . '-' . $nom . '.tpl.php';
		}
	}
	
	
	// tpl_header, tpl_aside et tpl_footer permet de charger le header, le aside ou le footer de la page
	public	function tpl_header( $nom = null ) { $this->tpl_load('header', $nom); }
	public	function tpl_footer( $nom = null ) { $this->tpl_load('footer', $nom); }
	public	function tpl_aside( $nom = null ) { $this->tpl_load('aside', $nom); }
	
	
	// tpl_redirection ( $page [ , $valeur = null, $attribut = id ] ) permet d'effectuer le renvoi de l'utilisateur vers une autre page
	public	function tpl_redirection( $page = null , $valeur = null , $attribut = null) {
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
	
	
	// tpl_get_url ( [ $page = nul , $valeur = null, $attribut = id ] ) permet d'effectuer l'affichage vers l'URL d'une page
	public	function tpl_return_url( $page = null , $valeur = null , $attribut = null, $valeur2 = null, $attribut2 = null) {
		if (!empty($page) && !empty($attribut) && !empty($valeur) && !empty($attribut2) && !empty($valeur2)) {
			$url = 'index.php?page=' . $page . '&amp;' . $attribut . '=' . $valeur . '&amp;' . $attribut2 . '=' . $valeur2;
		} else if (!empty($page) && !empty($attribut) && !empty($valeur) && empty($attribut2) && empty($valeur2)) {
			$url = 'index.php?page=' . $page . '&amp;' . $attribut . '=' . $valeur;
		} else if (!empty($page) && is_null($attribut) && !empty($valeur) && empty($attribut2) && empty($valeur2)) {
			$url = 'index.php?page=' . $page . '&amp;id=' . $valeur;
		} else if (!empty($page) && is_null($attribut) && is_null($valeur) && empty($attribut2) && empty($valeur2)) {
			$url = 'index.php?page=' . $page;
		} else {
			$url = 'index.php';
		}
		
		return $url;

	}
	
	public	function tpl_get_url( $page = null , $valeur = null , $attribut = null, $valeur2 = null, $attribut2 = null) { echo $this->tpl_return_url($page, $valeur, $attribut, $valeur2, $attribut2); }
	
	
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
		$affichage = str_replace(' A. ', ' aux ', $affichage);
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