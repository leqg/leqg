<?php
User::protection(5);
if (!isset($_GET['volet'])) $_GET['volet'] = null;
$data = new Campaign($_GET['id']);
$data->stats();
Core::tpl_header();
?>
<h2 id="titre-campagne" class="titre" data-campagne="<?php $data->get('id'); ?>"><?php if (!empty($data->get('titre'))) { echo 'Campagne ' . $data->get('titre'); } else { echo 'Campagne sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'destinataires')); ?>">Destinataire</a>
    <?php if ($data->get('type') == 'email') : ?><a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'contenu')); ?>">Contenu</a><?php endif; ?>
</nav>

<?php switch ($_GET['volet']) : case 'contenu': ?>
    <?php if ($data->get('template')) : ?>
    
    <?php else : $templates = Template::all(); ?>
    <div class="colonne">
        <section class="contenu">
            <h4>Pour commencer, veuillez choisir un template de courrier électronique</h4>
            
			<ul class="liste-campagnes">
				<?php foreach ($templates as $element) : ?>
				<li class="template">
					<a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'choixTemplate', 'template' => $element['id'])); ?>" class="nostyle"><h4><?php if (!empty($element['name'])) { echo $element['name']; } else { echo 'Template sans titre'; } ?></h4></a>
                    <?php if (!empty($element['desc'])) : ?><p><?php echo $element['desc']; ?></p><?php endif; ?>
				</li>
				<?php endforeach; ?>
				<li class="template">
				    <a href="<?php Core::tpl_go_to('campagne', array('template' => 'new')); ?>" class="nostyle"><h4>Créer un nouveau template de courrier électronique</h4></a>
				    <p>Un template est obligatoire pour pouvoir lancer une campagne d'emailing.</p>
				</li>
			</ul>
        </section>
    </div>
    <?php endif; ?>
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