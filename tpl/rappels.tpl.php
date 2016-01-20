<?php 
    User::protection(5);
    Core::loadHeader();
?>

    <h2>Gestion des rappels militants</h2>
    
    <?php
        // On récupère la liste des missions
        $missions = Rappel::liste();

        // S'il existe des missions, on en affiche la liste
    if (count($missions)) :
    ?>
    <section id="missions">
    <ul class="liste-rappels">
    <?php foreach ($missions as $mission) : $m = new Rappel($mission['argumentaire_id']); ?>
            <li>
				<a href="<?php Core::goPage('rappels', array('mission' => $m->get('argumentaire_id'))); ?>" class="nostyle"><h4><?php echo (!empty($m->get('argumentaire_nom'))) ? $m->get('argumentaire_nom') : 'Mission sans nom'; ?></h4></a>
                <p>
                    Cette mission concerne le rappel de <strong><?php echo $m->get('nombre'); ?></strong> numéro<?php if ($m->get('nombre') > 1) { ?>s<?php 
                   } ?>.<br>
               </p>
            </li>
    <?php endforeach; ?>
    </ul>
		
		<a class="nostyle" href="<?php Core::goPage('rappels', array('action' => 'nouveau')); ?>"><button>Créer un argumentaire et sa mission</button></a>
    </section>
    <?php else : ?>
	<section class="icone" id="aucuneMission">
		<h3>Aucune mission de rappels militant disponible actuellement.</h3>
		
		<a class="nostyle" href="<?php Core::goPage('rappels', array('action' => 'nouveau')); ?>"><button>Créer un argumentaire et sa mission</button></a>
	</section>
    <?php endif; ?>

<?php Core::loadFooter(); ?>