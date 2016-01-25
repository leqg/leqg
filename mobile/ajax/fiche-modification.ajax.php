<?php
/**
 * Update a contact
 *
 * PHP version 5
 *
 * @category Mobile
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On lance la connexion à la BDD
$link = Configuration::read('db.link');

// Je récupère les informations
$infos = $_POST;

// On formate correctement les numéros de téléphone
$infos['fixe'] = preg_replace('`[^0-9]`', '', $infos['fixe']);
$infos['mobile'] = preg_replace('`[^0-9]`', '', $infos['mobile']);

$modifs = array();

if (!empty($infos['email'])) {
    $modifs[] = '`contact_email`= "' . $infos['email'] . '"';
}
if (!empty($infos['fixe'])) {
    $modifs[] = '`contact_telephone`= "' . $infos['fixe'] . '"';
}
if (!empty($infos['mobile'])) {
    $modifs[] = '`contact_mobile`= "' . $infos['mobile'] . '"';
}

if (count($modifs) > 0) :
    $args = implode(', ', $modifs);

    $sql = 'UPDATE `contacts`
            SET ' . $args . '
            WHERE contact_id = ' . $infos['fiche'];
    $sql = $db->query($sql);
endif;

$core->goPage('contacts', array('fiche' => $infos['fiche']), true);
