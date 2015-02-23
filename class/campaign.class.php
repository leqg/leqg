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
        
        if ($this->_campaign['type'] == 'publi')
        {
            $query = Core::query('campaign-events-count');
            $query->bindValue(':campaign', $this->_campaign['id']);
            $query->execute();
            $count = $query->fetch(PDO::FETCH_NUM);
            $this->_campaign['count'] = $count[0];
        }
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
     * Update an information
     * 
     * @param   string  $data       Data to update
     * @param   string  $value      Value
     * @result  void
     * */
    public function update($data, $value)
    {
        $this->_campaign[$data] = $value;
        $link = Configuration::read('db.link');
        $query = $link->prepare('UPDATE `campagne` SET `'.$data.'` = :value WHERE `campagne_id` = :campaign');
        $query->bindValue(':value', $value);
        $query->bindValue(':campaign', $this->_campaign['id']);
        $query->execute();
    }
    
    
    /**
     * New object method
     * 
     * @param   string  $object     New object
     * @result  void
     * */
    public function object($object)
    {
        $this->_campaign['objet'] = $object;
        $query = Core::query('campaign-object');
        $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->bindValue(':object', $object);
        $query->execute();
    }
    
    
    /**
     * Recipients adding method
     * 
     * @param   array   $recipients     List of recipients' ID
     * @result  void
     * */
    public function recipients_add($recipients)
    {
        $query = Core::query('campaign-recipient-add');
        $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        
        foreach ($recipients as $element) {
            $query->bindParam(':person', $element, PDO::PARAM_INT);
            $query->execute();
        }
    }
    
    
    /**
     * Recipients list
     * 
     * @result  array
     * */
    public function recipients()
    {
        if ($this->_campaign['status'] == 'open') {
            $query = Core::query('campaign-recipients-list');
            $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
            $query->execute();
            $contacts = $query->fetchAll(PDO::FETCH_ASSOC);
            $recipients = array();
            
            foreach ($contacts as $contact) {
                switch ($this->_campaign['type']) {
                    case 'email':
                        $query = Core::query('emails-by-contact');
                        break;
                    
                    case 'sms':
                        $query = Core::query('mobiles-by-contact');
                        break;
                }
                $query->bindValue(':contact', $contact['contact'], PDO::PARAM_INT);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_NUM);
                
                foreach ($results as $result) {
                    $result['contact'] = $contact['contact'];
                    $result['status'] = 'ready';
                    $recipients[] = $result;
                }
            }
            
            return $recipients;
            
        } else {
            $query = Core::query('campaign-recipients');
            $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
            $query->execute();
            $recipients = $query->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($recipients as $key => $recipient) {
                switch ($this->_campaign['type']) {
                    case 'email':
                        $query = Core::query('contact-by-email');
                        break;
                    
                    case 'sms':
                        $query = Core::query('contact-by-mobile');
                        break;
                }
                $query->bindValue(':coord', $recipient['email']);
                $query->execute();
                $contact = $query->fetch(PDO::FETCH_NUM);
                $recipients[$key]['contact'] = $contact[0];
            }
        
            return $recipients;
        }
    }
    
    
    /**
     * Count number of items related to this campaign
     * @param   string  $target     What to count
     * @result  array
     * */
    public function count($target = null)
    {
        switch ($target) {
            // if we asked the number of send items & theirs status
            case 'items':
                $query = Core::query('campaign-items-count');
                $query->bindParam(':campaign', $this->_campaign['id']);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $nb = array();
                foreach ($result as $element) {
                    $nb[$element['status']] = $element['nb'];
                }
                if (count($nb)) {
                    $nb['all'] = array_sum($nb);
                } else {
                    $nb['all'] = 0;
                }
                return $nb;
                break;
            
            case 'emails':
                $query = Core::query('campaign-recipients-list');
                $query->bindValue(':campaign', $this->_campaign['id']);
                $query->execute();
                
                if ($query->rowCount()) {
                    $contacts = $query->fetchAll(PDO::FETCH_NUM);
                } else {
                    $contacts = false;
                }
                
                if ($contacts) {
                    $nb = 0;
                    foreach ($contacts as $contact) {
                        $query = Core::query('campaign-recipients-email-count');
                        $query->bindValue(':contact', $contact[0]);
                        $query->execute();
                        $count = $query->fetch(PDO::FETCH_NUM);
                        $nb = $nb + $count[0];
                    }
                    return $nb;
                } else {
                    return 0;
                }
                break;
            
            case 'mobile':
                $query = Core::query('campaign-recipients-list');
                $query->bindValue(':campaign', $this->_campaign['id']);
                $query->execute();
                
                if ($query->rowCount()) {
                    $contacts = $query->fetchAll(PDO::FETCH_NUM);
                } else {
                    $contacts = false;
                }
                
                if ($contacts) {
                    $nb = 0;
                    foreach ($contacts as $contact) {
                        $query = Core::query('campaign-recipients-mobile-count');
                        $query->bindValue(':contact', $contact[0]);
                        $query->execute();
                        $count = $query->fetch(PDO::FETCH_NUM);
                        $nb = $nb + $count[0];
                    }
                    return $nb;
                } else {
                    return 0;
                }
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
        if (!isset($this->_campaign['count']['emails'])) {
            $this->_campaign['count']['emails'] = $this->count('emails');
        }
        
        $hourlyQuota = Configuration::read('mail.quota');
        $target = $this->_campaign['count']['emails'];
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
        
        // number of emails
        $this->_campaign['count']['emails'] = $this->count('emails');
        
        // sending time estimation
        $this->_campaign['count']['time'] = $this->estimated_time();
        
        // number of send items
        $this->_campaign['count']['items'] = $this->count('items');
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
     * New empty template
     * 
     * @result  void
     * */
    public function template_empty()
    {
        $query = Core::query('campaign-empty-template');
        $query->bindParam(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
        $this->_campaign['template'] = '';
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
        $status = "send";
        $query = Core::query('campaign-new-status');
        $query->bindValue(':status', $status);
        $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
        	
        	$query = Core::query('campaign-contacts');
        	$query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        	$query->execute();
        	$contacts = $query->fetchAll(PDO::FETCH_NUM);
        
        switch($this->_campaign['type']) {
            case 'email':
                $mandrill = Configuration::read('mail');
                	$emails = array();
                
                	$query = Core::query('contact-emails');
                	foreach ($contacts as $contact) {
                    	$c = new People($contact[0]);
                    	$query->bindValue(':contact', $c->get('id'), PDO::PARAM_INT);
                    	$query->execute();
                    	$_emails = $query->fetchAll(PDO::FETCH_NUM);
                    	foreach ($_emails as $_email) {
                        	$emails[] = array(
                            	'email' => $_email[0],
                            	'name' => $c->display_name(),
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
                foreach ($result as $_element) $this->tracking($_element);
                break;
            
            case 'sms':
                	// On assure le démarrage du service
                	$service = new \Esendex\DispatchService(Configuration::read('sms'));
                	
                	$query = Core::query('mobiles-by-contact');
                	foreach ($contacts as $contact) {
                    	$query->bindParam(':contact', $contact[0], PDO::PARAM_INT);
                    	$query->execute();
                    	$_sms = $query->fetchAll(PDO::FETCH_NUM);
                    	
                    	foreach ($_sms as $_element) {
                        	$sms = new \Esendex\Model\DispatchMessage(
                        	    Configuration::read('sms.sender'),
                        	    $_element[0],
                        	    $this->_campaign['message'],
                        	    \Esendex\Model\Message::SmsType
                        	);
                        	
                        	$result = $service->send($sms);
                        	$this->tracking($result, $_element[0], $contact[0]);
                    	}
                	}
        }
    }
    
    
    /**
     * Launch an email tracking
     * 
     * @param   array   $result     Send email result
     * @param   string  $numero     Send numero
     * @param   int     $contact    Person ID
     * @result  void
     * */
    public function tracking($result, $numero = null, $contact = null)
    {
        switch ($this->_campaign['type']) {
            case 'email':
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
                        
                        $query = Core::query('contact-by-email');
                        $query->bindValue(':coord', $result['email']);
                        $query->execute();
                        $contact = $query->fetch(PDO::FETCH_ASSOC);

                        $event = Event::create($contact['contact']);
                        $event = new Event($event);
                        $event->update('historique_type', 'email');
                        $event->update('historique_objet', $this->_campaign['objet']);
                        $event->update('historique_date', date('Y-m-d'));
                        $event->update('campagne_id', $this->_campaign['id']);
                        
                        $e = new Evenement($contact['contact'], false, true);
                        $e->modification('historique_type', 'email');
                        $e->modification('campagne_id', $this->_campaign['id']);
                        $e->modification('historique_objet', $this->_campaign['objet']);
                        break;
                }
                break;
            
            case 'sms':
                $query = Core::query('campaign-sms-tracking');
                $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
                $query->bindValue(':numero', $numero);
                $query->bindValue(':id', $result->id());
                $query->execute();
                
                $event = Event::create($contact);
                $event = new Event($event);
                $event->update('historique_type', 'sms');
                $event->update('historique_objet', $this->_campaign['titre']);
                $event->update('historique_notes', $this->_campaign['message']);
                $event->update('historique_date', date('Y-m-d'));
                $event->update('campagne_id', $this->_campaign['id']);
                break;
        }
    }
    
    
    /**
     * Get errors informations
     * 
     * @result  array
     * */
    public function errors()
    {
        $mandrill = Configuration::read('mail');
        $query = Core::query('campaign-errors');
        $query->bindValue(':campaign', $this->_campaign['id']);
        $query->execute();
        $errors = $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($errors as $key => $error) {
            $infos = $mandrill->messages->info($error['id']);
            $errors[$key]['error'] = $infos['reject']['reason'];
            $errors[$key]['time'] = $infos['reject']['last_event_at'];
            
            $query = Core::query('contact-by-email');
            $query->bindValue(':coord', $error['email']);
            $query->execute();
            $contact = $query->fetch(PDO::FETCH_NUM);
            $errors[$key]['contact'] = $contact[0];
            
            $contact = new People($contact[0]);
            $errors[$key]['name'] = $contact->display_name();
        }
        
        return $errors;
    }
    
    /**
     * Price estimation
     * 
     * @result  int
     * */
    public function price()
    {
        switch ($this->_campaign['type']) {
            case 'email':
                $nombre = $this->count('emails');
                $pricePerThousand = Configuration::read('price.email');
                $price = $pricePerThousand/1000;
                $cost = $price * $nombre;
                break;
            
            case 'sms':
                $nombre = $this->count('mobile');
                $size = ceil(strlen($this->_campaign['message']) / Configuration::read('sms.size'));
                $price = Configuration::read('price.sms');
                $cost = $price * $nombre * $size;
                break;
        }
        
        return $cost;
    }
    
    
    /**
     * Update tracking status
     * 
     * @result  void
     * */
    public function tracking_update()
    {
        switch ($this->_campaign['type']) {
            case 'sms': 
                $service = new \Esendex\MessageHeaderService(Configuration::read('sms'));
                break;
                
            case 'email': 
                $service = Configuration::read('mail');
                break;
        }
        
        $query = Core::query('campaign-trackers');
        $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
        $trackers = $query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($trackers as $element) {
            switch ($this->_campaign['type']) {
                case 'sms':
                    $status = $service->message($element['id'])->status();
                
                    $query = Core::query('campaign-tracker-status-update');
                    $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
                    $query->bindValue(':coord', $element['coord']);
                    
                    switch ($status) {
                        case 'Delivered':
                            $query->bindValue(':status', "sent");
                            $query->execute();
                            break;
                            
                        default:
                            $query->bindValue(':status', "rejected");
                            $query->execute();
                            break;
                        
                    }
                    break;
                
                case 'email':
                    $status = $service->messages->info($element['id']);
                    
                    switch ($status['state']) {
                        case 'rejected':
                            $query = Core::query('campaign-tracker-status-reject');
                            $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
                            $query->bindValue(':coord', $status['email']);
                            $query->bindValue(':status', $status['state']);
                            $query->bindValue(':reason', $status['reject']['reason']);
                            $query->execute();
                            break;
                        
                        default:
                            $query = Core::query('campaign-tracker-status-update');
                            $query->bindValue(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
                            $query->bindValue(':coord', $status['email']);
                            $query->bindValue(':status', $status['state']);
                            $query->execute();
                            break;
                    }
                    break;
            }
        }
    }
    
    
    /**
     * List all persons with an event related to this campaign
     * 
     * @return  array
     * */
    public function list_events()
    {
        $query = Core::query('campaign-events-list');
        $query->bindValue(':campaign', $this->_campaign['id']);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Create a new campaign
     * 
     * @param   string  $method     Campaign method (email, sms, publi)
     * @return  int
     * */
    public static function create($method)
    {
        $user = User::ID();

        $query = Core::query('campaign-create');
        $query->bindParam(':type', $method);
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
    
    
    /**
     * Tracking informations
     * 
     * @param   string  $track_id   Tracking ID
     * @result  array               Tracking informations
     * */
    public static function tracking_infos($track_id)
    {
        $mandrill = Configuration::read('mail');
        $result = $mandrill->messages->info($track_id);
        return $result;
    }
    
    
    /**
     * Display status in french
     * 
     * @param   string  $status     Status to translate
     * @result  string
     * */
    public static function display_status($status)
    {
        switch ($status) {
            case 'send':
                return 'campagne lancée';
                break;
                
            case 'open':
                return 'campagne en préparation';
                break;
                
            case 'schedule':
                return 'campagne planifiée';
                break;
                
            case 'close':
                return 'campagne annulée';
                break;
                
            case 'ready':
                return 'prêt';
                break;
                
            case 'sent':
                return 'envoyé';
                break;
            
            case 'queued':
                return 'en cours';
                break;
            
            case 'scheduled':
                return 'départ prévu';
                break;
            
            case 'rejected':
                return 'en erreur';
                break;
            
            case 'invalid':
                return 'invalide';
                break;
        }
    }

}
