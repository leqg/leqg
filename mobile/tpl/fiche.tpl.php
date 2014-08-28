<section id="fiche">
	<header>
		<h2><?php $fiche->affichage_nom('span'); ?></h2>
	</header>
	
	<ul class="infos">
		<?php if ($fiche->is_info('naissance_date')) : ?>
		<li class="age">
			<p>Né le <?php $fiche->date_naissance('/'); ?> (<strong class="gros"><?php $fiche->age(); ?></strong>)</p>
		</li>
		<?php endif; ?>
		<li class="adresse">
			<?php if ($fiche->get_immeuble()) : $carto->adressePostale($fiche->get_immeuble()); else : echo 'Aucune adresse connue'; endif; ?>
		</li>
		<?php if (!empty($fiche->get_infos('email'))) : ?>
		<li class="email">
			<?php $fiche->contact('email', true); ?>
		</li>
		<?php endif; ?>
		<?php if (!empty($fiche->get_infos('mobile'))) : ?>
		<li class="mobile">
			<a href="tel:<?php $fiche->contact('mobile'); ?>"><?php $core->tpl_phone($fiche->contact('mobile', false, true)); ?></a>
		</li>
		<?php endif; ?>
		<?php if (!empty($fiche->get_infos('telephone'))) : ?>
		<li class="fixe">
			<a href="tel:<?php $fiche->contact('telephone'); ?>"><?php $core->tpl_phone($fiche->contact('telephone', false, true)); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</section>

<section id="historique">
	<h2>Historique des interactions</h2>
	
	<ul class="infos">
		<?php $lienVersFiche = array('contact', 'telephone', 'email', 'courrier', 'autre'); ?>
		<?php $interactions = $historique->rechercheParFiche($_GET['fiche']); foreach ($interactions as $interaction) : ?>
		<li>
			<p>
				<?php if (in_array($interaction['type'], $lienVersFiche)) : ?><a href="<?php $core->tpl_go_to('contacts', array('fiche' => $fiche->get_the_ID(), 'interaction' => $interaction['id'])); ?>" class="nostyle"><?php endif; ?>
					<em><?php echo date('d/m/Y', strtotime($interaction['date'])); ?></em><br>
					<?php if (in_array($interaction['type'], $lienVersFiche)) : ?><em><?php $historique->returnType($interaction['type']); ?></em><br><?php endif; ?>
					<?php if ($interaction['type'] == 'sms') : ?><strong>Envoi d'un SMS</strong><br><em>&laquo;&nbsp;<?php echo $interaction['notes']; ?>&nbsp;&raquo;</em><?php else : ?><strong><?php echo $interaction['objet']; ?></strong><?php endif; ?>
				<?php if (in_array($interaction['type'], $lienVersFiche)) : ?></a><?php endif; ?>
			</p>
		</li>
		<?php endforeach; ?>
	</ul>
	
	<nav id="actions-fiche">
		<a href="#" id="retourDepuisHistorique" class="retour">&#xe813;</a>
	</nav>
</section>

<nav id="actions-fiche">
	<a href="#" id="goToHistorique" class="historique">&#xe8dd;</a>
	<a href="#" id="goToModif" class="modifier">&#xe855;</a>
</nav>