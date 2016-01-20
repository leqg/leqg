<?php 
    // On met en place la protection
    User::protection(1);
    
    // On charge le header
    Core::loadHeader();
?>
<table id="services">
    <?php if (User::authLevel() >= 5) : ?>
	<tr>
		<td><a href="<?php Core::goPage('contacts'); ?>"><span>&#xe840;</span><p>Contacts</p></a></td>
		<td><a href="<?php Core::goPage('dossier'); ?>"><span>&#xe851;</span><p>Dossiers</p></a></td>
		<td><a href="<?php Core::goPage('carto'); ?>"><span>&#xe845;</span><p>Cartographie</p></a></td>
	</tr>
	<tr>
		<td><a href="<?php Core::goPage('poste'); ?>"><span>&#xe8ef;</span><p>Publipostage</p></a></td>
		<td><a href="<?php Core::goPage('email'); ?>"><span>&#xe805;</span><p>Emailing</p></a></td>
		<td><a href="<?php Core::goPage('sms'); ?>"><span>&#xe8e4;</span><p>SMS</p></a></td>
	</tr>
	<tr>
		<td><a href="<?php Core::goPage('porte'); ?>"><span>&#xe841;</span><p>Porte à porte</p></a></td>
		<td><a href="<?php Core::goPage('boite'); ?>"><span>&#xe84d;</span><p>Boîtage</p></a></td>
		<td><a href="<?php Core::goPage('rappels'); ?>" class="inactif"><span class="inactif">&#xe854;</span><p class="inactif">Rappels</p></a></td>
	</tr>
    <?php else : ?>
	<tr>
		<td><a href="<?php Core::goPage('porte', array('action' => 'missions')); ?>"><span>&#xe841;</span><p>Porte à porte</p></a></td>
		<td><a href="<?php Core::goPage('boite', array('action' => 'missions')); ?>"><span>&#xe84d;</span><p>Boîtage</p></a></td>
		<td><a href="<?php Core::goPage('rappels', array('action' => 'appel')); ?>"><span>&#xe854;</span><p>Rappels</p></a></td>
	</tr>
    <?php endif; ?>
</table>
<?php Core::loadFooter(); ?>