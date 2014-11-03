<?php Core::tpl_header(); ?>

    <h2>Gestion des rappels militants</h2>
    
    <?php
        // On récupère la liste des missions
        $missions = Rappel::liste();
$missions = array();
        // S'il existe des missions, on en affiche la liste
        if (count($missions)) :
    ?>
    <section id="missions">
        <ul class="liste-missions">
        <?php foreach ($missions as $mission) : $m = new Rappel(md5($mission['id'])) ?>
            <li>
				<a href="<?php Core::tpl_go_to('rappels', array('mission' => $m->get('md5'))); ?>" class="nostyle"><h4><?php echo (!empty($m->get('nom'))) ? $m->get('nom') : 'Mission sans nom'; ?></h4></a>
                <p>
                    Cette mission concerne le rappel de x numéros.<br>
                    <?php if (is_null($m->get('deadline'))) { ?>
					Cette mission n'a pas de date limite connue.
					<?php } else { ?>
					Cette mission doit être terminée pour le <strong><?php echo date('d/m/Y', strtotime($m->get('deadline'))); ?></strong>.
					<?php } ?>
               </p>
            </li>
        <?php endforeach; ?>
        </ul>
    </section>
    <?php else : ?>
	<section class="icone" id="aucuneMission">
		<h3>Aucune mission de rappels militant disponible actuellement.</h3>
		
		<!--<a class="nostyle" href="<?php $core->tpl_go_to('rappels', array('action' => 'nouveau')); ?>"><button>Créer une mission</button></a>
-->	</section>
	<?php endif; ?>

<?php Core::tpl_footer(); ?>