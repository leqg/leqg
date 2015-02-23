<?php
/**
 * LeQG – political database manager
 * User management class
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
 * User management class
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
class User {
	
	/**
     * Check auth level of an user
     * 
     * @param   int     $auth       Auth level asked to show content
     * @result  bool
     * */
	public static function protection($auth = 1)
	{
    	    if (isset($_COOKIE['leqg'], $_COOKIE['time']) && !empty($_COOKIE['time']) && !empty($_COOKIE['leqg'])) {
        	    $query = Core::query('user-data-cookie', 'core');
        	    $query->bindValue(':cookie', $_COOKIE['leqg'], PDO::PARAM_INT);
        	    $query->execute();
        	    
        	    if ($query->rowCount() == 1) {
            	    $data = $query->fetch(PDO::FETCH_ASSOC);
            	    
            	    if ($data['client'] == Configuration::read('client')) {
                	    if ($data['auth_level'] >= $auth) {
                    	    return true;
                	    } else {
						header('Location: http://'.$data['client'].'.leqg.info');
                	    }
                	    
            	    } else {
					header('Location: http://'.$infos['client'].'.leqg.info'.$_SERVER['PHP_SELF']);
            	    }
            	    
        	    } else {
            	    setcookie('leqg', null, time(), '/', 'leqg.info');
            	    setcookie('time', null, time(), '/', 'leqg.info');
            	    header('Location: http://auth.leqg.info');
        	    }
        	    
    	    } else {
        	    setcookie('leqg', null, time(), '/', 'leqg.info');
        	    setcookie('time', null, time(), '/', 'leqg.info');
        	    header('Location: http://auth.leqg.info');
    	    }
	}
	
	
	/**
    	 * Auth level of the current user
    	 * 
    	 * @return  int
    	 * */
	public static function auth_level()
	{
    	    $query = Core::query('user-data-cookie', 'core');
    	    $query->bindValue(':cookie', $_COOKIE['leqg'], PDO::PARAM_INT);
    	    $query->execute();
    	    
    	    if ($query->rowCount() == 1) {
        	    $data = $query->fetch(PDO::FETCH_ASSOC);
        	    return $data['auth_level'];
    	    } else {
        	    return 0;
    	    }
	}
	
	
	/**
     * Current user ID
     * 
     * @return  int
     * */
	public static function ID()
	{
    	    $query = Core::query('user-data-cookie', 'core');
    	    $query->bindValue(':cookie', $_COOKIE['leqg'], PDO::PARAM_INT);
    	    $query->execute();
    	    $data = $query->fetch(PDO::FETCH_ASSOC);
    	    return $data['id'];
	}
	
	
	/**
     * Current user's informations
     * 
     * @param   int     $user       User ID
     * @return  int
     * */
	public static function data($user)
	{
    	    $query = Core::query('user-data', 'core');
    	    $query->bindValue(':user', $user, PDO::PARAM_INT);
    	    $query->execute();
    	    return $query->fetch(PDO::FETCH_ASSOC);
	}
	
	
	/**
    	 * List all user of this account
    	 * 
    	 * @param   int     $auth_level     Auth level required for this list
    	 * @return  array
    	 * */
	public static function all($auth_level = 5)
	{
    	    $user = User::data(User::ID());
    	    
    	    $query = Core::query('user-list', 'core');
    	    $query->bindValue(':auth', $auth_level, PDO::PARAM_INT);
    	    $query->bindValue(':client', $user['client'], PDO::PARAM_INT);
    	    $query->execute();
    	    
    	    return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
    /**
     * Get an user's login by its id
     * 
     * @param   int     $user       Asked user ID
     * @result  string
     * */
	public static function get_login_by_ID($user) 
	{
    	    $data = User::data($user);
    	    return $data['firstname'].' '.$data['lastname'];
	}
	
	
	/**
    	 * Logout current user
    	 * 
    	 * @result  void
    	 * */
	public static function logout()
	{
		setcookie('leqg', 0, time());
		setcookie('time', 0, time());
		header('Location: http://auth.leqg.info/');
	}
	
	
    /**
     * List all client's user except each user asked
     * 
     * @param   array  $sauf        User to exclude
     * @return  array
     * */
    public static function sauf($sauf)
    {
    	    $user = User::data(User::ID());
    
        if (empty($sauf)) {
            return User::all(0);
        } else {
            $query = Core::query('user-list-except', 'core');
        	    $query->bindValue(':client', $user['client'], PDO::PARAM_INT);
        	    $query->bindValue(':exclude', implode(',', $sauf));
        	    $query->execute();
        	    return $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    
}
