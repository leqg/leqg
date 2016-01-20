<?php
/**
 * Fusion de fiches à la suite d'une recherche
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


// Script de recherche
$recherche = $core->formatageRecherche($_POST['recherche']);

// On effectue la recherche des fiches dont les tags correspondent
$query = 'SELECT *
          FROM `contacts`
          WHERE CONCAT_WS(" ",
                              `contact_prenoms`,
                              `contact_nom`,
                              `contact_nom_usage`,
                              `contact_nom`,
                              `contact_prenoms`)
          LIKE "%' . $recherche . '%"
          AND `contact_id` != "' . $_POST['fiche1'] . '"
          ORDER BY `contact_nom`, `contact_nom_usage`, `contact_prenoms` ASC
          LIMIT 0, 30';
$sql = $db->query($query);

if ($sql->num_rows > 0) :
    while ($contacts = $sql->fetch_assoc()) :
        $contact = $core->formatageDonnees($contacts);
?>
<?php if ($_GET['fiche'] == 1) { ?>
    <a href="<?php $core->goTo(
        'fiche',
        array('operation' => 'fusion', 'fiche1' => $contact['id'])
    ); ?>">
<?php } else { ?>
    <a href="<?php $core->goTo(
        'fiche',
        array(
            'operation' => 'fusion',
            'fiche1' => $_POST['fiche1'],
            'fiche2' => $contact['id']
        )
    ); ?>">
<?php } ?>
    <li class="electeur">
        <strong>
            <?php echo strtoupper($contact['nom']); ?>
            <?php echo strtoupper($contact['nom_usage']); ?>
            <?php echo ucwords(strtolower($contact['prenoms'])); ?>
        </strong>
    </li>
</a>
<?php
    endwhile;
else :
?>
    <li class="vide"><strong>Aucun résultat</strong></li>
<?php
endif;
