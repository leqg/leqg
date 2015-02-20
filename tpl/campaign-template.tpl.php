<?php
User::protection(5);
if (!isset($_GET['volet'])) $_GET['volet'] = null;
$data = new Campaign($_GET['id']);
$data->stats();
Core::tpl_header();
?>
<h2 id="titre-template" class="titre" data-template="<?php $data->get('id'); ?>"><?php if (!empty($data->get('titre'))) { echo 'Campagne ' . $data->get('titre'); } else { echo 'Campagne sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'destinataires')); ?>">Destinataire</a>
    <?php if ($data->get('type') == 'email') : ?><a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'contenu')); ?>">Contenu</a><?php endif; ?>
</nav>

