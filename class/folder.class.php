<?php
/**
 * LeQG – political database manager
 * Folder class
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
 * Folder class
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
class Folder
{
    
    /**
     * @var     array   $_folder    Folder data
     * */
    private $_folder;
    
    
    /**
     * Construct method
     * 
     * @param  string $folder Folder ID (w/o hash)
     * @result void
     * */
    public function __construct($folder)
    {
        $query = Core::query('folder-data');
        $query->bindParam(':folder', $folder, PDO::PARAM_INT);
        $query->execute();
        $this->_folder = $query->fetch(PDO::FETCH_ASSOC);
        $this->_folder['md5'] = md5($this->_folder['id']);
    }
    
    
    /**
     * Get an asked information
     * 
     * @param  string $data Asked information
     * @return mixed
     * */
    public function get($data)
    {
        return $this->_folder[$data];
    }
    
    
    /**
     * Update an asked information
     * 
     * @param  string $data  Asked information
     * @param  string $value New value
     * @result void
     * */
    public function update($data, $value)
    {
        $link = Configuration::read('db.link');
        $query = $link->prepare('UPDATE `dossiers` SET `'.$data.'` = :value WHERE `dossier_id` = :id');
        $query->bindValue(':id', $this->_folder['id'], PDO::PARAM_INT);
        $query->bindValue(':value', $value);
        $query->execute();
    }
    
    
    /**
     * Export known informations in JSON
     * 
     * @return string
     * */
    public function json()
    {
        return json_encode($this->_folder);
    }
    
    
    /**
     * List of all events linked to the folder
     * 
     * @return array
     * */
    public function events()
    {
        $query = Core::query('folder-events');
        $query->bindValue(':folder', $this->_folder['id'], PDO::PARAM_INT);
        $query->execute();
        
        $events = array();
        $_events = $query->fetchAll(PDO::FETCH_NUM);
        foreach ($_events as $event) { $events[] = $event[0]; 
        }
        
        return $events;
    }
    
    
    /**
     * List of all tasks linked to the folder
     * 
     * @return array
     * */
    public function tasks()
    {
        $query = Core::query('folder-tasks');
        $query->bindValue(':folder', $this->_folder['id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * List of all tasks linked to the folder
     * 
     * @return array
     * */
    public function files()
    {
        $query = Core::query('folder-files');
        $query->bindValue(':folder', $this->_folder['id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create a new folder
     * 
     * @param  string $name Folder's name
     * @param  string $desc Folder's description
     * @result int                 New folder ID
     * */
    public static function create($name, $desc)
    {
        $query = Core::query('folder-new');
        $query->bindValue(':name', $name);
        $query->bindValue(':desc', $desc);
        $query->execute();
        
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * List of all folders
     * 
     * @param  int $status Status of asked folders (1 open 0 close)
     * @return array
     * */
    public static function all($status = 1)
    {
        $query = Core::query('folder-all');
        $query->bindValue(':status', $status, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
