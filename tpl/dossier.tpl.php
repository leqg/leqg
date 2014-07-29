<?php $dossier = $fiche->dossier($_GET['id']); ?>
<section id="fiche-dossier">
	<header>
		<h2><span>Dossier</span><span id="titre-dossier"><?php echo $dossier['nom']; ?></span></h2>
	</header>
	
	<form action="<?php $core->tpl_get_url('modification', 'dossier', 'type', $_GET['id'], 'id'); ?>" method="post">
		<ul class="infos">
			<li>
				<label class="textarea" for="description">Description</label>
				<textarea id="description" name="description"><?php echo $dossier['description']; ?></textarea>
			</li>
		</ul>
	</form>
	
	<h3>Contacts associés au dossier</h3>
	<section class="liste"><?php $fiches = explode(',', $dossier['contacts']); foreach ($fiches as $contact) : ?>
		<a href="<?php $core->tpl_get_url('fiche', $contact); ?>">
			<article class="fiche" data-fiche="<?php echo $contact; ?>">
				<header>
					<h3><?php $fiche->nomByID($contact, 'span', false); ?></h3>
				</header>
			</article>
		</a>
		<?php endforeach; ?>
		<article id="ajout-contact" class="fiche ajout"><header><h3><span>Ajouter</span> <span>un contact</span></h3></header></article>
	</section>
</section>