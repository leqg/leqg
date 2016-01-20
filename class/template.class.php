<?php
/**
 * Email template methods
 *
 * PHP version 5
 *
 * @category Template
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Email template methods
 *
 * PHP version 5
 *
 * @category Template
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Template
{
    /**
     * Template data array
     * @var array $_template
     */
    private $_template = [];

    /**
     * Class constructer, load asked template informations
     *
     * @param integer $template Template ID
     *
     * @return void
     */
    public function __construct(int $template)
    {
        $query = Core::query('template-data');
        $query->bindParam(':template', $template, PDO::PARAM_INT);
        $query->execute();
        $this->_template = $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a stored data
     *
     * @param string $data Asked data
     *
     * @return mixed
     */
    public function get(string $data)
    {
        return $this->_template[$data];
    }

    /**
     * Save a new version of this template
     *
     * @param string $template New version of this template
     *
     * @return void
     */
    public function write(string $template)
    {
        $this->_template['template'] = $template;
        $query = Core::query('template-write');
        $query->bindValue(':template', $template);
        $query->bindValue(':id', $this->_template['id'], PDO::PARAM_INT);
        $query->execute();
    }

    /**
     * List all templates by name
     *
     * @return array
     * @static
     */
    public static function all()
    {
        $query = Core::query('templates-list');
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new template
     *
     * @return integer
     * @static
     */
    public static function create()
    {
        $user = User::ID();
        $query = Core::query('template-create');
        $query->bindParam(':user', $user);
        $query->execute();
        return Configuration::read('db.link')->lastInsertId();
    }
}
