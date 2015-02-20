<?php
User::protection(5);
if (!isset($_GET['volet'])) $_GET['volet'] = null;
$data = new Template($_GET['template']);

// we check if client asked for a specific operation
if (isset($_GET['operation'])) {
    switch ($_GET['operation']) {
        case 'sauvTemplate':
            $data->write($_POST['templateEditor']);
            break;
    }
}
Core::tpl_header();
?>
<h2 id="titre-template" class="titre" data-template="<?php $data->get('id'); ?>"><?php if (!empty($data->get('name'))) { echo 'Template ' . $data->get('name'); } else { echo 'Template sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('template' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('template' => $data->get('id'), 'volet' => 'contenu')); ?>">Squelette</a>
</nav>

<?php switch ($_GET['volet']) : case 'contenu' : ?>
    <div class="colonne">
        <section class="contenu">
            <h4>Squelette du template</h4>
            
            <form action="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'contenu', 'operation' => 'sauvTemplate')); ?>" method="post">
                <textarea class="templateEditor" id="templateEditor" name="templateEditor"><?php echo $data->get('template'); ?></textarea>
                <script>CKEDITOR.replace('templateEditor');</script>
                
                <button class="vert clair" type="submit">Sauvegarder le template</button>
            </form>
        </section>
    </div>

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