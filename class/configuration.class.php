<?php
/**
 * Gestion de la configuration
 *
 * PHP version 5
 *
 * @category Configuration
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * Gestion de la configuration
 *
 * PHP version 5
 *
 * @category Configuration
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class Configuration
{
    /**
     * Configuration data array
     *
     * @var array
     *
     * @static
     */
    public static $confArray = [];

    /**
     * Reading configuration method
     *
     * @param string $name data to read
     *
     * @return mixed
     * @static
     */
    public static function read(string $name)
    {
        return self::$confArray[$name];
    }

    /**
     * Writing configuration method
     *
     * @param string $name  Data name
     * @param mixed  $value Data value
     *
     * @return void
     * @static
     */
    public static function write(string $name, $value)
    {
        self::$confArray[$name] = $value;
    }
}
