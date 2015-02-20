<?php
// On protège la page
User::protection(5);

// On ouvre la campagne
$campagne = new Campagne($_GET['id']);

// On charge le header
Core::tpl_header();
?>
<h2 id="titre-campagne" class="titre" data-campagne="<?php $campagne->get('id'); ?>"><?php if (isset($titre)) { echo 'Campagne ' . $campagne->get('titre'); } else { echo 'Campagne sans titre'; } ?></h2>
