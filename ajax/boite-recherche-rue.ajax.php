<?php
/**
 * Recherche d'une rue pour l'ajouter à une mission de boîtage
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
$rue = $core->formatageRecherche($_POST['rue']);
$ville = $_POST['ville'];


// On fait la recherche
$rues = $carto->rechercheRue($ville, $rue);

// On fait la liste des résultats
foreach ($rues as $rue) :
?>
<a href="<?php $core->goPage(
    'boite',
    [
        'action' => 'nouveau',
        'ville' => $ville,
        'rue' => $rue['id']
    ]
); ?>">
 <li class="rue">
  <strong><?php $carto->afficherRue($rue['id']); ?></strong>
  <p><?php $carto->afficherVille($rue['commune_id']); ?></p>
 </li>
</a>
<?php
endforeach;
