<?php
User::protection(5);
if (!isset($_GET['volet'])) $_GET['volet'] = null;
$data = new Campaign($_GET['id']);
$data->stats();

// we check if client asked for a specific operation
if (isset($_GET['operation'])) {
    switch ($_GET['operation']) {
        case 'copieTemplate':
            $data->template_copy($_GET['template']);
            break;
        
        case 'saveTemplate':
            $data->template_write($_POST['templateEditor']);
            break;
    }
}

Core::tpl_header();
?>
<h2 id="titre-campagne" class="titre" data-campagne="<?php $data->get('id'); ?>"><?php if (!empty($data->get('titre'))) { echo 'Campagne ' . $data->get('titre'); } else { echo 'Campagne sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'destinataires')); ?>">Destinataire</a>
    <?php if ($data->get('type') == 'email' && $data->get('status') == 'open') : ?>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'template')); ?>">Template</a>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'contenus')); ?>">Contenus</a>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'visu')); ?>">Email final</a>
    <?php endif; ?>
</nav>

<?php switch ($_GET['volet']) : case 'template': ?>

    <div class="colonne">
        <?php if (is_null($data->get('template'))) : $templates = Campaign::templates(); ?>
        
        <section class="contenu">
            <h4>Création du template</h4>
            
			<ul class="liste-campagnes">
				<li class="template">
				    <a href="<?php Core::tpl_go_to('campagne', array('template' => 'new')); ?>" class="nostyle"><h4>Template vierge</h4></a>
				    <p>Vous pouvez commencer à partir d'un template vierge ou bien récupérer un précédent template pour l'adapter.</p>
				</li>
				<?php foreach ($templates as $element) : ?>
				<li class="template">
					<a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'template', 'operation' => 'copieTemplate', 'template' => $element['id'])); ?>" class="nostyle"><h4><?php if (!empty($element['name'])) { echo $element['name']; } else { echo 'Template sans titre'; } ?></h4></a>
                    <?php if (!empty($element['desc'])) : ?><p><?php echo $element['desc']; ?></p><?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
        </section>
        
        <?php else : ?>
            
        <section class="contenu">
            <h4>Personnalisation du template</h4>
            
            <form action="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'template', 'operation' => 'saveTemplate')); ?>" method="post">
                <textarea class="templateEditor" id="templateEditor" name="templateEditor"><?php echo $data->get('template'); ?></textarea>
                <script>CKEDITOR.replace( 'templateEditor' );</script>
                <button class="vert clair" type="submit">Sauvegarder le template</button>
            </form>
        </section>
            
        <?php endif; ?>
    </div>

<?php break; case 'visu': $email = $data->template_parsing(); ?>

    <div class="colonne">
        <section class="contenu">
            <?php echo $email; ?>
        </section>
    </div>
    
<?php break; default: ?>

    <div class="colonne demi gauche">
        <section class="contenu demi">
            <h4>Données générales</h4>
            
            <ul class="informations">
                <li class="responsable">
                    <span>Créateur</span>
                    <span><?php echo User::get_login_by_ID($data->get('user')); ?></span>
                </li>
                <?php if ($data->get('type') == 'email') : ?>
                <li class="dossier">
                    <span>Template utilisé</span>
                    <span><?php if ($data->used_template()) { echo $data->used_template(); } else { echo 'Aucun template défini'; } ?></span>
                </li>
                <?php endif; ?>
                <?php if ($data->get('status') == 'open') : ?>
                <li class="utilisateurs inscrit">
                    <span>Ciblage de la campagne</span>
                    <span><strong><?php echo number_format($data->get('count')['target'], 0, ',', ' '); ?></strong> destinataire<?php echo ($data->get('count')['target'] > 1) ? 's' : ''; ?></span>
                </li>
                <li class="date">
                    <span>Temps estimé pour l'envoi</span>
                    <span><?php echo $data->display_estimated_time(); ?></span>
                </li>
                <?php endif; ?>
            </ul>
        </section>
    </div>
    
<?php endswitch; ?>