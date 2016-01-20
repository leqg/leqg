<?php

/**
 * La classe événement permet de gérer l'ensemble des informations liées
 * à des événements d'histoire liés à des contacts donnés.
 *
 * Cette classe comprend l'ensemble des propriétés et méthodes disponibles pour
 * un événement demandé, ainsi que des méthodes statiques liées à la recherche
 * ou au listage des événements liés à une fiche, un utilisateur ou un compte.
 * 
 * @package   leQG
 * @author    Damien Senger <mail@damiensenger.me>
 * @copyright 2014 MSG SAS – LeQG
 * @version   0.1
 */

class Evenement
{
    /**
     * @var    array    $evenement    Tableau des informations connues sur l'événement
     *                            ouvert lors de la construction de la classe
     * @var object    $link        Lien vers la base de données
     */
    private $evenement;
    public $link;
    
    
    /**
     * Constructeur de la classe Contact
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param string $evenement Identifiant de l'événement demandé (en cas de création, il s'agit de l'ID du contact lié) création, il s'agit de l'ID du contact lié)
     *                                création, il s'agit de l'ID du contact lié)
     * @param bool   $securite  Permet de savoir si l'idenfiant entré est hashé par MD5 ou non hashé par MD5 ou non
     *                                 hashé par MD5 ou non
     * @param bool   $creation  La méthode doit-elle créer un nouvel événement où est-ce un événement existant (true = création) où est-ce un événement existant (true = création)
     *                                où est-ce un événement existant (true = création)
     *
     * @result void
     */
     
    public function __construct( $evenement, $securite = true, $creation = false )
    {
        // On commence par paramétrer les données PDO
        $this->link = Configuration::read('db.link');
        
        // On regarde si on doit créer un nouvel événement, ou s'il s'agit d'un événement à ouvrir
        if ($creation === true) {
            // On prépare les variables
            if (isset($_COOKIE['leqg-user'])) { $user = $_COOKIE['leqg-user']; 
            } else { $user = 0; 
            }
            
            // On prépare la requête
            $query = $this->link->prepare('INSERT INTO `historique` (`contact_id`, `compte_id`, `historique_type`, `historique_date`) VALUES (' . $evenement . ', ' . $user . ', "autre", NOW())');
            
            // On exécute la requête
            $query->execute();
            
            // On récupère l'identifiant de l'événement créé
            $identifiant = $this->link->lastInsertId();
            
            // On effectue une recherche des informations liées à cet enregistrement
            $query = $this->link->prepare('SELECT * FROM `historique` WHERE `historique_id` = :evenement');
            $query->bindParam(':evenement', $identifiant);
            $query->execute();
            $evenements = $query->fetchAll();
            $evenement = $evenements[0];
            
            // On commence par retraiter la date de l'événement pour l'avoir en format compréhensible
            $evenement['historique_date_fr'] = date('d/m/Y', strtotime($evenement['historique_date']));
            
            // On retraite ensuite l'ID pour l'avoir au format MD5
            $evenement['historique_md5'] = md5($evenement['historique_id']);
                
            // On retraite ensuite le type en clair
            $evenement['historique_type_clair'] = Core::eventType($evenement['historique_type']);
            
            // On effectue une recherche des fichiers associés à l'événement
            unset($query);
            $query = $this->link->prepare('SELECT * FROM `fichiers` WHERE `interaction_id` = :evenement');
            $query->bindParam(':evenement', $identifiant);
            $query->execute();
            $fichiers = $query->fetchAll();
            
            // On modifie la liste des fichiers pour la formater en JSON
            $fichiers = json_encode($fichiers);
            
            // On ajoute la liste des fichiers associés à la liste des données connues
            $evenement['fichiers'] = $fichiers;
            
            // On effectue une recherche des tâches associées à l'événement
            unset($query);
            $query = $this->link->prepare('SELECT * FROM `taches` WHERE `historique_id` = :evenement AND `tache_terminee` = 0');
            $query->bindParam(':evenement', $identifiant);
            $query->execute();
            $taches = $query->fetchAll();
                        
            // On modifie la liste des tâches pour la formater en JSON
            $taches = json_encode($taches);
            
            // On ajoute la liste des tâches associées à la liste des données connues
            $evenement['taches'] = $taches;
            
            // On cherche les données sur le dossier, si un dossier est lié
            if ($evenement['dossier_id'] > 0) {
                    unset($query);
                    $query = $this->link->prepare('SELECT * FROM `dossiers` WHERE `dossier_id` = :id');
                    $query->bindParam(':id', $evenement['dossier_id']);
                    $query->execute();
                    $dossier = $query->fetch(PDO::FETCH_ASSOC);
                    $dossier['dossier_md5'] = md5($dossier['dossier_id']);
                    $dossier = json_encode($dossier);
                    
                    // On ajoute les informations sur le dossier à la liste des données connues
                    $evenement['dossier'] = $dossier;
            }
            
            // On retourne le tout dans la propriété privée evenement
            $this->evenement = $evenement;
            
        }
        // On ouvre, dans ce cas, l'événement demandé
        else
        {
            // On cherche maintenant à savoir s'il existe un contact ayant pour identifiant celui demandé
            if ($securite === true) {
                $query = $this->link->prepare('SELECT * FROM `historique` WHERE MD5(`historique_id`) = :evenement');
            }
            else
            {
                $query = $this->link->prepare('SELECT * FROM `historique` WHERE `historique_id` = :evenement');
            }
            $query->bindParam(':evenement', $evenement);
            $query->execute();
            $evenements = $query->fetchAll();
            
            // Si on ne trouve pas d'utilisateur, on retourne vers la page d'accueil du module contact
            if (!count($evenements)) {
                Core::goPage('contacts', true);
            }
            // Sinon, on affecte les données aux propriétés de l'objet
            else
            {
                // On commence par retraiter la date de l'événement pour l'avoir en format compréhensible
                $evenement = $evenements[0];
                $evenement['historique_date_fr'] = date('d/m/Y', strtotime($evenement['historique_date']));
            
                // On retraite ensuite l'ID pour l'avoir au format MD5
                $evenement['historique_md5'] = md5($evenement['historique_id']);
                
                // On retraite ensuite le type en clair
                $evenement['historique_type_clair'] = Core::eventType($evenement['historique_type']);
            
                // On effectue une recherche des fichiers associés à l'événement
                unset($query);
                $query = $this->link->prepare('SELECT * FROM `fichiers` WHERE `interaction_id` = :evenement');
                $query->bindParam(':evenement', $evenement['historique_id']);
                $query->execute();
                $fichiers = $query->fetchAll();
                
                // On modifie la liste des fichiers pour la formater en JSON
                $fichiers = json_encode($fichiers);
                
                // On ajoute la liste des fichiers associés à la liste des données connues
                $evenement['fichiers'] = $fichiers;
            
                // On effectue une recherche des tâches associées à l'événement
                unset($query);
                $query = $this->link->prepare('SELECT * FROM `taches` WHERE `historique_id` = :evenement AND `tache_terminee` = 0');
                $query->bindParam(':evenement', $evenement['historique_id']);
                $query->execute();
                $taches = $query->fetchAll();
                
                // On modifie la liste des tâches pour la formater en JSON
                $taches = json_encode($taches);
                
                // On ajoute la liste des tâches associées à la liste des données connues
                $evenement['taches'] = $taches;
            
                 // On cherche les données sur le dossier, si un dossier est lié
                if ($evenement['dossier_id'] > 0) {
                    unset($query);
                    $query = $this->link->prepare('SELECT * FROM `dossiers` WHERE `dossier_id` = :id');
                    $query->bindParam(':id', $evenement['dossier_id']);
                    $query->execute();
                    $dossier = $query->fetch(PDO::FETCH_ASSOC);
                    $dossier['dossier_md5'] = md5($dossier['dossier_id']);
                    $dossier = json_encode($dossier);
                        
                    // On ajoute les informations sur le dossier à la liste des données connues
                    $evenement['dossier'] = $dossier;
                }
                
                // On retourne le tout dans la propriété evenement
                $this->evenement = $evenement;
            }
        }
    }
    
    
    /**
     * Retourne en JSON les informations liées au tableau
     *
     * Cette méthode retourne toutes les informations connues sur l'événement
     * ouvert actuellement dans un format JSON, retraitées pour éviter les 
     * entitées HTML (&...)
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @return string
     */
    
    public function json_infos()
    {
        // On prépare la fonction de suppression des entités HTML
        function rendu($string)
        {
            return html_entity_decode($string, ENT_QUOTES);
        }
        
        // On applique la fonction au tableau des informations liées à l'événement
        $event = array_map("rendu", $this->evenement);
        
        // On retourne le tableau retraité des informations en JSON
        return json_encode($event);
    }
    
    
    /**
     * Détermine si un événement donné fait l'objet d'une fiche ou non
     *
     * Cette méthode retourne un booléen pour déterminer si un événement peut 
     * faire l'objet d'un lien vers sa fiche détaillée ou s'il s'agit d'un
     * élément d'historique ne pouvant faire l'objet d'un affichage détaillé
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @return bool
     */
    
    public function lien( )
    {
        // On détermine la liste des types possédant une fiche détaillée
        $ouvert = array('contact', 'telephone', 'email', 'courrier', 'autre', 'rappel');
        $campagne = array('sms', 'email', 'publi');
        
        // On regarde si l'événement fait l'objet d'une fiche événement
        if (in_array($this->get_infos('type'), $ouvert)) {
            return 2;
        }
        
        // On regarde si l'événement fait partie d'une campagne
        elseif (in_array($this->get_infos('type'), $campagne)) {
            return 1;
        }
        
        // Sinon, on informe qu'il s'agit d'un simple événement d'information
        else
        {
            return false;
        }
    }
    
    
    /**
     * Retourne une information connue
     *
     * Cette méthode permet de récupérer les informations connues sur l'événement
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param  array $infos Information demandée
     * @return mixed
     */
    
    public function get_infos( $infos )
    {
        return $this->evenement['historique_' . $infos];
    }
    
    
    /**
     * Retourne une information connue sans préfixe
     *
     * Cette méthode permet de récupérer les informations connues sur l'événement
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param  array $infos Information demandée
     * @return mixed
     */
    
    public function get( $infos )
    {
        return $this->evenement[$infos];
    }
    
    
    /**
     * Modifie les données dans la base de données
     *
     * Cette méthode permet de mettre à jour les informations de la base de données
     * concernant un champ de l'événément
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param  string $info  Information à modifier
     * @param  string $value Valeur à enregistrer
     * @return bool            Réussite ou non de l'opération
     */
     
    public function modification( $info , $value )
    {
        // On retraite la valeur s'il s'agit d'une demande de modification de la date
        if ($info == 'historique_date') {
            $value = explode('/', $value);
            $value = $value[2] . '-' . $value[1] . '-' . $value[0];
            
        }
        // Sinon on retraite les caractères spéciaux
        else
        {
            $value = Core::securisationString($value);
        }
        
        // On prépare la requête
        $query = $this->link->prepare('UPDATE `historique` SET `'. $info . '` = "' . $value . '" WHERE `historique_id` = ' . $this->evenement['historique_id']);
        
        // On exécute la modification
        if ($query->execute()) {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    
    /**
     * Supprime l'événement ouvert
     *
     * Cette méthode réalise la suppression de l'événement ouvert actuellement de
     * la base de données
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @result void
     */
    
    public function suppression()
    {
        $identifiant = $this->evenement['historique_id'];
        
        // On prépare la requête
        $query = $this->link->prepare('DELETE FROM `historique` WHERE `historique_id` = :event');
        
        // On affecte les informations discriminantes à la requête
        $query->bindParam(':event', $identifiant, PDO::PARAM_INT);
        
        // On lance la requête
        $query->execute();
        
        // On prépare maintenant la suppression des fichiers concernés par cet événement
        unset($query);
        $query = $this->link->prepare('DELETE FROM `fichiers` WHERE `interaction_id` = :event');
        $query->bindParam(':event', $identifiant);
        $query->execute();
        
        // On prépare la suppression des tâches liées à cet événement
        unset($query);
        $query = $this->link->prepare('DELETE FROM `taches` WHERE `historique_id` = :event');
        $query->bindParam(':event', $identifiant);
        $query->execute();
    }
    
    
    /**
     * Ajoute une tâche
     *
     * Cette méthode ajoute une tâche à l'événement ouvert actuellement
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param int    $user     Utilisateur concerné par la tâche
     * @param string $task     Tâche à ajouter
     * @param string $deadline Deadline demandée
     *
     * @result array                Informations sur la tâche ajoutée
     */
    
    public function tache_ajout( $user , $task , $deadline )
    {
        // On retraite la deadline
        if (!empty($deadline)) {
            $deadline = explode('/', $deadline);
            krsort($deadline);
            $deadline = implode('-', $deadline);
        } else {
            $deadline = '0000-00-00';
        }
        
        // On récupère l'ID de l'utilisateur
        $compte = (isset($_COOKIE['leqg-user'])) ? $_COOKIE['leqg-user'] : 0;
        
        // On prépare la requête
        $query = $this->link->prepare('INSERT INTO `taches` (`createur_id`, `compte_id`, `historique_id`, `tache_description`, `tache_deadline`) VALUES (:createur, :compte, :evenement, :description, :deadline)');
        $query->bindParam(':createur', $compte);
        $query->bindParam(':compte', $user);
        $query->bindParam(':evenement', $this->evenement['historique_id']);
        $query->bindParam(':description', $task);
        $query->bindParam(':deadline', $deadline);
        
        // On exécute la variable
        $query->execute();
        
        // On récupère l'identifiant
        $tache = $this->link->lastInsertId();
        
        // On récupère les informations connues sur cette tâche pour les renvoyer vers AJAX en JSON
        unset($query);
        $query = $this->link->prepare('SELECT * FROM `taches` WHERE `tache_id` = :tache');
        $query->bindParam(':tache', $tache);
        $query->execute();
        $tache = $query->fetchAll();
        
        return $tache;
    }
    
    
    /**
     * Supprimer une tâche
     *
     * Cette méthode permet de supprimer une tâche de l'événement ouvert actuellement
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @param int $task Tâche à supprimer
     *
     * @result void
     */
    
    public function tache_suppression( $task )
    {
        // On prépare la requête
        $query = $this->link->prepare('UPDATE `taches` SET `tache_terminee` = 1 WHERE `tache_id` = :tache');
        $query->bindParam(':tache', $task);
        
        // On exécute la requête
        $query->execute();
    }
    
    
    /**
         * Lie un dossier à l'événement
         *
         * Cette méthode permet de lier un dossier à l'événement actuellement ouvert
         *
         * @author  Damien Senger <mail@damiensenger.me>
         * @version 1.0
         * 
         * @param int $dossier Dossier à lier
         * 
         * @result void
         */
         
    public function lier_dossier( $dossier )
    {
        // On modifie l'information
        $this->modification('dossier_id', $dossier);
    }
    
    
    /**
     * Liste les dernières interactions
     *
     * Cette méthode statique permet de lister les x dernières interactions
     *
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     *
     * @param int $nombre Nombre d'interactions 
     *
     * @result array            Liste des interactions
     */
     
    public static function last( $nombre = 15 )
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');
        
        // On prépare la requête
        $query = $link->prepare('SELECT `historique_id` FROM `historique` WHERE ( `historique_type` = "contact" OR `historique_type` = "telephone" OR `historique_type` = "courriel" OR `historique_type` = "courrier" OR `historique_type` = "autre" ) ORDER BY `historique_date` DESC LIMIT 0, ' . $nombre);
        $query->execute();
        
        // On fait la liste des dernières interactions en question
        $interactions = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // On renvoit le tableau
        return $interactions;
    }
    
    
    /**
     * Liste les prochaines tâches à réaliser, par deadline
     * 
     * Cette méthode permet d'obtenir une liste des tâches à réaliser par deadline
     * avec les informations sur l'utilisateur concerné et la deadline
     * 
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @param int $nombre Nombre de tâches à retourner
     * 
     * @result array            Liste des prochaines tâches à réaliser
     */
    
    public static function taches( $nombre = 5 ) 
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');
        
        // On prépare la requête
        $query = $link->prepare('SELECT `tache_id`, `compte_id`, `historique_id`, `tache_description`, `tache_deadline` FROM `taches` WHERE `tache_terminee` = 0 ORDER BY `tache_deadline` DESC LIMIT 0,' . $nombre);
        $query->execute();
        
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        
        else {
            return false;
        }
    }
    
    
    /**
     * Liste les prochaines tâches à réaliser, par deadline
     * 
     * Cette méthode permet d'obtenir une liste des tâches à réaliser par deadline
     * avec les informations sur l'utilisateur concerné et la deadline
     * 
     * @author  Damien Senger <mail@damiensenger.me>
     * @version 1.0
     * 
     * @param int $nombre Nombre de tâches à retourner
     * 
     * @result array            Liste des prochaines tâches à réaliser
     */
    
    public static function taches_personnelles() 
    {
        // On prépare le lien vers la BDD
        $link = Configuration::read('db.link');
        $userId = User::ID();
        
        // On prépare la requête
        $query = $link->prepare('SELECT `tache_id`, `compte_id`, `historique_id`, `tache_description`, `tache_deadline` FROM `taches` WHERE `tache_terminee` = 0 AND `compte_id` = :compte ORDER BY `tache_deadline` DESC');
        $query->bindParam(':compte', $userId, PDO::PARAM_INT);
        $query->execute();
        
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        
        else {
            return false;
        }
    }
}