<?php
User::protection(5);
if (!isset($_GET['volet'])) $_GET['volet'] = null;
$data = new Template($_GET['template']);
Core::tpl_header();
?>
<h2 id="titre-template" class="titre" data-template="<?php $data->get('id'); ?>"><?php if (!empty($data->get('name'))) { echo 'Template ' . $data->get('name'); } else { echo 'Template sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('template' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('template' => $data->get('id'), 'volet' => 'contenu')); ?>">Squelette</a>
</nav>

<?php switch ($_GET['volet']) : case 'contenu' : ?>


<?php break; default: ?>
    <div class="colonne demi gauche">
        <section class="contenu demi">
            <h4>Données générales</h4>
            
        </section>
    </div>
    
    <div class="colonne demi droite">
        <section class="contenu demi">
            <h4>Utilisations</h4>
        </section>
    </div>
<?php endswitch; ?>