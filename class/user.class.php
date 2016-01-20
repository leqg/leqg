<?php
/**
 * User system methods
 *
 * PHP version 5
 *
 * @category User
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

/**
 * User system methods
 *
 * PHP version 5
 *
 * @category User
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */
class User
{
    /**
     * Check auth level of an user
     *
     * @param integer $auth Auth level asked to show content
     *
     * @return boolean
     * @static
     */
    public static function protection($auth = 1)
    {
        if (isset($_COOKIE['leqg'], $_COOKIE['time'])
            && !empty($_COOKIE['time'])
            && !empty($_COOKIE['leqg'])
        ) {
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
                    header(
                        'Location: http://' . $data['client'] . '.leqg.info' .
                        $_SERVER['PHP_SELF']
                    );
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
     * @return integer
     * @static
     */
    public static function authLevel()
    {
        if (isset($_COOKIE['leqg'])) {
            $query = Core::query('user-data-cookie', 'core');
            $query->bindValue(':cookie', $_COOKIE['leqg'], PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() == 1) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
                return $data['auth_level'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Current user ID
     *
     * @return integer
     * @static
     */
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
     * @param integer $user User ID
     *
     * @return integer
     * @static
     */
    public static function data(int $user)
    {
        $query = Core::query('user-data', 'core');
        $query->bindValue(':user', $user, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * List all user of this account
     *
     * @param integer $auth_level Auth level required for this list
     *
     * @return array
     * @static
     */
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
     * @param integer $user Asked user ID
     *
     * @return string
     * @static
     */
    public static function getLoginByID(int $user)
    {
        $data = User::data($user);
        return $data['firstname'].' '.$data['lastname'];
    }

    /**
     * Logout current user
     *
     * @return void
     * @static
     */
    public static function logout()
    {
        setcookie('leqg', 0, time());
        setcookie('time', 0, time());
        header('Location: http://auth.leqg.info/');
    }

    /**
     * List all client's user except each user asked
     *
     * @param array\string $sauf User to exclude
     *
     * @return array
     * @static
     */
    public static function sauf($sauf = '')
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
