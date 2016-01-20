<?php
/**
 * Event's class
 *
 * PHP version 5
 *
 * @category Event
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Event's class
 *
 * PHP version 5
 *
 * @category Event
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Event
{
    /**
     * Data items init
     *
     * @var array $_event  Event data
     * @var array $_files  Event's files
     * @var array $_tasks  Event's tasks
     * @var array $_folder Event's folder
     */
    private $_event, $_files, $_tasks, $_folder;

    /**
     * Construct method
     *
     * @param string $eventasked Event ID (w/o hash)
     *
     * @return void
     */
    public function __construct(string $eventasked)
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

        $this->_event['display_date'] = date(
            'd/m/Y',
            strtotime($this->_event['date'])
        );
    }

    /**
     * Get an asked information
     *
     * @param string $data Asked information
     *
     * @return mixed
     */
    public function get(string $data)
    {
        return $this->_event[$data];
    }

    /**
     * Update an asked information
     *
     * @param string $data  Asked information
     * @param string $value New value
     *
     * @return void
     */
    public function update(string $data, string $value)
    {
        $link = Configuration::read('db.link');
        $sql = 'UPDATE `historique`
                SET `'.$data.'` = :value
                WHERE `historique_id` = :id';
        $query = $link->prepare($sql);
        $query->bindValue(':id', $this->_event['id'], PDO::PARAM_INT);
        $query->bindValue(':value', $value);
        $query->execute();
    }

    /**
     * Data JSON export
     *
     * @return string
     */
    public function json()
    {
        $datas = $this->_event;
        $datas['files'] = $this->_files;
        $datas['tasks'] = $this->_tasks;
        if ($this->_event['folder']) {
            $datas['folder'] = $this->_folder;
        }

        return json_encode($datas);
    }

    /**
     * Return a boolean to know if an event's card exist
     *
     * @return integer 2 for an event's card,
     *                 1 for a campaign link,
     *                 0 for a simple data
     */
    public function link()
    {
        $campaign = ['sms', 'email', 'publi', 'porte', 'boite', 'rappel'];
        $card = ['contact', 'telephone', 'courriel', 'courrier', 'autre'];

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
     * @param integer $user     Assigned user ID
     * @param string  $task     Task assigned
     * @param string  $deadline Deadline assigned
     *
     * @return array
     */
    public function newTask(int $user, string $task, string $deadline)
    {
        if (!empty($deadline)) {
            $deadline = DateTime::createFromFormat('d/m/Y', $deadline);
            $deadline = $deadline->format('Y-m-d');
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
     * @param integer $task Task to remove
     *
     * @return void
     */
    public function removeTask(int $task)
    {
        $query = Core::query('task-delete');
        $query->bindValue(':task', $task, PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * Link to a folder
     *
     * @param integer $folder Folder ID
     *
     * @return void
     */
    public function linkFolder(int $folder)
    {
        $query = Core::query('event-folder');
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->bindValue(':folder', $folder, PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * Delete an event
     *
     * @return void
     */
    public function delete()
    {
        $query = Core::query('event-delete');
        $query->bindValue(':event', $this->_event['id'], PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * Create a new event
     *
     * @param integer $person Person ID for this event
     *
     * @return integer
     */
    public static function create(int $person)
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
     * @return array
     */
    public static function last()
    {
        $query = Core::query('events-last');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tasks to come, by deadline
     *
     * @param integer $user User ID related, if asked
     *
     * @return array
     */
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
     * @param string $type Type asked
     *
     * @return string
     * @static
     */
    static public function displayType($type)
    {
        $display = [
            'contact' => 'Entrevue',
            'telephone' => 'Entretien téléphonique',
            'courriel' => 'Échange électronique',
            'sms' => 'Envoi SMS',
            'email' => 'Envoi d\'un email',
            'publi' => 'Publipostage',
            'porte' => 'Porte-à-porte',
            'boite' => 'Boîtage',
            'rappel' => 'Rappel militant',
            'autre' => 'Divers'
        ];
        return $display[$type];
    }
}
