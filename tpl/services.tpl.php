<?php 
    // On met en place la protection
    User::protection(1);
    
    // On charge le header
    Core::tpl_header();
?>
<table id="services">
    <?php if (User::auth_level() >= 5) : ?>
	<tr>
		<td><a href="<?php Core::tpl_go_to('contacts'); ?>"><span>&#xe840;</span><p>Contacts</p></a></td>
		<td><a href="<?php Core::tpl_go_to('dossier'); ?>"><span>&#xe851;</span><p>Dossiers</p></a></td>
		<td><a href="<?php Core::tpl_go_to('carto'); ?>"><span>&#xe845;</span><p>Cartographie</p></a></td>
	</tr>
	<tr>
		<td><a href="<?php Core::tpl_go_to('poste'); ?>"><span>&#xe8ef;</span><p>Publipostage</p></a></td>
		<td><a href="<?php Core::tpl_go_to('email'); ?>"><span>&#xe805;</span><p>Emailing</p></a></td>
		<td><a href="<?php Core::tpl_go_to('sms'); ?>"><span>&#xe8e4;</span><p>SMS</p></a></td>
	</tr>
	<tr>
		<td><a href="<?php Core::tpl_go_to('porte'); ?>"><span>&#xe841;</span><p>Porte à porte</p></a></td>
		<td><a href="<?php Core::tpl_go_to('boite'); ?>"><span>&#xe84d;</span><p>Boîtage</p></a></td>
		<td><a href="<?php Core::tpl_go_to('rappels'); ?>" class="inactif"><span class="inactif">&#xe854;</span><p class="inactif">Rappels</p></a></td>
	</tr>
    <?php else : ?>
	<tr>
		<td><a href="<?php Core::tpl_go_to('porte', array('action' => 'missions')); ?>"><span>&#xe841;</span><p>Porte à porte</p></a></td>
		<td><a href="<?php Core::tpl_go_to('boite', array('action' => 'missions')); ?>"><span>&#xe84d;</span><p>Boîtage</p></a></td>
		<td><a href="<?php Core::tpl_go_to('rappels', array('action' => 'appel')); ?>"><span>&#xe854;</span><p>Rappels</p></a></td>
	</tr>
    <?php endif; ?>
</table>
<?php Core::tpl_footer(); ?>