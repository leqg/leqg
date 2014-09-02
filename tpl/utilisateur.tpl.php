<section id="fiche">

	<header>
		<h2>
			<span>Fiche utilisateur</span>
			<span><?php $user->the_nickname(); ?></span>
		</h2>
	</header>
	
	<ul class="deuxColonnes">
		<li>
			<span class="label-information">Email</span>
			<p><?php $user->the_email(); ?></p>
			<a class="nostyle icone" title="Modifier l'email de contact et de connexion" href="<?php $core->tpl_go_to('utilisateur', array('modification' => 'email')); ?>">&#xe855;</a>
		</li>
		<li>
			<span class="label-information">Mot de passe</span>
			<p><a href="<?php $core->tpl_go_to('utilisateur', array('modification' => 'pass')); ?>">Mettre à jour votre mot de passe</a></p>
		</li>
		<li>
			<span class="label-information"><label for="form-mobile">Téléphone</label></span>
			<input type="text" name="mobile" id="form-mobile" placeholder="01 02 03 04 05" value="<?php $user->the_phone(true); ?>">
		</li>
		<li>
			<span class="label-information">Dernière connexion</span>
			<p><?php echo date('d/m/Y \à H:i', strtotime($user->get_the_lasttime())); ?></p>
			<a class="nostyle icone" title="Réinitialiser toutes les connexions" href="ajax.php?script=reinitialiser-connexions">&#xe8fb;</a>
		</li>
	</ul>

</section>

<aside>
	<?php if (isset($_GET['modification'])) : ?>
		<?php if ($_GET['modification'] == 'email') : ?>
			<div>
				<h6>Modification de l'email associé au compte</h6>
				<p>
					Attention, la connexion au service LeQG utilise votre email comme identifiant et comme information principale de contact.
					<strong>Veillez à entrer un email valide afin de ne pas perdre l'accès à votre compte.</strong>
				</p>
				
				<form action="ajax.php?script=utilisateur-changement-email" method="post">
					<input type="hidden" name="user" value="<?php $user->the_id(); ?>">
					<ul class="deuxColonnes">
						<li>
							<span class="label-information">Email actuel</span>
							<p><?php $user->the_email(); ?></p>
						</li>
						<li>
							<span class="label-information"><label for="form-email">Nouvel email</label></span>
							<input type="email" id="form-email" name="email" placeholder="abc@domaine.fr">
						</li>
						<li class="submit">
							<input type="submit" value="Changer mon email">
						</li>
					</ul>
				</form>
			</div>
		<?php elseif ($_GET['modification'] == 'pass') : ?>
		
		<?php endif; ?>
	<?php else : ?>
		<div>
		<?php if (isset($_GET['message'])) : ?>
			<?php if ($_GET['message'] == 'changement-email') : ?>
				<div class="message orange">
					<strong>Changement de votre email en cours</strong>
					<p>Vous allez recevoir un email contenant un lieu sur lequel nous vous invitons à cliquer pour valider le changement de votre adresse email.</p>
				</div>
			<?php endif; ?>
		<?php endif; ?>
			<h6>Historique des dernières connexions</h6>
			
			<?php $query = 'SELECT * FROM `connexions` WHERE `user_id` = ' . $user->get_the_id() . ' ORDER BY connexion_date DESC LIMIT 0, 30'; $sql = $noyau->query($query); while($row = $sql->fetch_assoc()) $connexions[] = $core->formatage_donnees($row); ?>
		
			<table id="historique-contact">
				<thead>
					<tr>
						<th width="200px">Plateforme</th>
						<th width="300px">Date</th>
						<th>Adresse IP</th>
						<!--<th>Host</th>-->
					</tr>
				</thead>
				<tbody>
					<?php foreach ($connexions as $connexion) : ?>
					<tr>
						<td><?php echo ($connexion['plateforme']) ? 'Internet' : 'Mobile'; ?></td>
						<td><?php echo ucwords(strftime('%A %e %B %Y %X', strtotime($connexion['date']))); ?></td>
						<td><?php echo $connexion['ip']; ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</aside>