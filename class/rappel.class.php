<?php

/**
 * La classe contact permet de créer un objet rappel contenant toutes les infos
 * concernant une mission de rappel militant demandée
 * 
 * @package		leQG
 * @author		Damien Senger <mail@damiensenger.me>
 * @copyright	2014 MSG SAS – LeQG
 */

class Rappel
{
	
	/**
	 * @var	array	$contact    Propriété contenant un tableau d'informations 
	 *                          relatives à la mission ouverte
	 * @var object	$link       Lien vers la base de données
	 */
	public $rappel;
	private $link;
	
	
	/**
	 * Constructeur de la classe Rappel
	 *
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 *
	 * @param   string  $mission  Identifiant (hashage MD5) de la mission demandée
	 *
	 * @result  void
	 */
	 
	public function __construct( $mission )
	{
		// On commence par paramétrer les données PDO
		$this->link = Configuration::read('db.link');
		
		// On cherche toutes les informations concernant la mission en cours 
		// pour les ajouter à la propriété $mission
		$query = $this->link->prepare('SELECT * FROM argumentaires WHERE `argumentaire_id` = :id');
		$query->bindParam(':id', $mission);
		
		// On exécute la recherche puis on l'affecte à la variable $mission
		$query->execute();
		$mission = $query->fetch(PDO::FETCH_ASSOC);
		
		// On recherche le nombre de numéros à appeler au sein de cette mission
		unset($query);
		$query = $this->link->prepare('SELECT COUNT(*) AS `nombre` FROM `rappels` WHERE `argumentaire_id` = :argumentaire');
		$query->bindParam(':argumentaire', $mission['argumentaire_id']);
		$query->execute();
		$rappels = $query->fetch(PDO::FETCH_ASSOC);
		$mission['nombre'] = $rappels['nombre'];
		
		// On recherche le nombre de numéros déjà appelés au sein de cette mission
		unset($query); unset($rappels);
		$query = $this->link->prepare('SELECT COUNT(*) AS `nombre` FROM `rappels` WHERE `argumentaire_id` = :argumentaire AND `rappel_statut` >= 2');
		$query->bindParam(':argumentaire', $mission['argumentaire_id']);
		$query->execute();
		$rappels = $query->fetch(PDO::FETCH_ASSOC);
		$mission['fait'] = $rappels['nombre'];
		
		// On affecte le contenu de $mission à la propriété $mission
		$this->mission = $mission;
    }
    
    
    /**
     * Récupère une information
     *
     * Cette méthode permet de récupérer une information dans les propriétés de 
     * la mission ouverte actuellement
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @param   string   $info     Nom de l'information demandée
     *
     * @result  mixed    $valeur   Information demandée
     */
    
    public function get( $info )
    {
        return $this->mission[ $info ];
    }
    
    
    /**
     * Modifie une donnée dans la base de données
     *
     * Cette méthode permet de modifier une donnée relative à la mission dans la
     * base de données et dans la propriété
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @param   string   $info    Information à modifier
     * @param   string   $valeur  Valeur à enregistrer
     *
     * @result  bool     Réussite ou non de l'opération
     */
    
    public function modification( $info , $valeur )
    {
        // On prépare la requête de modification
        $query = $this->link->prepare('UPDATE `argumentaires` SET `' . $info . '` = :valeur WHERE `argumentaire_id` = :id');
        $query->bindParam(':id', $this->mission['argumentaire_id'], PDO::PARAM_INT);
        $query->bindParam(':valeur', $valeur);
        $query->execute();
        
        $this->mission[ $info ] = $valeur;
    }
    
    
    /**
	 * Récupère les commentaires liés aux appels de cet argumentaire
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @result  Tableau des commentaires avec les informations connues
	 */
	
	public function commentaires() {
		// On recherche tous les appels de l'argumentaire avec reporting
		$query = $this->link->prepare('SELECT `contact_id`, `rappel_reporting` FROM `rappels` WHERE `rappel_statut` >= 2 AND `argumentaire_id` = :argumentaire AND `rappel_reporting` != ""');
		$query->bindParam(':argumentaire', $this->mission['argumentaire_id'], PDO::PARAM_INT);
		$query->execute();
		
		// On regarde s'il existe des lignes à afficher
		if ($query->rowCount()) {
			return $query->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}
    
    
    /**
	 * Récupère les demandes de procurations liés aux appels de cet argumentaire
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @result  Tableau des commentaires avec les informations connues
	 */
	
	public function procurations() {
		// On recherche tous les appels de l'argumentaire avec reporting
		$query = $this->link->prepare('SELECT `contact_id`, `rappel_reporting` FROM `rappels` WHERE `rappel_statut` = 3 AND `argumentaire_id` = :argumentaire');
		$query->bindParam(':argumentaire', $this->mission['argumentaire_id'], PDO::PARAM_INT);
		$query->execute();
		
		// On regarde s'il existe des lignes à afficher
		if ($query->rowCount()) {
			return $query->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}
    
    
    /**
	 * Récupère les demandes de recontact liés aux appels de cet argumentaire
	 * 
	 * @author  Damien Senger <mail@damiensenger.me>
	 * @version 1.0
	 * 
	 * @result  Tableau des commentaires avec les informations connues
	 */
	
	public function recontacts() {
		// On recherche tous les appels de l'argumentaire avec reporting
		$query = $this->link->prepare('SELECT `contact_id`, `rappel_reporting` FROM `rappels` WHERE `rappel_statut` = 4 AND `argumentaire_id` = :argumentaire');
		$query->bindParam(':argumentaire', $this->mission['argumentaire_id'], PDO::PARAM_INT);
		$query->execute();
		
		// On regarde s'il existe des lignes à afficher
		if ($query->rowCount()) {
			return $query->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}
    
    
    /**
     * Récupère une liste de toutes les missions créées de rappels
     *
     * Cette méthode permet de récupérer une liste complète de toutes les missions
     * créées selon leur statut (en cours ou terminées)
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @param   int   $statut  Statut des missions à récupérer (0 fini, 1 ouverte, -1 supprimées)
     *
     * @result  array          Tableau des missions
     */
    
    public static function liste( $statut = 1 )
    {
		// On commence par paramétrer les données PDO
		$link = Configuration::read('db.link');
		
        // On prépare la requête
        $query = $link->prepare('SELECT `argumentaire_id` FROM `argumentaires` WHERE `argumentaire_statut` = :statut ORDER BY `argumentaire_deadline` ASC, `argumentaire_creation` DESC');
        $query->bindParam(':statut', $statut);

        // On exécute la requête et on récupère les données
        $query->execute();
        $missions = $query->fetchAll(PDO::FETCH_ASSOC);

        // On retourne le tableau des différentes missions
        return $missions;
    }
    
    
    /**
     * Créé une nouvelle mission de rappels
     * 
     * Cette méthode statique permet de créer une nouvelle mission de rappel
     * vide prête à être paramétrée
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @result  int    Identifiant de la mission de rappels créée
     */
    
    public static function creer( )
    {
		// On commence par paramétrer les données PDO
		$link = Configuration::read('db.link');
		$userId = User::ID();

        // On prépare la requête
        $query = $link->prepare('INSERT INTO `argumentaires` (`createur_id`) VALUES (:id)');
        $query->bindParam(':id', $userId);
        
        // On exécute la requête
        $query->execute();
        
        // On récupère l'identifiant des données insérées
        $identifiant = $link->lastInsertId();
        
        // On retourne cet identifiant
        return $identifiant;
    }
    
    
    /**
     * Estime le nombre de numéros à contacter pour une mission donnée
     *
     * Cette méthode permet d'estimer le nombre de numéros à contacter pour une 
     * mission donnée
     *
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param   array  $args   Tableau des arguments de tri des numéros à contacter
     * 
     * @result  int    Identifiant de la mission de rappels créée
     */
     
    public static function estimation( array $args )
    {
		// On commence par paramétrer les données PDO
		$link = Configuration::read('db.link');

        // On prépare la requête de récupération de tous les numéros de téléphone
        // connus par le système
        $query = $link->prepare('SELECT * FROM `coordonnees` WHERE `coordonnee_type` != "email"');
        $query->execute();
        $numeros = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // On prépare le tableau des résultats du tri $num
        $num = array();
        
        // On retraite les arguments entrés
        if (isset($args['age']) && !empty($args['age']))
        {
            $age = array();
            $age['usr'] = explode(':', $args['age']);
            $age['min'] = $age['usr'][0];
            $age['max'] = $age['usr'][1];
        }
        else
        {
            $age = false;
        }
        
        if (isset($args['bureaux']) && !empty($args['bureaux']))
        {
            $bureaux = explode(',', $args['bureaux']);
        }
        else
        {
            $bureaux = false;
        }
        
        if (isset($args['thema']) && !empty($args['thema']))
        {
            $thema = $args['thema'];
        }
        else
        {
            $thema = false;
        }
        
        // On fait la boucle de tous les numéros, et on charge les informations pour chacun
        foreach ($numeros as $numero)
        {
            // On réinitialise le test
            $test = true;
            
            // On ouvre la fiche correspondante
            $contact = new People($numero['contact_id']);
            
            // On récupère son bureau de vote, son âge et ses tags
            $ageContact = $contact->age();
            $bureau = $contact->get('bureau');
            $tags = $contact->get('tags');
            
            // On vérifie si la fiche correspond aux arguments entrés, concernant l'âge
            if ($age && $test)
            {
                if ($ageContact <= $age['max'] && $ageContact >= $age['min'])
                {
                    // On le rajoute au test
                    $test = true;
                    $testAge = true;
                }
                else
                {
                    $test = false;
                    $testAge = false;
                }
            }
            
            // On fait les vérifications concernant le bureau de vote
            if ($bureaux && $test)
            {
                if (in_array($bureau, $bureaux))
                {
                    $test = true;
                    $testBureau = true;
                }
                else
                {
                    $test = false;
                    $testBureau = false;
                }
            }
            
            // On fait les vérifications concernant les thématiques
            if ($thema && $test)
            {
                if (in_array($thema, $tags))
                {
                    $test = true;
                }
                else
                {
                    $test = false;
                }
            }
            
            // Si le test est concluant, on l'ajoute à la liste des numéros à contacter
            // en utilisant comme key l'ID du contact pour éviter les doublons
            if ($test)
            {
                $id = $contact->get('id');
                $num[ $id ] = $numero['coordonnee_id'];
            }
        }
        
        // On calcule maintenant le nombre de numéros concluant
        $nombre = count($num);
        
        // On retourne cette estimation
        return $nombre;
    }
}
