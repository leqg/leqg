<?php
	// On ouvre la mission
	$data = new Mission($_GET['code']);
	
	// On vérifie que la mission a bien été ouverte
	if ($data->err) Core::tpl_go_to('porte', true);
	
	// On récupère tous les items de l'immeuble et la rue en question et la ville concernée
	$rue = Carto::rue($_GET['rue']);
	$contact = new Contact(md5($_GET['electeur']));
	
	if (!isset($_GET['electeur'])) Core::tpl_go_to('reporting', array('mission' => $_GET['mission']), true);
	
	// typologie
	$typologie = ($data->get('mission_type') == 'porte') ? 'porte' : 'boite';

    // On charge le header
	Core::tpl_header();
?>

	<h2>Mission &laquo;&nbsp;<?php echo $data->get('mission_nom'); ?>&nbsp;&raquo;</h2>

    <h3 style="margin: 20px;"><?php echo mb_convert_case($contact->get('contact_nom'), MB_CASE_UPPER) . ' ' . mb_convert_case($contact->get('contact_nom_usage'), MB_CASE_UPPER) . ' ' . mb_convert_case($contact->get('contact_prenoms'), MB_CASE_TITLE); ?></h3>

    <a href="<?php Core::tpl_go_to('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['electeur'], 'statut' => 1)); ?>" class="nostyle bouton">Absent</a>
    <a href="<?php Core::tpl_go_to('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['electeur'], 'statut' => 2)); ?>" class="nostyle bouton">Ouvert</a>
    <a href="<?php Core::tpl_go_to('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['electeur'], 'statut' => 3)); ?>" class="nostyle bouton">Demande procuration</a>
    <a href="<?php Core::tpl_go_to('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['electeur'], 'statut' => 4)); ?>" class="nostyle bouton">Demande contact</a>
    <a href="<?php Core::tpl_go_to('report', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'], 'electeur' => $_GET['electeur'], 'statut' => -1)); ?>" class="nostyle bouton">Inconnu à l'adresse</a>
	
	<a href="<?php Core::tpl_go_to('mission', array('code' => $_GET['code'], 'rue' => $_GET['rue'], 'immeuble' => $_GET['immeuble'])); ?>" class="bouton nostyle">Retour à l'immeuble</a>
<?php Core::tpl_footer(); ?>