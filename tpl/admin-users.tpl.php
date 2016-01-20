<?php
User::protection(5);
Core::loadHeader();
?>
    <a href="<?php Core::goPage('administration', array('action' => 'nouveau')); ?>" class="nostyle"><button style="float: right; margin-top: 0em;">Créer un compte</button></a>	
	<h2>Passez en revue toute votre équipe</h2>
    
    <section class="contenu">
        	<ul class="listeContacts" style="padding-top: 1em;">
            <?php $utilisateurs = User::all(0); foreach ($utilisateurs as $utilisateur) : ?><!--
        	 --><a href="<?php Core::goPage('administration', array('compte' => $utilisateur['id'])); ?>" class="nostyle"><li class="demi contact homme cursor">
        	 		<strong><?php echo $utilisateur['firstname']; ?> <?php echo $utilisateur['lastname']; ?></strong>
</li></a><?php endforeach; ?>
        	</ul>
    </section>
<?php Core::loadFooter(); ?>