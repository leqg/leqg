<?php
/**
 * Event's folder class
 *
 * PHP version 5
 *
 * @category Folder
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Event's folder class
 *
 * PHP version 5
 *
 * @category Folder
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Folder
{
    /**
     * Folder data
     * @var array
     */
    private $_folder = [];

    /**
     * Construct method
     *
     * @param integer $folder Folder ID (w/o hash)
     *
     * @return void
     **/
    public function __construct(int $folder)
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
     * @param string $data Asked information
     *
     * @return mixed
     **/
    public function get(string $data)
    {
        return $this->_folder[$data];
    }

    /**
     * Update an asked information
     *
     * @param string $data  Asked information
     * @param string $value New value
     *
     * @return void
     **/
    public function update(string $data, string $value)
    {
        $link = Configuration::read('db.link');
        $sql = 'UPDATE `dossiers`
                SET `' . $data . '` = :value
                WHERE `dossier_id` = :id';
        $query = $link->prepare($sql);
        $query->bindValue(':id', $this->_folder['id'], PDO::PARAM_INT);
        $query->bindValue(':value', $value);
        $query->execute();
    }

    /**
     * Export known informations in JSON
     *
     * @return string
     **/
    public function json()
    {
        return json_encode($this->_folder);
    }

    /**
     * List of all events linked to the folder
     *
     * @return array
     **/
    public function events()
    {
        $query = Core::query('folder-events');
        $query->bindValue(':folder', $this->_folder['id'], PDO::PARAM_INT);
        $query->execute();

        $events = array();
        $_events = $query->fetchAll(PDO::FETCH_NUM);
        foreach ($_events as $event) {
            $events[] = $event[0];
        }

        return $events;
    }

    /**
     * List of all tasks linked to the folder
     *
     * @return array
     **/
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
     **/
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
     * @param string $name Folder's name
     * @param string $desc Folder's description
     *
     * @return integer New folder ID
     * @static
     **/
    static public function create(string $name, string $desc)
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
     * @param integer $status Status of asked folders (1 open 0 close)
     *
     * @return array
     * @static
     **/
    static public function all($status = 1)
    {
        $query = Core::query('folder-all');
        $query->bindValue(':status', $status, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
