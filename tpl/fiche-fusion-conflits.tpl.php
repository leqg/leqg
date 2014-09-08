<?php
	$query1 = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $_GET['fiche1'];
	$query2 = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $_GET['fiche2'];
	$sql1 = $db->query($query1);
	$sql2 = $db->query($query2);
	$contact1 = $sql1->fetch_assoc();
	$contact2 = $sql2->fetch_assoc();
	$contact1 = $core->formatage_donnees($contact1);
	$contact2 = $core->formatage_donnees($contact2);
?>
<section id="fiche">
	<header class="fusion">
		<h2>Fusion de fiche</h2>
	</header>
	
	<form method="post" action="ajax.php?script=fusion">
		<input type="hidden" name="fiche1" value="<?php echo $_GET['fiche1']; ?>">
		<input type="hidden" name="fiche2" value="<?php echo $_GET['fiche2']; ?>">
		<ul class="deuxColonnes">
			<li>
				<span class="label-information">Fiche 1</span>
				<p><?php echo $fiche->affichageNomByID($contact1['id']); ?></p>
			</li>
			<li>
				<span class="label-information">Fiche 2</span>
				<p><?php echo $fiche->affichageNomByID($contact2['id']); ?></p>
			</li>
			<li>
				<span class="label-information">Adresse</span>
				<span class="bordure-form">
					<label class="selectbox">
						<select name="adresse">
							<?php if ($contact1['immeuble_id'] != 0) : ?><option value="<?php echo $contact1['immeuble_id']; ?>"><?php $carto->adressePostale($contact1['immeuble_id'], ' '); ?></option><?php endif; ?>
							<?php if ($contact2['immeuble_id'] != 0) : ?><option value="<?php echo $contact2['immeuble_id']; ?>"><?php $carto->adressePostale($contact2['immeuble_id'], ' '); ?></option><?php endif; ?>
						</select>
					</label>
				</span>
			</li>
			<li>
				<span class="label-information">Email</span>
				<span class="bordure-form">
					<label class="selectbox">
						<select name="email">
							<?php if (!is_null($contact1['email'])) : ?><option value="<?php echo $contact1['email']; ?>"><?php echo $contact1['email']; ?></option><?php endif; ?>
							<?php if (!is_null($contact2['email'])) : ?><option value="<?php echo $contact2['email']; ?>"><?php echo $contact2['email']; ?></option><?php endif; ?>
						</select>
					</label>
				</span>
			</li>
			<li>
				<span class="label-information">Téléphone fixe</span>
				<span class="bordure-form">
					<label class="selectbox">
						<select name="fixe">
							<?php if (!is_null($contact1['telephone'])) : ?><option value="<?php echo $contact1['telephone']; ?>"><?php echo $core->tpl_phone($contact1['telephone']); ?></option><?php endif; ?>
							<?php if (!is_null($contact2['telephone'])) : ?><option value="<?php echo $contact2['telephone']; ?>"><?php echo $core->tpl_phone($contact2['telephone']); ?></option><?php endif; ?>
						</select>
					</label>
				</span>
			</li>
			<li>
				<span class="label-information">Téléphone mobile</span>
				<span class="bordure-form">
					<label class="selectbox">
						<select name="mobile">
							<?php if (!is_null($contact1['mobile'])) : ?><option value="<?php echo $contact1['mobile']; ?>"><?php echo $core->tpl_phone($contact1['mobile']); ?></option><?php endif; ?>
							<?php if (!is_null($contact2['mobile'])) : ?><option value="<?php echo $contact2['mobile']; ?>"><?php echo $core->tpl_phone($contact2['mobile']); ?></option><?php endif; ?>
						</select>
					</label>
				</span>
			</li>
			<li class="submit">
				<input type="submit" value="Fusionner ces deux fiches">
			</li>
		</ul>
	</form>
</section>