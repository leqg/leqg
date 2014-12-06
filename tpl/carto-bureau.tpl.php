<?php
	// Protection de la page
	User::protection(5);
	
	// On récupère les données génériques sur le bureau
	$bureau = Carto::bureau_secure($_GET['code']);
	$ville = Carto::ville($bureau['commune_id']);
	$departement = Carto::departement($ville['departement_id']);
	$region = Carto::region($departement['region_id']);
	
	// On récupère les statistiques
	$electeurs = Carto::nombreElecteurs('bureau', $bureau['bureau_id']);
	$emails = Carto::nombreElecteurs('bureau', $bureau['bureau_id'], 'email');
	$mobiles = Carto::nombreElecteurs('bureau', $bureau['bureau_id'], 'mobile');
	$fixes = Carto::nombreElecteurs('bureau', $bureau['bureau_id'], 'fixe');
	
	// Chargement du template
	Core::tpl_header();
?>

<h2 data-bureau="<?php echo $bureau['bureau_id']; ?>">Bureau <?php echo $bureau['bureau_numero']; ?> <?php echo mb_convert_case(trim($bureau['bureau_nom']), MB_CASE_TITLE); ?></h2>

<div class="colonne demi gauche">
	<section class="contenu">
		<h4>Informations générales</h4>
		<ul class="informations">
			<li class="ville"><span>Ville</span><span><a href="<?php Core::tpl_go_to('carto', array('niveau' => 'communes', 'code' => hash('sha256', $ville['commune_id']))); ?>" class="nostyle"><strong><?php echo $ville['commune_nom']; ?></strong></a></span></li>
			<li class="region"><span>Département</span><span><?php echo $departement['departement_nom']; ?> (<?php echo $region['region_nom']; ?>)</span></li>
			<li class="electeur"><span>Électeurs</span><span><strong><?php echo number_format($electeurs, 0, ',', ' '); ?></strong> <em>électeur<?php if ($electeurs > 1) { ?>s<?php } ?> importé<?php if ($electeurs > 1) { ?>s<?php } ?></em></span></li>
			<li class="email"><span>Emails recueillis</span><span><strong><?php echo number_format($emails, 0, ',', ' '); ?></strong> <em>contact<?php if ($emails > 1) { ?>s<?php } ?></em></span></li>
			<li class="mobile"><span>Mobiles recueillis</span><span><strong><?php echo number_format($mobiles, 0, ',', ' '); ?></strong> <em>contact<?php if ($mobiles > 1) { ?>s<?php } ?></em></span></li>
			<li class="fixe"><span>Fixes recueillis</span><span><strong><?php echo number_format($fixes, 0, ',', ' '); ?></strong> <em>contact<?php if ($fixes > 1) { ?>s<?php } ?></em></span></li>
		</ul>
	</section>
</div>

<?php Core::tpl_footer(); ?>