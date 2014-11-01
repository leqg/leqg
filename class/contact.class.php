<?php

/**
 * La classe contact permet de créer un objet contact contenant toutes les opérations liées au contact ouvert
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class contact extends carto
{
	
	/**
	 * @var	array	$contact	Propriété contenant un tableau d'informations relatives au contact ouvert
	 * @var object	$link		Lien vers la base de données
	 */
	public $contact;
	private $link;
	
	
	/**
	 * Constructeur de la classe Contact
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$contact	Identifiant (hashage SHA2) du contact demandé
	 * @param	bool	$securite	Permet de savoir si on doit revenir au module contact si aucune fiche contact n'est trouvée
	 *
	 * @result	void
	 */
	 
	public function __construct($contact, $securite = false)
	{
		// On commence par paramétrer les données PDO
		$dsn =  'mysql:host=' . Configuration::read('db.host') . 
				';dbname=' . Configuration::read('db.basename');
		$user = Configuration::read('db.user');
		$pass = Configuration::read('db.pass');

		$this->link = new PDO($dsn, $user, $pass);
				
		// On cherche maintenant à savoir s'il existe un contact ayant pour identifiant celui demandé
		$query = $this->link->prepare('SELECT * FROM `contacts` WHERE MD5(`contact_id`) = :contact');
		$query->bindParam(':contact', $contact);
		$query->execute();
		$users = $query->fetchAll();
		
		// Si on ne trouve pas d'utilisateur, on retourne vers la page d'accueil du module contact
		if (!count($users) && $securite)
		{
			Core::tpl_go_to('contacts', true);
		}
		// Sinon, on affecte les données aux propriétés de l'objet
		else
		{
			$this->contact = $users[0];
		}
	}
	
	
	
	/**
	 * Cette méthode permet d'afficher les noms et prénoms du contact demandé
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 *
	 * @param	string	$separateur 	Élement choisi pour séparer les composants affichés
	 * @param	string	$conteneur 		Conteneur choisi pour les différents composants affichés
	 *
	 * @result	string					Noms et prénoms mis en forme du demandeur
	 */
	
	public function noms( $separateur = ' ', $conteneur = 'span' )
	{
		// On prépare le tableau d'affichage des résultats
		$retour = array();
		
		// On ajoute le conteneur comprenant le nom, le nom d'usage et les prénoms
		if (!empty($this->contact['contact_nom']))
		{
			$retour[] = $this->contenir(strtoupper($this->contact['contact_nom']), 'span');
		}
		
		if (!empty($this->contact['contact_nom_usage']))
		{
			$retour[] = $this->contenir(strtoupper($this->contact['contact_nom_usage']), 'span');
		}
		
		if (!empty($this->contact['contact_prenoms']))
		{
			$retour[] = $this->contenir(ucwords(strtolower($this->contact['contact_prenoms'])), 'span');
		}
		
		// On traite le tableau en intégrant le séparateur
		$retour = implode($separateur, $retour);
		
		// On retourne les informations demandées
		return $retour;
	}
	
	
	/**
	 * Cette méthode permet de retourner les informations de naissance, mis en forme
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$separateur			Séparateur utilisé pour la date
	 * @param	bool	$date_uniquement	Si true, cette méthode ne retourne que la date de naissance
	 *
	 * @result	array						Informations de naissance mises en forme
	 */
	
	public function naissance( $separateur = '/' , $date_uniquement = false )
	{
		// timestamp lié à la date de naissance
		$time = strtotime($this->contact['contact_naissance_date']);
		
		// on prépare l'affichage dans le tableau $retour
		$retour = array();
		
		// On prépare la date avec le séparateur choisi
		$retour[] = date('d' . $separateur . 'm' . $separateur . 'Y', $time);
		
		// On ne prépare les informations relatives aux départements et communes de naissance que si c'est demandé
		if ($date_uniquement === false && $this->contact['contact_naissance_commune_id'] != 0)
		{
			// on récupère les informations géographiques
			$query = $this->link->prepare('SELECT `commune_nom`, `departement_id` FROM `communes` WHERE `commune_id` = :commune');
			$query->bindParam(':commune', $this->contact['contact_naissance_commune_id']);
			$query->execute();
			$commune = $query->fetch(PDO::FETCH_ASSOC);
			
			// on récupère le nom du département
			$query = $this->link->prepare('SELECT `departement_nom` FROM `departements` WHERE `departement_id` = :departement');
			$query->bindParam(':departement', $commune['departement_id']);
			$query->execute();
			$departement = $query->fetch(PDO::FETCH_ASSOC);

			// On prépare l'affichage de la ville et de son département
			$retour[] = 'à ' . $commune['commune_nom'] . ' (' . ucwords($departement['departement_nom']) . ')';
		}
		
		// On met en forme le tableau
		$retour = implode(' ', $retour);
		
		// On retourne l'affichage préparé
		return $retour;
	}
	
	
	/**
	 * Cette méthode permet d'afficher l'âge du contact
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	bool	$textuel	Activer le mode textuel
	 *
	 * @return	string|int			Âge du contact en mode textuel ou non
	 */
	
	public function age( $textuel = true )
	{
		// Récupération du timestamp de la date de naissance
		$date = strtotime($this->contact['contact_naissance_date']);
		
		// Traitement de la date de naissance en blocs
		$annee = date('Y', $date);
		$mois = date('m', $date);
		$jour = date('j', $date);
		
		// On commence par calculer simplement l'âge
		$age = 2014 - $annee;
		
		// On ajuste par rapport au mois
		if ($mois >= date('m'))
		{
			// On ajuste par rapport au jour
			if ($jour > date('j'))
			{
				$age = $age - 1;
			}
		}
		
		// On regarde le mode d'affichage et on adapte le retour en conséquence
		if ($textuel === true)
		{
			// On retourne l'âge accompagné du mot "an(s)"
			$retour = $age . ' an';
			
			// On regarde si le pluriel du mot doit être mis ou non
			if ($age > 1)
			{
				$retour.= 's';
			}
			
			// On retourne l'âge
			return $retour;
		}
		else
		{
			// On retourne l'âge
			return $age;
		}
	}
	
	
	/**
	 * Cette méthode permet de retourner une des adresses, au format postal, du contact
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$adresse		Adresse choisie (électorale ou déclarée)
	 * @param	string	$separateur		Séparateur choisi entre les différentes entités de l'adresse
	 *
	 * @result	string					Adresse retournée
	 */
	
	public function adresse( $adresse = 'electoral' , $separateur = '<br>' )
	{
		// On récupère l'identifiant de l'adresse demandée
		$type = array('electorale' => 'immeuble', 'declaree' => 'adresse');
		$immeuble = $this->contact[ $type[ $adresse ] . '_id' ];
	
		// On récupère les informations liées à l'adresse dans la base de données en commençant par l'immeuble
		$query = $this->link->prepare('SELECT `immeuble_numero`, `rue_id` FROM `immeubles` WHERE `immeuble_id` = :immeuble');
		$query->bindParam(':immeuble', $immeuble);
		$query->execute();
		$immeuble = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On récupère les informations liées à la rue
		$query = $this->link->prepare('SELECT `rue_nom`, `commune_id` FROM `rues` WHERE `rue_id` = :rue');
		$query->bindParam(':rue', $immeuble['rue_id']);
		$query->execute();
		$rue = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On récupère les informations liées à la ville
		$query = $this->link->prepare('SELECT `commune_nom` FROM `communes` WHERE `commune_id` = :commune');
		$query->bindParam(':commune', $rue['commune_id']);
		$query->execute();
		$ville = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On récupère les informations léies au code postal
		$query = $this->link->prepare('SELECT `code_postal` FROM `codes_postaux` WHERE `commune_id` = :commune');
		$query->bindParam(':commune', $rue['commune_id']);
		$query->execute();
		$codepostal = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On prépare la mise en forme retournée dans la tableau $retour
		$retour = $immeuble['immeuble_numero'] . ' ' . $rue['rue_nom'] . $separateur . $codepostal['code_postal'] . ' ' . $ville['commune_nom'];
		
		// On retourne le texte
		return $retour;
	}
	
	
	/**
	 * Cette méthode permet d'afficher les informations liées au bureau de vote d'inscription du conseiller municipal
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version	1.0
	 * 
	 * @param	bool	$adresse	Si true, l'adresse sera affichée, sinon non
	 *
	 * @return	string				Informations relatives au bureau de vote, mises en forme
	 */
	
	public function bureau( $adresse = false )
	{
		// On récupère les informations sur le bureau de vote
		$query = $this->link->prepare('SELECT `bureau_numero`, `bureau_nom`, `bureau_adresse`, `bureau_cp`, `commune_id` FROM `bureaux` WHERE `bureau_id` = :bureau');
		$query->bindParam(':bureau', $this->contact['bureau_id']);
		$query->execute();
		$bureau = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On effectue la recherche des informations liées à la commune du bureau de vote
		$query = $this->link->prepare('SELECT `commune_nom` FROM `communes` WHERE `commune_id` = :commune');
		$query->bindParam(':commune', $bureau['commune_id']);
		$query->execute();
		$ville = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On prépare le retour au sein d'un tableau $retour
		$retour = array();
		
		// On prépare le nom du bureau de vote et de son numéro dans la variable $ligne
		$ligne = 'Bureau ' . $bureau['bureau_numero'] . ' &ndash; ' . $ville['commune_nom'];
		if (!empty($bureau['bureau_nom']))
		{
			$ligne.= '<br>' . $bureau['bureau_nom'];
		}
		
		// On affecte cette ligne au tableau de retour
		$retour[] = $ligne;
		unset($ligne);
		
		// On prépare l'affichage de l'adresse
		if ($adresse === true)
		{
			$ligne = $bureau['bureau_adresse'] . '<br>';
			$ligne.= $bureau['bureau_cp'] . ' ' . $ville['commune_nom'];
			
			// On affecte cette ligne au tableau de retour
			$retour[] = $ligne;
			unset($ligne);
		}
		
		// On prépare le rendu via le tableau $retour
		$retour = implode('<br>', $retour);
		
		// On retourne le rendu
		return $retour;
	}
	
	
	/**
	 * Cette méthode permet de récupérer un tableau de toutes les coordonnées enregistrées pour le contact
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param	string	$type		Type de coordonnées particulier demandé (email, fixe, mobile)
	 *
	 * @result	array				Tableau des coordonnées correspondant à la demande
	 */
	
	public function coordonnees( $type = 'toutes' )
	{
		// On prépare les requêtes SQL
		if ( $type == 'email' )
		{
			$query = $this->link->prepare('SELECT `coordonnee_id`, `coordonnee_type`, `coordonnee_email` FROM `coordonnees` WHERE `coordonnee_type` = "email" AND `coordonnee_email` IS NOT NULL AND `contact_id` = :contact ORDER BY `coordonnee_email` ASC');
		}
		else if ( $type == 'fixe' )
		{
			$query = $this->link->prepare('SELECT `coordonnee_id`, `coordonnee_type`, `coordonnee_numero` FROM `coordonnees` WHERE `coordonnee_type` = "fixe" AND `coordonnee_numero` IS NOT NULL AND `contact_id` = :contact ORDER BY `coordonnee_numero` ASC');
		}	
		else if ( $type == 'mobile' )
		{
			$query = $this->link->prepare('SELECT `coordonnee_id`, `coordonnee_type`, `coordonnee_numero` FROM `coordonnees` WHERE `coordonnee_type` = "mobile" AND `coordonnee_numero` IS NOT NULL AND `contact_id` = :contact ORDER BY `coordonnee_numero` ASC');
		}
		else
		{
			$query = $this->link->prepare('SELECT `coordonnee_id`, `coordonnee_type`, `coordonnee_email`, `coordonnee_numero` FROM `coordonnees` WHERE ( `coordonnee_numero` IS NOT NULL OR `coordonnee_email` IS NOT NULL ) AND `contact_id` = :contact ORDER BY `coordonnee_type`, `coordonnee_email`, `coordonnee_numero` ASC');
		}
		
		// On affecte au sein de la requête les données d'identification du contact et on exécute la requête
		$query->bindParam(':contact', $this->contact['contact_id']);
		$query->execute();
		$coordonnees = $query->fetchAll();
		unset($query);
		// On retourne le tableau $coordonnees
		return $coordonnees;
	}
	
	
	/**
	 * Cette méthode permet de vérifier si des informations de contact existent par rapport à un type demandé
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$type	Type demandé à la vérification d'une coordonnée ou non
	 *
	 * @return	bool
	 */
	
	public function possede( $type )
	{
		// On prépare la requête de vérification selon le type
		if ($type == 'email')
		{
			$query = $this->link->prepare('SELECT COUNT(*) AS `nombre` FROM `coordonnees` WHERE `coordonnee_email` IS NOT NULL AND `coordonnee_type` = "email" AND `contact_id` = :contact');
		}
		else if ($type == 'mobile')
		{
			$query = $this->link->prepare('SELECT COUNT(*) AS `nombre` FROM `coordonnees` WHERE `coordonnee_numero` IS NOT NULL AND `coordonnee_type` = "mobile" AND `contact_id` = :contact');
		}
		else
		{
			$query = $this->link->prepare('SELECT COUNT(*) AS `nombre` FROM `coordonnees` WHERE `coordonnee_numero` IS NOT NULL AND `coordonnee_type` = "fixe" AND `contact_id` = :contact');
		}
		
		// On affecte au sein de la requête les données d'identification du contact et on exécute la requête
		$query->bindParam(':contact', $this->contact['contact_id']);
		$query->execute();
		$coordonnees = $query->fetch(PDO::FETCH_ASSOC);
		unset($query);
		
		// On retourne un booléen selon le nombre de données trouvées
		if ($coordonnees['nombre'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 * Cette méthode permet d'ajouter un conteneur autour d'un texte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$contenu		Contenu concerné
	 * @param	string	$conteneur		Conteneur à ajouter autour de l'élément
	 *
	 * @result	string					Contenu et son conteneur
	 */
	
	private function contenir( $contenu, $conteneur = '' )
	{
		// On prépare la variable contenant le retour demandé
		$retour = '';
	
		// On ajoute l'ouverture du conteneur
		if (!empty($conteneur)) { $retour.= '<' . $conteneur . '>'; }
		
		// On ajoute le contenu
		$retour.= $contenu;
		
		// On ajoute la fermeture du conteneur
		if (!empty($conteneur)) { $retour.= '</' . $conteneur . '>'; }
		
		// On retourne le contenu
		return $retour;
	}
	
	
	/**
	 * Cette méthode permet l'ajout de coordonnées en lien avec la fiche ouverte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string		$type			Type de coordonnées envoyées
	 * @param	string|int	$coordonnees 	Coordonnées à rajouter
	 *
	 * @result	void
	 */
	
	public function ajoutCoordonnees( $type , $coordonnees )
	{
		// On prépare la requête selon le type fourni
		if ($type == 'email')
		{
			$query = $this->link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_email`) VALUES (:contact, :type, :coordonnees)');
		}
		else
		{
			$query = $this->link->prepare('INSERT INTO `coordonnees` (`contact_id`, `coordonnee_type`, `coordonnee_numero`) VALUES (:contact, :type, :coordonnees)');
		}
		
		// On affecte les variables à la requête
		$query->bindParam(':contact', $this->contact['contact_id']);
		$query->bindParam(':type', $type);
		$query->bindParam(':coordonnees', $coordonnees);
		
		// On exécute la requête
		$query->execute();
	}
	
	
	/**
	 * Cette méthode permet de retourner un tableau des différentes fiches liées à cette fiche
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @result	array		Tableau des fiches liées
	 */
	 
	public function fichesLiees()
	{
		// On prépare la requête de récupération des fiches liées
		$query = $this->link->prepare('SELECT `ficheA`, `ficheB` FROM `liaisons` WHERE `ficheA` = :contact OR `ficheB` = :contact');
		$query->bindParam(':contact', $this->contact['contact_id']);
		
		// On exécute la requête
		$query->execute();
		$fiches = $query->fetchAll();
		
		// On prépare le tableau des contacts trouvés $contacts
		$contacts = array();
		
		// Pour chaque résultat, on cherche les noms et prénoms du contact
		foreach ($fiches as $fiche)
		{
			// On regarde quelle est la deuxième fiche liée, et on l'affecte à la variable $fiche_id
			if ($fiche['ficheA'] == $this->contact['contact_id'])
			{
				$fiche_id = $fiche['ficheB'];
			}
			else
			{
				$fiche_id = $fiche['ficheA'];
			}
			
			// On prépare la requête de recherche
			$query = $this->link->prepare('SELECT `contact_nom`, `contact_nom_usage`, `contact_prenoms` FROM `contacts` WHERE `contact_id` = :contact');
			$query->bindParam(':contact', $fiche_id);
			
			// On exécute la requête et on ajoute les informations au tableau des contacts trouvés $contacts
			$query->execute();
			$infos = $query->fetch(PDO::FETCH_ASSOC);
			$contacts[$fiche_id] = $infos;
		}
		
		// On retourne le tableau final des contacts liés
		return $contacts;
	}
		
	
	/*
	 * Affiche une liste d'événements pour le contact ouvert
	 * 
	 * Cette méthode permet d'afficher une liste des événements connus dans la
	 * base de données pour le contact ouvert
	 * 
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @result	array					Tableau des informations connues
	 */
	
	public function listeEvenements(  )
	{
		// On exécute la recherche PDO et on récupère les informations dans un
		// tableau $evenements
		$query = $this->link->prepare('SELECT `historique_id` FROM `historique` WHERE `contact_id` = :contact ORDER BY `historique_date` DESC');
		$query->bindParam(':contact', $this->contact['contact_id']);
		$query->execute();
		$evenements = $query->fetchAll();
		
		// On retourne le tableau trouvé
		return $evenements;
	}
	
	
	/**
	 * Met à jour des informations dans la base de données
	 *
	 * Cette méthode permet de mettre à jour les données intégrées dans la base 
	 * de données pour le contact ouvert
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @param	int 	$info		Information à mettre à jour
	 * @param	mixed	$value		Valeur à intégrer dans la base
	 *
	 * @result	void
	 */
	
	public function update( $info , $value )
	{
		// On prépare la requête de mise à jour des données
		$query = $this->link->prepare('UPDATE `contacts` SET `' . $info . '` = :value WHERE `contact_id` = :id');
		
		// On affecte les variables
		$query->bindParam(':id', $this->contact['contact_id']);
		$query->bindParam(':value', $value);
		
		// On lance la requête
		$query->execute();
		
		// On enregistre dans les propriétés les informations
		$this->contact[$info] = $value;
	}
	
	
	/**
	 * Rajoute un tag à la base de données
	 *
	 * Cette méthode permet d'ajouter un tag pour la fiche ouverte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$tag	Tag à ajouter à la fiche
	 *
	 * @result	void
	 */
	
	public function tag_ajout( $tag )
	{
		// On récupère la liste des tags et on la transforme en array
		$tags = $this->contact['contact_tags'];
		$tags = explode(',', $tags);
		
		// On prépare la liste des tags entrés sous forme de tableaux
		$tag = explode(',', $tag);
		
		// On rassemble les deux tableaux
		$tags = array_merge($tags, $tag);
		
		// On supprime les doublons
		$tags = array_unique($tags);
		
		// On fabrique le champ texte à insérer dans la base de données
		$tags = implode(',', $tags);
		
		// On prépare la requête d'insertion dans la base de données
		$query = $this->link->prepare('UPDATE `contacts` SET `contact_tags` = :tags WHERE `contact_id` = :id');
		
		// On insère les paramètres dans la requête
		$query->bindParam(':id', $this->contact['contact_id'], PDO::PARAM_INT);
		$query->bindParam(':tags', $tags);
		
		// On exécute la requête
		$query->execute();
	}
	
	
	/**
	 * Supprimer le tag demandé de la base de données
	 *
	 * Cette méthode supprime le tag demandé de la fiche ouverte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	string	$tag	Tag à supprimer
	 *
	 * @result	void
	 */
	
	public function tag_suppression( $tag )
	{
		// On récupère la liste des tags
		$tags = $this->contact['contact_tags'];
		
		// On la retraite sous forme de tableau
		$tags = explode(',', $tags);
		
		// On regarde si on trouve le tag à supprimer dans la base de données
		if (array_search($tag, $tags))
		{
			$cle = array_search($tag, $tags);
			
			// On retire le tag de la liste en question
			unset($tags[$cle]);
		}
		
		// On reformate sous forme de chaîne de caractère la liste
		$tags = implode(',', $tags);
		
		// On enregistre le tout dans la base de données
		$query = $this->link->prepare('UPDATE `contacts` SET `contact_tags` = :tags WHERE `contact_id` = :id');
		
		// On insère les paramètres dans la requête
		$query->bindParam(':id', $this->contact['contact_id'], PDO::PARAM_INT);
		$query->bindParam(':tags', $tags);
		
		// On exécute la requête
		$query->execute();
	}
	
	
	/**
	 * Enregistre l'upload d'un fichier
	 *
	 * Cette méthode permet d'enregistrer dans la base de données l'upload d'un
	 * fichier pour la fiche ouverte
	 *
	 * @author	Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param	mixed	$file			Fichier à gérer
	 * @param	array	$data			Données liées 
	 * @param	array	$extensions		Extensions autorisées
	 * @param	int		$maxsize		Taille maximale autorisée des fichiers
	 *
	 * @result	void
	 */
	
	public function fichier_upload( $file , array $data , $extensions = false , $maxsize = false )
	{
		// On commence par déterminer le nom du fichier
		$extension = substr(strrchr($file['name'], '.'), 1);
		$nom = preg_replace("#[^a-z0-9]#", "-", strtolower($data['titre'])) . '-' . time() . '.' . $extension;
		
		// test1 : on vérifie que le fichier s'est uploadé correctement
		if (!isset($file) || $file['error'] > 0) return false;
		
		// test2 : on vérifie si on ne dépasse pas la taille limite
		if ($maxsize !== FALSE && $file['size'] > $maxsize) return false;
		
		// test3 : on vérifie si l'extension est autorisée
		$extension = substr(strrchr($file['name'], '.'), 1);
		if ($extensions !== FALSE && !in_array($extension, $extensions)) return false;
		
		// On détermine la destination
		$destination = 'uploads/' . $nom;
		
		// Dans ce cas, on déplace le fichier à sa destination finale
		if (move_uploaded_file($file['tmp_name'], $destination))
		{
			// On récupère l'ID de l'utilisateur
			$utilisateur = (isset($_COOKIE['leqg-user'])) ? $_COOKIE['leqg-user'] : 0;
			
			// On enregistre le fichier dans la base de données
			$query = $this->link->prepare('INSERT INTO `fichiers` (`contact_id`, `compte_id`, `interaction_id`, `fichier_nom`, `fichier_description`, `fichier_url`) VALUES ( :contact , :compte , :interaction , :nom , :description , :url )');
			$query->bindParam(':contact', $this->contact['contact_id']);
			$query->bindParam(':compte', $utilisateur);
			$query->bindParam(':interaction', $data['evenement']);
			$query->bindParam(':nom', $data['titre']);
			$query->bindParam(':description', $data['description']);
			$query->bindParam(':url', $nom);
			
			// On exécute l'ajout à la base de données
			$query->execute();
			
			// On retourne que tout s'est bien passé
			return true;
		}
		else
		{
			// On retourne l'erreur
			return false;
		}
	}
}

?>