<?php
/**
 * LeQG – political database manager
 * Campaign class
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
 * Campaign class
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
class Campaign
{
    
    /**
     * Properties definition
     * @var array $_campaign Campaign data array
     * */
    private $_campaign = array();
    

    /**
     * Class constructer, load asked campaign informations
     * @param  string $campaign Campaign ID
     * @return void
     * */
    public function __construct($campaign) 
    {
        $query = Core::query('campaign-data');
        $query->bindParam(':campagne', $campaign, PDO::PARAM_INT);
        $query->execute();
        $this->_campaign = $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Get a stored data
     * @param  string $data Asked data
     * @return mixed
     * */
    public function get($data) 
    {
        return $this->_campaign[ $data ];
    }
    
    
    /**
     * Count number of items related to this campaign
     * @param  string $target What to count
     * @result array
     * */
    public function count($target = null)
    {
        switch ($target) {
            // if we asked the number of send items & theirs status
            case 'items':
            
                break;
            
            // if we asked the number of recipients
            default:
                $query = Core::query('campaign-recipients-count');
                $query->bindParam(':campaign', $this->_campaign['id']);
                $query->execute();
                return $query->fetch(PDO::FETCH_NUM);
                break;
        }
    }
    
    
    /**
     * Estimated sending time
     * @result int
     * */
    public function estimated_time()
    {
        if (!isset($this->_campaign['count']['target'])) {
            $this->_campaign['count']['target'] = $this->count()[0];
        }
        
        $hourlyQuota = Configuration::read('mail.quota');
        $target = $this->_campaign['count']['target'];
        $hoursNeeded = $target / $hourlyQuota;
        $result = array('months' => 0, 'weeks' => 0, 'days' => 0, 'hours' => 0);

        if ($hoursNeeded > 24) {
            $result['days'] = floor($hoursNeeded / 24);
            $result['hours'] = $hoursNeeded - ($result['days'] * 24);
            
            if ($result['days'] > 7) {
                $result['weeks'] = floor($result['days'] / 7);
                $result['days'] = $result['days'] - ($result['weeks'] * 7);
            }
            
            if ($result['weeks'] > 4) {
                $result['days'] = $result['weeks'] * 7 + $result['days'];
                $result['weeks'] = 0;
                $result['months'] = floor($result['days'] / 30);
                $result['days'] = $result['days'] - ($result['months'] * 30);
                
                if ($result['days'] > 7) {
                    $result['weeks'] = floor($result['days'] / 7);
                    $result['days'] = $result['days'] - ($result['weeks'] * 7);
                }
            }
        } else {
            unset($result);
            $result['hours'] = floor($hoursNeeded);
        }
        
        return $result;
    }
    
    
    /**
     * Get global stats of a campaign and store them into $_campaign
     * @result void
     * */
    public function stats()
    {
        // stats array init
        $stats = array();
        
        // number of recipients
        $this->_campaign['count']['target'] = $this->count()[0];
        
        // sending time estimation
        $this->_campaign['count']['time'] = $this->estimated_time();
    }
    
    
    /**
     * Display estimated time of this campaign
     * @result string
     * */
    public function display_estimated_time()
    {
        if (!isset($this->_campaign['count']['time'])) {
            $this->_campaign['count']['time'] = $this->estimated_time();
        }
        
        $time = $this->_campaign['count']['time'];
        $display = array();
        
        if (isset($time['months'], $time['weeks'], $time['days'])) {
            if ($time['months'] >= 1) $display[] = $time['months'] . ' mois';
            if ($time['weeks'] > 1) $display[] = $time['weeks'] . ' semaines';
            if ($time['weeks'] == 1) $display[] = $time['weeks'] . ' semaine';
            if ($time['days'] > 1) $display[] = $time['days'] . ' jours';
            if ($time['days'] == 1) $display[] = $time['days'] . ' jour';
            if ($time['hours'] > 1) $display[] = $time['hours'] . ' heures';
            if ($time['hours'] == 1) $display[] = $time['hours'] . ' heure';
            
            $display = implode(', ', $display);
        } else {
            $display = 'Quelques minutes';
        }
        
        return $display;
    }
    
    
    /**
     * Return name of used template
     * @result string
     * */
    public function used_template()
    {
        if ($this->_campaign['template']) {
            $query = Core::query('campaign-template-name');
            $query->bindParam(':template', $this->_campaign['template']);
            $query->execute();
            return $query->fetch(PDO::FETCH_NUM)[0];
        } else {
            return false;
        }
    }
    
    
    /**
     * Update this campaign template
     * 
     * @param   string  $tempalte       New template version
     * @result  void
     * */
    public function template_write($template)
    {
        $this->_campaign['template'] = $template;
        $template = $this->template_parsing();
        $query = Core::query('campaign-template-update');
        $query->bindParam(':template', $template);
        $query->bindParam(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
    }
    
    
    /**
     * Copy an existing template to this campaign
     * 
     * @param   int     $campaign       Existing template campaign ID
     * @result  void
     * */
    public function template_copy($campaign)
    {
        $query = Core::query('campaign-template');
        $query->bindParam(':campaign', $campaign);
        $query->execute();
        $template = $query->fetch(PDO::FETCH_NUM);
        
        $this->template_write($template[0]);
    }
    
    
    /**
     * Template parsing method
     * 
     * @result  string                  Parsed template
     * */
    public function template_parsing()
    {
        $template = $this->_campaign['template'];
        $nb_boucles = preg_match_all("#\{boucle\:(.+)\}(.+)\{finboucle\}#isU", $template, $boucles);
        
        $cur = 0;
        while($cur < $nb_boucles) {
            $data = $boucles[1][$cur];
            
            switch ($data) {
                case 'articles':
                    // we load article tpl
                    $tpl_articles = $boucles[2][$cur];
                    $tpl_final = '';
                    
                    // we search last 5 articles
                    $json = file_get_contents('https://public-api.wordpress.com/rest/v1.1/sites/preprod.leqg.info/posts/');
                    $posts = json_decode($json)->posts;
                    $posts = array_slice($posts, 0, 5);
                    
                    foreach ($posts as $post) {
                        $tpl_article = $tpl_articles;
                        $titre = '<a href="' . $post->URL . '">' . $post->title . '</a>';
                        $contenu = $post->excerpt;
                        $tpl_article = str_replace('{article:titre}', $titre, $tpl_article);
                        $tpl_article = str_replace('{article:contenu}', $contenu, $tpl_article);
                        $tpl_final .= $tpl_article;
                        unset($tpl_article);
                    }
                    
                    $template = preg_replace("#\{boucle\:(.+)\}(.+)\{finboucle\}#isU", $tpl_final, $template);
                    
                    break;
            }
            
            $cur++;
        }

        $this->_campaign['mail'] = $template;
        $query = Core::query('campaign-template-parsed');
        $query->bindValue(':mail', $template);
        $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
        return $template;
    }
    
    
    /**
     * Launch the campaign
     * 
     * @return  void
     * */
    public function launch()
    {
        $mandrill = Configuration::read('mail');
        	
        	$query = Core::query('campaign-contacts');
        	$query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        	$query->execute();
        	$contacts = $query->fetchAll(PDO::FETCH_NUM);
        	$emails = array();

        	$query = Core::query('contact-emails');
        	foreach ($contacts as $contact) {
            	$c = new Contact(md5($contact[0]));
            	$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
            	$query->execute();
            	$_emails = $query->fetchAll(PDO::FETCH_NUM);
            	foreach ($_emails as $_email) {
                	$emails[] = array(
                    	'email' => $_email[0],
                    	'name' => $c->get('nom_affichage'),
                    	'type' => 'bcc'
                	);
            }
        	}
        	
        	$message = array(
            	'html' => $this->_campaign['mail'],
            'subject' => $this->_campaign['objet'],
            'from_email' => 'noreply@leqg.info',
            'from_name' => 'LeQG',
            'to' => $emails,
            'headers' => array('Reply-To' => 'tech@leqg.info'),
            'track_opens' => true,
            'auto_text' => true
        	);
        	$async = true;
        	$result = $mandrill->messages->send($message, $async);
        
        foreach ($result as $email) $this->tracking($email);
    }
    
    
    /**
     * Launch an email tracking
     * 
     * @param   array   $result     Send email result
     * @result  void
     * */
    public function tracking($result)
    {
        switch ($result['status']) {
            case 'rejected':
                $query = Core::query('campaign-tracking-reject');
                $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
                $query->bindValue(':id', $result['_id']);
                $query->bindValue(':email', $result['email']);
                $query->bindValue(':status', $result['status']);
                $query->bindValue(':reject', $result['reject_reason']);
                $query->execute();
                break;
            
            default:
                $query = Core::query('campaign-tracking');
                $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
                $query->bindValue(':id', $result['_id']);
                $query->bindValue(':email', $result['email']);
                $query->bindValue(':status', $result['status']);
                $query->execute();
                break;
        }
    }
    
    
    /**
     * Create a new campaign
     * @param  string $method Campaign method (email, sms, publi)
     * @return int
     * */
    public static function create($method)
    {
        $user = User::ID();

        $query = Core::query('campaign-create');
        $query->bindParam(':type', $type);
        $query->bindParam(':user', $user, PDO::PARAM_INT);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
    
    
    /**
     * List all campaigns by type
     * @param  string $type Campaign method (email, sms, publi)
     * @return array
     * */
    public static function all($type) 
    {
        $query = Core::query('campaigns-list');
        $query->bindParam(':type', $type);
        $query->execute();
        
        if ($query->rowCount()) {
             return $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
             return array();
        }
    }
    
    
    /**
     * List all templates by name
     * @result array
     * */
    public static function templates()
    {
        $query = Core::query('campaign-templates-list');
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
