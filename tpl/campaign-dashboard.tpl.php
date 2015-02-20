<?php
User::protection(5);
if (!isset($_GET['volet'])) $_GET['volet'] = null;
$data = new Campaign($_GET['id']);
$data->stats();

// we check if client asked for a specific operation
if (isset($_GET['operation'])) {
    switch ($_GET['operation']) {
        case 'copieTemplate':
            $data->template_copy($_GET['template']);
            break;
        
        case 'saveTemplate':
            $data->template_write($_POST['templateEditor']);
            break;
        
        case 'launch':
            $data->launch();
            break;
    }
}

Core::tpl_header();
?>
<h2 id="titre-campagne" class="titre" data-campagne="<?php $data->get('id'); ?>"><?php if (!empty($data->get('titre'))) { echo 'Campagne ' . $data->get('titre'); } else { echo 'Campagne sans titre'; } ?></h2>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'destinataires')); ?>">Destinataire</a>
    <?php if ($data->get('type') == 'email' && $data->get('status') == 'open') : ?>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'template')); ?>">Template</a>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'visu')); ?>">Email final</a>
    <?php elseif ($data->get('type') == 'email' && ($data->get('status') == 'send' || $data->get('status') == 'close')) : ?>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'visu')); ?>">Email final</a>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'statistiques')); ?>">Statistiques</a>
    <?php endif; ?>
</nav>

<?php switch ($_GET['volet']) : case 'template': ?>

    <div class="colonne">
        <?php if (is_null($data->get('template'))) : $templates = Campaign::templates(); ?>
        
        <section class="contenu">
            <h4>Création du template</h4>
            
			<ul class="liste-campagnes">
				<li class="template">
				    <a href="<?php Core::tpl_go_to('campagne', array('template' => 'new')); ?>" class="nostyle"><h4>Template vierge</h4></a>
				    <p>Vous pouvez commencer à partir d'un template vierge ou bien récupérer un précédent template pour l'adapter.</p>
				</li>
				<?php foreach ($templates as $element) : ?>
				<li class="template">
					<a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'template', 'operation' => 'copieTemplate', 'template' => $element['id'])); ?>" class="nostyle"><h4><?php if (!empty($element['name'])) { echo $element['name']; } else { echo 'Template sans titre'; } ?></h4></a>
                    <?php if (!empty($element['desc'])) : ?><p><?php echo $element['desc']; ?></p><?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
        </section>
        
        <?php else : ?>
            
        <section class="contenu">
            <h4>Personnalisation du template</h4>
            
            <form action="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'template', 'operation' => 'saveTemplate')); ?>" method="post">
                <textarea class="templateEditor" id="templateEditor" name="templateEditor"><?php echo $data->get('template'); ?></textarea>
                <script>CKEDITOR.replace( 'templateEditor' );</script>
                <button class="vert clair" type="submit">Sauvegarder le template</button>
            </form>
        </section>
            
        <?php endif; ?>
    </div>

<?php break; case 'visu': $email = $data->template_parsing(); ?>

    <div class="colonne">
        <section class="contenu">
            <?php echo $data->get('mail'); ?>
        </section>
    </div>
    
<?php break; case 'launch': ?>

    <div class="colonne">
        <section class="contenu">
            <h3>Campagne lancée !</h3>
        </section>
    </div>
    
<?php break; case 'destinataires' : ?>

    <div class="colonne">
        <section class="contenu">
            <h4>Liste des destinataires</h4>
            
            <?php $destinataires = $data->recipients(); ?>
            <table class="listeDestinataires">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th class="align-center">Contact</th>
                        <th class="align-center petit">Statut</th>
                    </tr>
                </thead>
                <?php foreach ($destinataires as $destinataire) : $contact = new Contact(md5($destinataire['contact'])); ?>
                <tbody>
                    <tr>
                        <td><?php echo $destinataire['email']; ?></td>
                        <td><a href="<?php echo Core::tpl_go_to('contact', array('contact' => hash('md5', $destinataire['contact']))); ?>"><?php echo $contact->get('nom_affichage'); ?></a></td>
                        <td><?php echo Campaign::display_status($destinataire['status']); ?></td>
                    </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
        </section>
    </div>

<?php break; case 'statistiques' : ?>

    <div class="colonne demi gauche">
        <section class="contenu demi">
            <h4>Statistiques d'envoi</h4>
            <?php
            $nombre['sent']        = (isset($data->get('count')['items']['sent'])) ? $data->get('count')['items']['sent'] : 0;
            $nombre['queued']      = (isset($data->get('count')['items']['queued'])) ? $data->get('count')['items']['queued'] : 0;
            $nombre['scheduled']   = (isset($data->get('count')['items']['scheduled'])) ? $data->get('count')['items']['scheduled'] : 0;
            $nombre['rejected']    = (isset($data->get('count')['items']['rejected'])) ? $data->get('count')['items']['rejected'] : 0;
            $nombre['invalid']     = (isset($data->get('count')['items']['invalid'])) ? $data->get('count')['items']['invalid'] : 0;
            $nombre['total']       = (isset($data->get('count')['items']['all'])) ? $data->get('count')['items']['all'] : 0;
            
            function pourcentage( $actu , $total ) {
                $pourcentage = $actu * 100 / $total;
                $pourcentage = str_replace(',', '.', $pourcentage);
                return $pourcentage;
            }
            ?>
            <div id="avancementMission"><!--
             --><div class="npai" style="width: <?php echo pourcentage($nombre['scheduled'], $nombre['total']); ?>%;"><span>Emails&nbsp;prévus&nbsp;:&nbsp;<?php echo number_format($nombre['scheduled'], 0, ',', ' '); ?></span></div><!--
             --><div class="absent" style="width: <?php echo pourcentage($nombre['queued'], $nombre['total']); ?>%;"><span>Envois&nbsp;en&nbsp;cours&nbsp;:&nbsp;<?php echo number_format($nombre['queued'], 0, ',', ' '); ?></span></div><!--
             --><div class="ouvert" style="width: <?php echo pourcentage($nombre['sent'], $nombre['total']); ?>%;"><span>Emails&nbsp;délivrés&nbsp;:&nbsp;<?php echo number_format($nombre['sent'], 0, ',', ' '); ?></span></div><!--
             --><div class="procuration" style="width: <?php echo pourcentage($nombre['rejected'], $nombre['total']); ?>%;"><span>Emails&nbsp;en&nbsp;erreur&nbsp;:&nbsp;<?php echo number_format($nombre['rejected'], 0, ',', ' '); ?></span></div><!--
             --><div class="contact" style="width: <?php echo pourcentage($nombre['invalid'], $nombre['total']); ?>%;"><span>Emails&nbsp;invalides&nbsp;:&nbsp;<?php echo number_format($nombre['invalid'], 0, ',', ' '); ?></span></div><!--
         --></div>
         
            <ul class="statistiquesMission">
                <?php if ($nombre['scheduled']) : ?><li><strong><?php echo number_format($nombre['scheduled'], 0, ',', ' '); ?></strong> emails planifiés</li><?php endif; ?>
                <?php if ($nombre['queued']) : ?><li><strong><?php echo number_format($nombre['queued'], 0, ',', ' '); ?></strong> emails en cours d'envoi</li><?php endif; ?>
                <?php if ($nombre['sent']) : ?><li><strong><?php echo number_format($nombre['sent'], 0, ',', ' '); ?></strong> emails envoyés et reçus</li><?php endif; ?>
                <?php if ($nombre['rejected']) : ?><li><strong><?php echo number_format($nombre['rejected'], 0, ',', ' '); ?></strong> emails en erreur</li><?php endif; ?>
                <?php if ($nombre['invalid']) : ?><li><strong><?php echo number_format($nombre['invalid'], 0, ',', ' '); ?></strong> emails invalides</li><?php endif; ?>
            </ul>
        </section>
    </div>
    
    <div class="colonne demi droite">
        <?php if ($nombre['rejected'] || $nombre['invalid']) : ?>
        <section class="contenu demi">
            <h4>Envois en erreur</h4>
            <?php $erreurs = $data->errors(); ?>
            
            <ul class="statistiquesMission">
                <?php foreach($erreurs as $erreur) : ?>
                <li>
                    <strong><?php echo $erreur['email']; ?></strong><br>
                    <a href="<?php Core::tpl_go_to('contact', array('contact' => md5($erreur['contact']))); ?>" class="nostyle"><?php echo $erreur['name']; ?></a><br>
                    <em>Erreur : <?php echo $erreur['error']; ?></em><br>
                    <em>Date : <?php echo date('d/m/Y H\hi', strtotime($erreur['time'])); ?></em>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>
    </div>
    
<?php break; default: ?>

    <div class="colonne demi gauche">
        <section class="contenu demi">
            <h4>Données générales</h4>
            
            <ul class="informations">
                <li class="responsable">
                    <span>Créateur</span>
                    <span><?php echo User::get_login_by_ID($data->get('user')); ?></span>
                </li>
                <?php if ($data->get('type') == 'email') : ?>
                <li class="utilisateurs inscrit">
                    <span>Nombre d'envois</span>
                    <span><strong><?php echo number_format($data->get('count')['items']['all'], 0, ',', ' '); ?></strong> destinataire<?php echo ($data->get('count')['items']['all'] > 1) ? 's' : ''; ?></span>
                </li>
                <?php endif; ?>
                <?php if ($data->get('status') == 'open') : ?>
                <li class="utilisateurs inscrit">
                    <span>Ciblage de la campagne</span>
                    <span><strong><?php echo number_format($data->get('count')['target'], 0, ',', ' '); ?></strong> destinataire<?php echo ($data->get('count')['target'] > 1) ? 's' : ''; ?></span>
                </li>
                <li class="date">
                    <span>Temps estimé pour l'envoi</span>
                    <span><?php echo $data->display_estimated_time(); ?></span>
                </li>
                <?php endif; ?>
                <li class="prix">
                    <span>Coût potentiel de la campagne</span>
                    <span><?php if ($data->price() >= 1) : ?><?php echo number_format($data->price(), 2, ',', ' '); ?>&nbsp;&euro;<?php else : ?>coût négligeable<?php endif; ?></span>
                </li>
            </ul>
        </section>
        
        <?php if ($data->get('status') == 'open') : ?>
        <section class="contenu demi">
            <a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'launch', 'volet' => 'launch')); ?>" class="nostyle"><button class="vert clair long" style="margin: .25em auto .15em;">Envoi de la campagne</button></a>
        </section>
        <?php endif; ?>
    </div>
    
<?php endswitch; ?>