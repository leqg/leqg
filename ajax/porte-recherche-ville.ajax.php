<?php
/**
 * Recherche d'une ville pour une mission de porte à porte
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */

// On récupère la ville recherchée et on la formate
$ville = $core->formatageRecherche($_POST['ville']);

// On fait la recherche
$villes = $carto->rechercheVille($ville);

// On fait la liste des résultats
foreach ($villes as $ville) :
?>
<a href="<?php $core->goPage(
    'porte',
    array('action' => 'nouveau', 'ville' => $ville['id'])
); ?>">
 <li class="ville">
  <strong><?php $carto->afficherVille($ville['id']); ?></strong>
  <p><?php $carto->afficherDepartement($ville['departement_id']); ?></p>
 </li>
</a>
<?php
endforeach;
