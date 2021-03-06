<?php
/**
 * Méthodes de traitement des campagnes email, SMS ou publipostage
 *
 * PHP version 5
 *
 * @category Campaign
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Méthodes de traitement des campagnes email, SMS ou publipostage
 *
 * PHP version 5
 *
 * @category Campaign
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Campaign
{
    /**
     * Campaign data array
     * @var array
     * */
    private $_campaign = [];

    /**
     * Constructor
     *
     * @param integer $campaign Campaign ID
     *
     * @return void
     **/
    public function __construct(int $campaign)
    {
        $query = Core::query('campaign-data');
        $query->bindParam(':campagne', $campaign, PDO::PARAM_INT);
        $query->execute();
        $this->_campaign = $query->fetch(PDO::FETCH_ASSOC);

        if ($this->_campaign['type'] == 'publi') {
            $query = Core::query('campaign-events-count');
            $query->bindValue(':campaign', $this->_campaign['id']);
            $query->execute();
            $count = $query->fetch(PDO::FETCH_NUM);
            $this->_campaign['count'] = $count[0];
        }
    }

    /**
     * Get a stored data
     *
     * @param string $data Asked data
     *
     * @return mixed
     **/
    public function get(string $data)
    {
        return $this->_campaign[$data];
    }

    /**
     * Update an information
     *
     * @param string $data  Data to update
     * @param mixed  $value Value
     *
     * @return void
     **/
    public function update(string $data, $value)
    {
        $this->_campaign[$data] = $value;
        $link = Configuration::read('db.link');
        $query = 'UPDATE `campagne`
                  SET `'.$data.'` = :value
                  WHERE `campagne_id` = :campaign';
        $query = $link->prepare($query);
        $query->bindValue(':value', $value);
        $query->bindValue(':campaign', $this->_campaign['id']);
        $query->execute();
    }

    /**
     * New object method
     *
     * @param string $object New object
     *
     * @return void
     **/
    public function object(string $object)
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
     * @param array $recipients List of recipients' ID
     *
     * @return void
     **/
    public function addRecipients(array $recipients)
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
     * @param int $first Première ligne appelée
     *
     * @return array
     **/
    public function recipients($first = null)
    {
        if ($this->_campaign['status'] == 'open') {
            if (is_null($first)) {
                $query = Core::query('campaign-recipients-list');
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
                $query->execute();
            } else {
                $query = file_get_contents(
                    'sql/campaign-recipients-list-limited.sql'
                );
                $query = str_replace(':first', $first, $query);
                $query = Configuration::read('db.link')->prepare($query);
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
                $query->execute();
            }
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
                $query->bindValue(
                    ':contact',
                    $contact['contact'],
                    PDO::PARAM_INT
                );
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
            if (is_null($first)) {
                $query = Core::query('campaign-recipients');
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
                $query->execute();
            } else {
                $query = file_get_contents('sql/campaign-recipients-limited.sql');
                $query = str_replace(':first', $first, $query);
                $query = Configuration::read('db.link')->prepare($query);
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
                $query->execute();
            }
            $recipients = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recipients as $key => $recipient) {
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
     *
     * @param string $target What to count
     *
     * @return array
     **/
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
     *
     * @return integer
     **/
    public function estimatedTime()
    {
        if (!isset($this->_campaign['count']['target'])) {
            $this->_campaign['count']['target'] = $this->count('emails');
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
     *
     * @return void
     **/
    public function stats()
    {
        // stats array init
        $stats = [];

        if ($this->_campaign['status'] == 'open') {
            // number of recipients
            $this->_campaign['count']['target'] = $this->count()[0];
            $this->_campaign['count']['time'] = $this->estimatedTime();
        } else {
            // number of send items
            $this->_campaign['count']['items'] = $this->count('items');
            $this->_campaign['count']['target'] = $this->count('items');
        }
    }

    /**
     * Display estimated time of this campaign
     *
     * @return string
     **/
    public function displayEstimatedTime()
    {
        if (!isset($this->_campaign['count']['time'])) {
            $this->_campaign['count']['time'] = $this->estimatedTime();
        }

        $time = $this->_campaign['count']['time'];
        $display = [];

        if (isset($time['months'], $time['weeks'], $time['days'])) {
            if ($time['months'] >= 1) {
                $display[] = $time['months'] . ' mois';
            }
            if ($time['weeks'] > 1) {
                $display[] = $time['weeks'] . ' semaines';
            }
            if ($time['weeks'] == 1) {
                $display[] = $time['weeks'] . ' semaine';
            }
            if ($time['days'] > 1) {
                $display[] = $time['days'] . ' jours';
            }
            if ($time['days'] == 1) {
                $display[] = $time['days'] . ' jour';
            }
            if ($time['hours'] > 1) {
                $display[] = round($time['hours']) . ' heures';
            }
            if ($time['hours'] == 1) {
                $display[] = $time['hours'] . ' heure';
            }

            $display = implode(', ', $display);
        } else {
            $display = 'Quelques minutes';
        }

        return $display;
    }

    /**
     * Return name of used template
     *
     * @return string
     **/
    public function usedTemplate()
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
     * @param string $template New template version
     *
     * @return void
     **/
    public function templateWrite(string $template)
    {
        $this->_campaign['template'] = $template;
        $query = Core::query('campaign-template-update');
        $query->bindParam(':template', $template);
        $query->bindParam(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
        $this->templateParsing();
    }

    /**
     * Copy an existing template to this campaign
     *
     * @param integer $campaign Existing template campaign ID
     *
     * @return void
     **/
    public function templateCopy(int $campaign)
    {
        $query = Core::query('campaign-template');
        $query->bindParam(':campaign', $campaign);
        $query->execute();
        $template = $query->fetch(PDO::FETCH_NUM);
        $this->templateWrite($template[0]);
    }

    /**
     * New empty template
     *
     * @return void
     **/
    public function templateEmpty()
    {
        $query = Core::query('campaign-empty-template');
        $query->bindParam(':campaign', $this->_campaign['id'], PDO::PARAM_INT);
        $query->execute();
        $this->_campaign['template'] = '';
    }

    /**
     * Template parsing method
     *
     * @return string
     **/
    public function templateParsing()
    {
        $template = $this->_campaign['template'];
        $nb_boucles_article = preg_match_all(
            "#\{articles\}(.+)\{finarticles\}#isU",
            $template,
            $boucles
        );

        $cur = 0;
        while ($cur < $nb_boucles_article) {
            // we load article tpl
            $tpl_articles = $boucles[1][$cur];
            $tpl_final = '';

            // we search last 5 articles
            $json = file_get_contents(
                'https://public-api.wordpress.com/rest/v1.1/sites/' .
                'preprod.leqg.info/posts/?category=newsletter'
            );
            $posts = json_decode($json)->posts;
            $posts = array_slice($posts, 0, 20);

            foreach ($posts as $post) {
                $tpl_article = $tpl_articles;
                $titre = '<a href="' . $post->URL . '">' . $post->title . '</a>';
                $contenu = $post->excerpt;
                $miniature_url = $post->post_thumbnail->URL;
                $miniature = '<img src="'.$miniature_url.'" ' .
                                  'style="width: 100%; height: auto;" ' .
                                  'alt="Miniature de l\'article">';
                $tpl_article = str_replace(
                    '{article:titre}',
                    $titre,
                    $tpl_article
                );
                $tpl_article = str_replace(
                    '{article:contenu}',
                    $contenu,
                    $tpl_article
                );
                $tpl_article = str_replace(
                    '{article:miniature}',
                    $miniature,
                    $tpl_article
                );
                $tpl_final .= $tpl_article;
                unset($tpl_article);
            }

            $template = preg_replace(
                "#\{articles\}(.+)\{finarticles\}#isU",
                $tpl_final,
                $template
            );

            $cur++;
        }

        $nb_boucles_agenda = preg_match_all(
            "#\{agenda\}(.+)\{finagenda\}#isU",
            $template,
            $boucles
        );

        $cur = 0;
        while ($cur < $nb_boucles_agenda) {
            // we load article tpl
            $tpl_articles = $boucles[1][$cur];
            $tpl_final = '';

            // we search next 5 events
            $host = Configuration::read('db.host');
            $port = 3306;
            $dbname = 'pcordery';
            $user = Configuration::read('db.user');
            $pass = Configuration::read('db.pass');
            $charset = 'utf8';
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
            $event_db = new PDO($dsn, $user, $pass);

            $query = 'SELECT *
                      FROM `wp_em_events`
                      WHERE `event_start_date` >= NOW()
                      AND `event_status` = 1
                      ORDER BY `event_start_date` ASC';
            $query = $event_db->prepare($query);
            $query->execute();
            $events = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($events as $post) {
                $tpl_article = $tpl_articles;
                $titre = '<a href="http://philipcordery.fr/evenets/' .
                         $post['event_slug'] . '">' . $post['event_name'] . '</a>';
                $date = date(
                    'd/m/Y H\hi',
                    strtotime(
                        $post['event_start_date'] . ' ' . $post['event_start_time']
                    )
                );
                $tpl_article = str_replace('{agenda:titre}', $titre, $tpl_article);
                $tpl_article = str_replace('{agenda:date}', $date, $tpl_article);
                $tpl_final .= $tpl_article;
                unset($tpl_article);
            }

            $template = preg_replace(
                "#\{agenda\}(.+)\{finagenda\}#isU",
                $tpl_final,
                $template
            );

            $cur++;
        }

        if (strstr($template, '{readonline}')) {
            $remplace = array(
                '{readonline}' => '<a href="http://' . Configuration::read('url') .
                                  '/mail-view.php?'.md5($this->_campaign['id']) .
                                  '" style="color: inherit; ' .
                                  'text-decoration: none;">',
                '{/readonline}' => '</a>'
            );
            $template = strtr($template, $remplace);
        }

        if (strstr($template, '{unsubscribe}')) {
            $remplace = array(
                '{unsubscribe}' => '<a href="http://' . Configuration::read('url') .
                                   '/mail-optout.php?test" ' .
                                   'style="color: inherit; ' .
                                   'text-decoration: none;">',
                '{/unsubscribe}' => '</a>'
            );
            $template = strtr($template, $remplace);
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
     * @return void
     **/
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
             $query = Core::query('contact-emails');
            foreach ($contacts as $contact) {
                $query->bindValue(':contact', $contact[0], PDO::PARAM_INT);
                $query->execute();
                $_emails = $query->fetchAll(PDO::FETCH_NUM);
                foreach ($_emails as $_email) {
                    $this->trackingNew($_email[0]);
                }
            }
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
     * Send a test email
     *
     * @return void
     **/
    public function testing()
    {
        $mail = $this->get('mail');

        $to = array(
            array(
                'email' => Configuration::read('mail.replyto'),
                'name' => Configuration::read('mail.sender.name'),
                'type' => 'to'
            )
        );

        $message = array(
            'html' => $mail,
            'subject' => '[LeQG – Mail test] '.$this->get('objet'),
            'from_email' => Configuration::read('mail.sender.mail'),
            'from_name' => Configuration::read('mail.sender.name'),
            'headers' => array('Reply-To' => Configuration::read('mail.replyto')),
            'to' => $to,
            'track_opens' => true,
            'auto_text' => true,
            'subaccount' => Configuration::read('client')
        );
        $async = true;

        $mandrill = Configuration::read('mail');
        $mandrill->messages->send($message, $async);
    }

    /**
     * Launch an email tracking
     *
     * @param array  $result  Send email result
     * @param string $numero  Send numero
     * @param int    $contact Person ID
     *
     * @return void
     **/
    public function tracking(array $result, $numero = null, $contact = null)
    {
        switch ($this->_campaign['type']) {
        case 'email':
            switch ($result['status']) {
            case 'rejected':
                $query = Core::query('campaign-tracking-reject');
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
                $query->bindValue(':id', $result['_id']);
                $query->bindValue(':email', $result['email']);
                $query->bindValue(':status', $result['status']);
                $query->bindValue(':reject', $result['reject_reason']);
                $query->execute();
                break;

            default:
                $query = Core::query('campaign-tracking');
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
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
     * Prepare a new email to go
     *
     * @param array $email Recipient's mail
     *
     * @return void
     **/
    public function trackingNew(array $email)
    {
        switch ($this->_campaign['type']) {
        case 'email':
            $query = Core::query('tracking-new');
            $query->bindValue(
                ':campaign',
                $this->_campaign['id'],
                PDO::PARAM_INT
            );
            $query->bindValue(':email', $email);
            $query->execute();
            break;
        }
    }

    /**
     * Get errors informations
     *
     * @return array
     **/
    public function errors()
    {
        $mandrill = Configuration::read('mail');
        $query = Core::query('campaign-errors');
        $query->bindValue(':campaign', $this->_campaign['id']);
        $query->execute();
        $errors = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($errors as $key => $error) {
            $query = Core::query('contact-by-email');
            $query->bindValue(':coord', $error['email']);
            $query->execute();
            $contact = $query->fetch(PDO::FETCH_NUM);
            $errors[$key]['contact'] = $contact[0];
        }

        return $errors;
    }

    /**
     * Price estimation
     *
     * @return int
     **/
    public function price()
    {
        switch ($this->_campaign['type']) {
        case 'email':
            if ($this->_campaign['status'] == 'open') {
                $nombre = $this->_campaign['count']['target'];
            } else {
                $nombre = $this->_campaign['count']['target']['all'];
            }
            $pricePerThousand = Configuration::read('price.email');
            $price = $pricePerThousand / 1000;
            $cost = $price * $nombre;
            break;

        case 'sms':
            $nombre = $this->count('mobile');
            $messageSize = $this->_campaign['message'];
            $size = ceil(strlen($messageSize) / Configuration::read('sms.size'));
            $price = Configuration::read('price.sms');
            $cost = $price * $nombre * $size;
            break;
        }

        return $cost;
    }

    /**
     * Update tracking status
     *
     * @return void
     **/
    public function trackingUpdate()
    {
        switch ($this->_campaign['type']) {
        case 'sms':
            $service = new \Esendex\MessageHeaderService(
                Configuration::read('sms')
            );
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
                $query->bindValue(
                    ':campaign',
                    $this->_campaign['id'],
                    PDO::PARAM_INT
                );
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
                    $query->bindValue(
                        ':campaign',
                        $this->_campaign['id'],
                        PDO::PARAM_INT
                    );
                    $query->bindValue(':coord', $status['email']);
                    $query->bindValue(':status', $status['state']);
                    $query->bindValue(':reason', $status['reject']['reason']);
                    $query->execute();
                    break;

                default:
                    $query = Core::query('campaign-tracker-status-update');
                    $query->bindValue(
                        ':campaign',
                        $this->_campaign['id'],
                        PDO::PARAM_INT
                    );
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
     * @return array
     **/
    public function listEvents()
    {
        $query = Core::query('campaign-events-list');
        $query->bindValue(':campaign', $this->_campaign['id']);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new campaign
     *
     * @param string $method Campaign method (email, sms, publi)
     *
     * @return int
     * @static
     **/
    public static function create(string $method)
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
     *
     * @param string $type Campaign method (email, sms, publi)
     *
     * @return array
     * @static
     **/
    public static function all(string $type)
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
     *
     * @return array
     * @static
     **/
    public static function templates()
    {
        $query = Core::query('campaign-templates-list');
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Email sending method
     *
     * @param integer $campaign Campaign ID
     * @param string  $email    Recipient's email
     *
     * @return void
     * @static
     **/
    public static function sending(int $campaign, string $email)
    {
        // campaign data
        $campaign = new Campaign($campaign);

        // recipient data
        $query = Core::query('contact-by-email');
        $query->bindValue(':coord', $email);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_NUM);
        $person = new People($data[0]);
        unset($data);

        $mail = $campaign->get('mail');
        $replace = array(
            'mail-optout.php?test' => 'mail-optout.php?email='.md5($email)
        );
        $mail = strtr($mail, $replace);

        $to = array(
            array(
                'email' => $email,
                'name' => $person->display_name(),
                'type' => 'to'
            )
        );

        $message = array(
            'html' => $mail,
            'subject' => $campaign->get('objet'),
            'from_email' => Configuration::read('mail.sender.mail'),
            'from_name' => Configuration::read('mail.sender.name'),
            'headers' => array('Reply-To' => Configuration::read('mail.replyto')),
            'to' => $to,
            'track_opens' => true,
            'auto_text' => true,
            'subaccount' => Configuration::read('client')
        );
        $async = true;

        $mandrill = Configuration::read('mail');

        $result = $mandrill->messages->send($message, $async);

        // we parse mail sending result
        $result = $result[0];

        // we save result data
        $query = Core::query('tracking-update');
        $query->bindValue(':campaign', $campaign->get('id'), PDO::PARAM_INT);
        $query->bindValue(':email', $result['email']);
        $query->bindValue(':id', $result['_id']);
        $query->bindValue(':status', $result['status']);
        $query->bindValue(':reject', $result['reject_reason']);
        $query->execute();
    }


    /**
     * Tracking informations
     *
     * @param string $track_id Tracking ID
     *
     * @return array
     * @static
     **/
    public static function trackingInfos(string $track_id)
    {
        $mandrill = Configuration::read('mail');
        $result = $mandrill->messages->info($track_id);
        return $result;
    }


    /**
     * Display status in french
     *
     * @param string $status Status to translate
     *
     * @return string
     * @static
     **/
    public static function displayStatus(string $status)
    {
        switch ($status) {
        case 'pending':
            return 'dans la file d\'attente';
                break;

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
