<?php
	// On protège la page
	User::protection(5);
	
	// On cherche des infos sur l'utilisateur
	$user = User::infos($_GET['compte']);
	
	// On défini les niveaux d'autorisation
	$auth_lvl = array(
    	9 => 'Maintenance technique',
    	8 => 'Administrateur',
    	7 => 'Administrateur',
    	6 => 'Administrateur',
    	5 => 'Administrateur',
    	4 => 'Militant organisateur',
    	3 => 'Militant',
    	2 => 'Militant',
    	1 => 'Militant'
	);

    Core::tpl_header();
?>
    <a href="<?php Core::tpl_go_to('administration'); ?>" class="nostyle"><button style="float: right; margin-top: 0em;" class="gris">Retour à la liste</button></a>	
	
	<h2><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h2>
	
	<div class="colonne demi gauche">
    	<section class="contenu demi">
        	<h4>Informations générales</h4>
        	
            <ul class="informations">
                <li class="responsable">
                    <span>Niveau d'autorisation</span>
                    <span><?php echo $auth_lvl[$user['auth_level']]; ?></span>
                </li>
                <li class="email">
                    <span>Email</span>
                    <span><?php echo $user['email']; ?></span>
                </li>
                <li class="mobile">
                    <span>Téléphone</span>
                    <span><?php echo Core::get_tpl_phone($user['telephone']); ?></span>
                </li>
                <li class="date">
                    <span>Dernière connexion</span>
                    <span><?php $connexion = DateTime::createFromFormat('Y-m-d H:i:s', $user['last_login']); echo $connexion->format('d/m/Y H:i'); ?></span>
                </li>
            </ul>
    	</section>
    	
    	<?php if(User::ID() != $_GET['compte']) : ?>
    	<section class="contenu demi">
        	<a href="ajax.php?script=admin-suppression&compte=<?php echo $_GET['compte']; ?>" class="nostyle"><button class="deleting long" style="margin: 0 auto">Supprimer ce compte</button></a>
    	</section>
    	<?php endif; ?>
	</div>
    
    <div class="colonne demi droite">
    	<?php if(User::ID() != $_GET['compte']) : ?>
        <section class="contenu demi">
            <h4>Attribuer de nouveaux droits</h4>
            
            <a href="ajax.php?script=admin-auth&compte=<?php echo $user['id']; ?>&lvl=8" class="nostyle"><button class="jaune long">Administrateur</button></a>
            <a href="ajax.php?script=admin-auth&compte=<?php echo $user['id']; ?>&lvl=3" class="nostyle"><button class="vert long">Militant</button></a>
        </section>
    	<?php endif; ?>
    </div>