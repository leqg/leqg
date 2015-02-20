<?php
/**
 * LeQG – political database manager
 * Email Template class
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
 * Email Template class
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
class Template
{
    
    /**
     * Properties definition
     * @var array $_template Template data array
     * */
    private $_template = array();
    

    /**
     * Class constructer, load asked template informations
     * @param  string $template Template ID
     * @return void
     * */
    public function __construct($template) 
    {
        $query = Core::query('template-data');
        $query->bindParam(':template', $template, PDO::PARAM_INT);
        $query->execute();
        $this->_template = $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * List all templates by name
     * @result array
     * */
    public static function all()
    {
        $query = Core::query('templates-list');
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create a new template
     * @result int New template ID
     * */
    public static function create()
    {
        $user = User::ID();
        $query = Core::query('template-create');
        $query->bindParam(':user', $user);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }

} 
