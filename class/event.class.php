<?php
/**
 * LeQG – political database manager
 * Event class
 * 
 * PHP Version 5.5.21
 * 
 * @category  LeQG
 * @package   LeQG
 * @author    Damien Senger <hi@hiwelo.co>
 * @copyright 2014-2015 MSG SAS – LeQG
 * @license   Personal Use Only
 * @version   GIT:<git@github.com:hiwelo/leqg.git>
 * @link      http://hiwelo.co/
 * */

/**
 * LeQG – political database manager
 * Event class
 * 
 * PHP Version 5.5.21
 * 
 * @category  LeQG
 * @package   LeQG
 * @author    Damien Senger <hi@hiwelo.co>
 * @copyright 2014-2015 MSG SAS – LeQG
 * @license   Personal Use Only
 * @link      http://hiwelo.co/
 * */
class Event
{
    
    /**
     * @var     array   $_event     Event data
     * @var     array   $_files     Event's files
     * @var     array   $_tasks     Event's tasks
     * @var     array   $_folder    Event's folder
     * */
    private $_event, $_files, $_tasks, $_folder;
    
    
    /**
     * Construct method
     * 
     * @param   string  $eventasked Event ID (w/o hash)
     * @result  void
     * */
    public function __construct($eventasked)
    {
        $query = Core::query('event-data');
        $query->bindValue(':event', $eventasked, PDO::PARAM_INT);
        $query->execute();
        $this->_event = $query->fetch(PDO::FETCH_ASSOC);
        
        $query = Core::query('event-files');
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->execute();
        $this->_files = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $query = Core::query('event-tasks');
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->execute();
        $this->_tasks = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if ($this->_event['folder']) {
            $query = Core::query('folder-data');
            $query->bindValue(':folder', $this->_event['folder'], PDO::PARAM_INT);
            $query->execute();
            $this->_folder = $query->fetch(PDO::FETCH_ASSOC);
        }
        
        $this->_event['display_date'] = date('d/m/Y', strtotime($this->_event['date']));
    }
    
    
    /**
     * Get an asked information
     * 
     * @param   string  $data       Asked information
     * @result  mixed
     * */
    public function get($data)
    {
        return $this->_event[$data];
    }
    
    
    /**
     * Update an asked information
     * 
     * @param   string  $data       Asked information
     * @param   string  $value      New value
     * @result  void
     * */
    public function update($data, $value)
    {
        $link = Configuration::read('db.link');
        $query = $link->prepare('UPDATE `historique` SET `'.$data.'` = :value WHERE `historique_id` = :id');
        $query->bindValue(':id', $this->_event['id'], PDO::PARAM_INT);
        $query->bindValue(':value', $value);
        $query->execute();
    }
    
    
    /**
     * Data JSON export
     * 
     * @return  string
     * */
    public function json()
    {
        $datas = $this->_event;
        $datas['files'] = $this->_files;
        $datas['tasks'] = $this->_tasks;
        if ($this->_event['folder']) $datas['folder'] = $this->_folder;
        
        return json_encode($datas);
    }
    
    
    /**
     * Return a boolean to know if an event's card exist
     * 
     * @result  int                 2 for an event's card,
     *                              1 for a campaign link,
     *                              0 for a simple data
     * */
    public function link()
    {
        $campaign = array('sms', 'email', 'publi', 'porte', 'boite', 'rappel');
        $card = array('contact', 'telephone', 'courriel', 'courrier', 'autre');
        
        if (in_array($this->_event['type'], $card)) {
            return 2;
        } elseif (in_array($this->_event['type'], $campaign)) {
            return 1;
        } else {
            return 0;
        }
    }
    
    
    /**
     * Add a new task
     * 
     * @param   int     $user       Assigned user ID
     * @param   string  $task       Task assigned
     * @param   string  $deadline   Deadline assigned
     * @return  array               Task informations
     * */
    public function task_new($user, $task, $deadline)
    {
        if (!empty($deadline)) {
            $deadline = DateTime::createFromFormat('d/m/Y', $deadline)->format('Y-m-d');
        } else {
            $deadline = null;
        }
        
        $createur = User::ID();
        
        $query = Core::query('task-new');
        $query->bindValue(':createur', $createur, PDO::PARAM_INT);
        $query->bindValue(':user', $user, PDO::PARAM_INT);
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->bindValue(':task', $task);
        $query->bindValue(':deadline', $deadline);
        $query->execute();
        
        $task = Configuration::read('db.link')->lastInsertId();
        $query = Core::query('task-data');
        $query->bindValue(':task', $task, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Remove a task
     * 
     * @param   int     $task       Task to remove
     * @return  void
     * */
    public function task_remove($task)
    {
        $query = Core::query('task-delete');
        $query->bindValue(':task', $task, PDO::PARAM_INT);
        $query->execute();
    }
    
    
    /**
     * Link to a folder
     * 
     * @param   int     $folder     Folder
     * @return  void
     * */
    public function link_folder($folder)
    {
        $query = Core::query('event-folder');
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->bindValue(':folder', $folder, PDO::PARAM_INT);
        $query->execute();
    }
    
    
    /**
     * Delete an event
     * 
     * @return  void
     * */
    public function delete()
    {
        $query = Core::query('event-delete');
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->execute();
    }
    
    
    /**
     * Create a new event
     * 
     * @param   int     $person     Person ID for this event
     * @return  int
     * */
    public static function create($person)
    {
        $user = User::ID();
        $query = Core::query('event-new');
        $query->bindValue(':person', $person, PDO::PARAM_INT);
        $query->bindValue(':user', $user, PDO::PARAM_INT);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * Last 15 events
     * 
     * @result  array
     * */
    public static function last()
    {
        $query = Core::query('events-last');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Tasks to come, by deadline
     * 
     * @param   int     $user       User ID related, if asked
     * @result  array
     * */
    public static function tasks($user = null)
    {
        if (is_null($user)) {
            $query = Core::query('tasks-all');
            $query->execute();
        } else {
            $query = Core::query('tasks');
            $query->bindValue(':user', $user, PDO::PARAM_INT);
            $query->execute();
        }
        
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }
    
    
    /**
     * Display name of a type of event
     * 
     * @param   string  $type       Type asked
     * @result  string
     * */
    public static function display_type($type)
    {
        $display = array(
            'contact'   => 'Entrevue',
            'telephone' => 'Entretien téléphonique',
            'courriel'  => 'Échange électronique',
            'sms'       => 'Envoi SMS',
            'email'     => 'Envoi d\'un email',
            'publi'     => 'Publipostage',
            'porte'     => 'Porte-à-porte',
            'boite'     => 'Boîtage',
            'rappel'    => 'Rappel militant',
            'autre'     => 'Divers'
        );

        return $display[$type];
    }
    
}
