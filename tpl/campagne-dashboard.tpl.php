<?php
User::protection(5);
Core::tpl_header();

$data = new Campaign($_GET['id']);
$data->stats();
?>
<h2 id="titre-campagne" class="titre" data-campagne="<?php $data->get('id'); ?>"><?php if (!empty($data->get('titre'))) { echo 'Campagne ' . $data->get('titre'); } else { echo 'Campagne sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'destinataires')); ?>">Destinataire</a>
    <?php if ($data->get('type') == 'email') : ?><a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'email')); ?>">Contenu</a><?php endif; ?>
</nav>

<div class="colonne demi gauche">
    <section class="contenu demi">
        <h4>Données générales</h4>
        
        <ul class="informations">
            <li class="responsable">
                <span>Créateur</span>
                <span><?php echo User::get_login_by_ID($data->get('user')); ?></span>
            </li>
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
