<?php
/**
 * LeQG – political database manager
 * Configuration class
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
 * Configuration class
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
class Configuration
{
    
    /**
     * @var array $confArray Configuration data array
     * */
	static $confArray;
	
	/**
    	 * Reading configuration method
    	 * @param string $name Saved conf data name
    	 * @result mixed
    	 * */
	public static function read($name)
	{
		return self::$confArray[$name];
	}
	
	/**
    	 * Writing configuration method
    	 * @param string $name To save data name
    	 * @param mixed $value To save data content
    	 * @result void
    	 * */	
	public static function write($name, $value)
	{
		self::$confArray[$name] = $value;
	}
	
}
