<section id="fusion">
	<table class="fusion">
		<thead>
			<tr>
				<th>Fiche 1</th>
				<th>&#xe911;</th>
				<th>Fiche 2</th>
			</tr>
		</thead>
		<?php if (empty($_GET['fiche1']) && empty($_GET['fiche2'])) : ?>
		<tbody>
			<tr>
				<td class="recherche">
					<input class="bordure" type="text" name="fiche1" id="form-fiche1" placeholder="Noms et prénoms">
				</td>
				<td>&nbsp;</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="resultats iconeCentre">
					<ul class="listeEncadree" id="resultats-fiche1">
						<li class="vide">
							<strong>Aucun résultat</strong>
						</li>
					</ul>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
		<?php elseif (!empty($_GET['fiche1']) && empty($_GET['fiche2'])) : ?>
		<tbody>
			<tr>
				<td>
					<ul class="listeEncadree" id="retourEnArriere">
						<a href="<?php $core->tpl_go_to('fiche', array('operation' => 'fusion')); ?>">
							<li class="retour">
								<strong>Choisir une autre fiche</strong>
							</li>
						</a>
					</ul>
				</td>
				<td>&nbsp;</td>
				<td class="recherche">
					<input class="bordure" type="text" name="fiche2" id="form-fiche2" placeholder="Noms et prénoms" data-fiche1="<?php echo $_GET['fiche1']; ?>">
				</td>
			</tr>
			<tr>
				<td class="resultats iconeCentre">
					<ul class="listeEncadree" id="resultats-fiche1">
						<a href="<?php $core->tpl_go_to('fiche', array('id' => $_GET['fiche1'])); ?>">
							<li class="electeur">
								<?php $fiche->acces($_GET['fiche1'], true); ?>
								<strong><?php $fiche->affichage_nom() ?></strong>
							</li>
						</a>
					</ul>
				</td>
				<td>&nbsp;</td>
				<td class="resultats iconeCentre">
					<ul class="listeEncadree" id="resultats-fiche2">
						<li class="vide">
							<strong>Aucun résultat</strong>
						</li>
					</ul>
				</td>
			</tr>
		</tbody>
		<?php else :
		
			// On recherche les informations sur les fiches
			$query1 = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $_GET['fiche1'];
			$query2 = 'SELECT * FROM `contacts` WHERE `contact_id` = ' . $_GET['fiche2'];
			$sql1 = $db->query($query1);
			$sql2 = $db->query($query2);
			$contact1 = $sql1->fetch_assoc();
			$contact2 = $sql2->fetch_assoc();
			$contact1 = $core->formatage_donnees($contact1);
			$contact2 = $core->formatage_donnees($contact2);
		?>
		<tbody>
			<tr>
				<td class="nom"><?php $fiche->affichageNomByID($_GET['fiche1']); ?></td>
				<td>&nbsp;</td>
				<td class="nom"><?php $fiche->affichageNomByID($_GET['fiche2']); ?></td>
			</tr>
			
			<tr>
				<td class="adresse">
					<?php if ($contact1['immeuble_id']) : ?>
						<?php echo $carto->adressePostale($contact1['immeuble_id']); ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
				<td class="merging">
					<?php if ($contact1['immeuble_id'] == $contact2['immeuble_id']) : ?>
					<span class="egal">&#xe896;</span>
					<?php else : ?>
					<span class="inegal">&#xe898;</span>
					<?php endif; ?>
				</td>
				<td class="adresse">
					<?php if ($contact2['immeuble_id']) : ?>
						<?php $carto->adressePostale($contact2['immeuble_id']); ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
			</tr>
			
			<tr>
				<td class="email">
					<?php if ($contact1['email']) : ?>
						<?php echo $contact1['email']; ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
				<td class="merging">
					<?php if ($contact1['email'] == $contact2['email']) : ?>
					<span class="egal">&#xe896;</span>
					<?php else : ?>
					<span class="inegal">&#xe898;</span>
					<?php endif; ?>
				</td>
				<td class="email">
					<?php if ($contact2['email']) : ?>
						<?php echo $contact2['email']; ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
			</tr>
			
			<tr>
				<td class="fixe">
					<?php if ($contact1['telephone']) : ?>
						<?php echo $contact1['telephone']; ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
				<td class="merging">
					<?php if ($contact1['telephone'] == $contact2['telephone']) : ?>
					<span class="egal">&#xe896;</span>
					<?php else : ?>
					<span class="inegal">&#xe898;</span>
					<?php endif; ?>
				</td>
				<td class="fixe">
					<?php if ($contact2['telephone']) : ?>
						<?php echo $contact2['telephone']; ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
			</tr>
			
			<tr>
				<td class="mobile">
					<?php if ($contact1['mobile']) : ?>
						<?php echo $contact1['mobile']; ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
				<td class="merging">
					<?php if ($contact1['mobile'] == $contact2['mobile']) : ?>
					<span class="egal">&#xe896;</span>
					<?php else : ?>
					<span class="inegal">&#xe898;</span>
					<?php endif; ?>
				</td>
				<td class="fixe">
					<?php if ($contact2['mobile']) : ?>
						<?php echo $contact2['mobile']; ?>
					<?php else : ?>
						<em>Aucune adresse</em>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
		<?php endif; ?>
	</table>
	
	<div id="goFusion"><a href="<?php $core->tpl_go_to('fiche', array('operation' => 'choix', 'fiche1' => $_GET['fiche1'], 'fiche2' => $_GET['fiche2'])); ?>" class="bouton">Fusionner ces fiches</a></div>
</section>