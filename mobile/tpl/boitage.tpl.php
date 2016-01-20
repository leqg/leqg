<?php
    // On charge la liste des missions ouvertes où la personne est inscrite
    $missions_ouvertes = Mission::missions_ouvertes('boitage', User::ID());
    
    // On charge le header
    Core::loadHeader();
?>
	<h2>Missions de boîtage</h2>

	<ul class="listeMissions">
    <?php 
    if ($missions_ouvertes) :
        foreach ($missions_ouvertes as $mission_ouverte) : $mission = new Mission(md5($mission_ouverte)); $deadline = DateTime::createFromFormat('Y-m-d', $mission->get('mission_deadline'));
        ?>
		<li>
		    <a href="<?php Core::goTo('mission', array('code' => $mission->get('mission_hash'))); ?>" class="nostyle">
        <h4><?php echo $mission->get('mission_nom'); ?></h4>
				<?php if ($mission->get('mission_deadline')) : ?><p><span>Deadline :</span> <strong><?php echo $deadline->format('d/m/Y'); ?></strong></p><?php 
    endif; ?>
		    </a>
		</li>
    <?php 
        endforeach;
            else :
        ?>
		<li class="vide">
			<p>Aucune mission actuellement</p>
		</li>
            <?php endif; ?>
	</ul>
<?php Core::loadFooter(); ?>