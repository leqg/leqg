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
        
        case 'newTemplate':
            $data->template_empty();
            break;
        
        case 'modificationObjetCampagne':
            $data->object($_POST['objet']);
            break;
        
        case 'copieTemplate':
            $data->template_copy($_GET['template']);
            break;
        
        case 'modificationSMSCampagne':
            $data->update('message', $_POST['message']);
            break;
        
        case 'updateTracking':
            $data->tracking_update();
            break;
    }
}

Configuration::write('tpl.actuel', $data->get('type'));
Core::tpl_header();
?>
<?php if ($data->get('type') == 'email') : ?>
<h2 id="titre-campagne" <?php if ($data->get('status') == 'open') : ?>class="titre"<?php endif; ?> data-campagne="<?php $data->get('id'); ?>"><?php if (!empty($data->get('objet'))) { echo 'Campagne &laquo;&nbsp;' . $data->get('objet') . '&nbsp;&raquo;'; } else { echo 'Campagne sans titre'; } ?></h2>
<?php elseif ($data->get('type') == 'sms') : ?>
<h2 id="titre-campagne" <?php if ($data->get('status') == 'open') : ?>class="titre"<?php endif; ?> data-campagne="<?php $data->get('id'); ?>"><?php if (!empty($data->get('titre'))) { echo 'Campagne &laquo;&nbsp;' . $data->get('titre') . '&nbsp;&raquo;'; } else { echo 'Campagne sans titre'; } ?></h2>
<?php endif; ?>

<nav class="onglets">
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'))); ?>">Supervision</a>
    <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'destinataires')); ?>">Destinataire</a>
    <?php if ($data->get('type') == 'email' && $data->get('status') == 'open') : ?>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'template')); ?>">Template</a>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'visu')); ?>">Email final</a>
    <?php elseif ($data->get('type') == 'email' && ($data->get('status') == 'send')) : ?>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'visu')); ?>">Email final</a>
        <a href="<?php Core::tpl_go_to('campagne', array('id' => $data->get('id'), 'volet' => 'statistiques')); ?>">Statistiques</a>
    <?php elseif ($data->get('type') == 'sms' && ($data->get('status') == 'send')) : ?>
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
				    <a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'template', 'operation' => 'newTemplate')); ?>" class="nostyle"><h4>Template vierge</h4></a>
				    <p>Vous pouvez commencer à partir d'un template vierge ou bien récupérer un précédent template pour l'adapter.</p>
				</li>
				<?php foreach ($templates as $element) : ?>
				<li class="template">
					<a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'template', 'operation' => 'copieTemplate', 'template' => $element['id'])); ?>" class="nostyle"><h4><?php if (!empty($element['objet'])) { echo $element['objet']; } else { echo 'Campagne sans titre'; } ?></h4></a>
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
                <!--<script>CKEDITOR.replace( 'templateEditor' );</script>-->
                <button class="vert clair" type="submit">Sauvegarder le template</button>
            </form>
        </section>
            
        <?php endif; ?>
    </div>

<?php break; case 'visu': ?>

    <div class="colonne">
        <section class="contenu previsualisationEmail">
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
                        <th>Coordonnée</th>
                        <th class="align-center">Contact</th>
                        <th class="align-center petit">Statut</th>
                    </tr>
                </thead>
                <?php foreach ($destinataires as $destinataire) : $contact = new People($destinataire['contact']); ?>
                <tbody>
                    <tr>
                        <td><?php if ($data->get('status') == 'open') { echo $destinataire[0]; } else { echo $destinataire['email']; } ?></td>
                        <td><a href="<?php echo Core::tpl_go_to('contact', array('contact' => $contact->get('id'))); ?>"><?php echo $contact->display_name(); ?></a></td>
                        <td><?php echo Campaign::display_status($destinataire['status']); ?></td>
                    </tr>
                </tbody>
                <?php endforeach; ?>
            </table>
            <?php if ($data->get('status') == 'send') { ?><a class="nostyle" href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'volet' => 'destinataires', 'operation' => 'updateTracking')); ?>"><button class="vert clair">Mettre à jour</button></a><?php } ?>
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
             --><div class="npai" style="width: <?php echo pourcentage($nombre['scheduled'], $nombre['total']); ?>%;"><span>Élément<?php if($nombre['scheduled'] > 1) { echo 's'; } ?>&nbsp;prévu<?php if($nombre['scheduled'] > 1) { echo 's'; } ?>&nbsp;:&nbsp;<?php echo number_format($nombre['scheduled'], 0, ',', ' '); ?></span></div><!--
             --><div class="absent" style="width: <?php echo pourcentage($nombre['queued'], $nombre['total']); ?>%;"><span>Élément<?php if($nombre['queued'] > 1) { echo 's'; } ?>&nbsp;en&nbsp;cours&nbsp;:&nbsp;<?php echo number_format($nombre['queued'], 0, ',', ' '); ?></span></div><!--
             --><div class="ouvert" style="width: <?php echo pourcentage($nombre['sent'], $nombre['total']); ?>%;"><span>Élément<?php if($nombre['sent'] > 1) { echo 's'; } ?>&nbsp;délivré<?php if($nombre['sent'] > 1) { echo 's'; } ?>&nbsp;:&nbsp;<?php echo number_format($nombre['sent'], 0, ',', ' '); ?></span></div><!--
             --><div class="procuration" style="width: <?php echo pourcentage($nombre['rejected'], $nombre['total']); ?>%;"><span>Élément<?php if($nombre['rejected'] > 1) { echo 's'; } ?>&nbsp;en&nbsp;erreur&nbsp;:&nbsp;<?php echo number_format($nombre['rejected'], 0, ',', ' '); ?></span></div><!--
             --><div class="contact" style="width: <?php echo pourcentage($nombre['invalid'], $nombre['total']); ?>%;"><span>Élément<?php if($nombre['invalid'] > 1) { echo 's'; } ?>&nbsp;invalide<?php if($nombre['invalid'] > 1) { echo 's'; } ?>&nbsp;:&nbsp;<?php echo number_format($nombre['invalid'], 0, ',', ' '); ?></span></div><!--
         --></div>
         
            <ul class="statistiquesMission">
                <?php if ($nombre['scheduled']) : ?><li><strong><?php echo number_format($nombre['scheduled'], 0, ',', ' '); ?></strong> élément<?php if($nombre['scheduled'] > 1) { echo 's'; } ?> planifié<?php if($nombre['scheduled'] > 1) { echo 's'; } ?></li><?php endif; ?>
                <?php if ($nombre['queued']) : ?><li><strong><?php echo number_format($nombre['queued'], 0, ',', ' '); ?></strong> élément<?php if($nombre['queued'] > 1) { echo 's'; } ?> en cours d'envoi</li><?php endif; ?>
                <?php if ($nombre['sent']) : ?><li><strong><?php echo number_format($nombre['sent'], 0, ',', ' '); ?></strong> élément<?php if($nombre['sent'] > 1) { echo 's'; } ?> envoyé<?php if($nombre['sent'] > 1) { echo 's'; } ?> et reçu<?php if($nombre['sent'] > 1) { echo 's'; } ?></li><?php endif; ?>
                <?php if ($nombre['rejected']) : ?><li><strong><?php echo number_format($nombre['rejected'], 0, ',', ' '); ?></strong> élément<?php if($nombre['rejected'] > 1) { echo 's'; } ?> en erreur</li><?php endif; ?>
                <?php if ($nombre['invalid']) : ?><li><strong><?php echo number_format($nombre['invalid'], 0, ',', ' '); ?></strong> élément<?php if($nombre['invalid'] > 1) { echo 's'; } ?> invalides</li><?php endif; ?>
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
                    <a href="<?php Core::tpl_go_to('contact', array('contact' => $erreur['contact'])); ?>" class="nostyle"><?php echo $erreur['name']; ?></a><br>
                    <em>Erreur :</em> <?php echo $erreur['error']; ?><br>
                    <em>Date :</em> <?php echo date('d/m/Y H\hi', strtotime($erreur['time'])); ?>
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
                <li class="type">
                    <span>Type de campagne</span>
                    <span><?php if ($data->get('type') == 'email') { echo 'Emailing'; } elseif ($data->get('type') == 'sms') { echo 'SMS groupés'; } elseif ($data->get('type') == 'publi') { echo 'Publipostage'; } else { echo 'Type de campagne inconnu'; } ?></span>
                </li>
                <li class="courrier">
                    <span>Statut de la campagne</span>
                    <span><?php echo Campaign::display_status($data->get('status')); ?></span>
                </li>
                <li class="responsable">
                    <span>Créateur</span>
                    <span><?php echo User::get_login_by_ID($data->get('user')); ?></span>
                </li>
                <?php if ($data->get('type') == 'email' && $data->get('status') != 'open') : ?>
                <li class="utilisateurs inscrit">
                    <span>Nombre d'envois</span>
                    <span><strong><?php echo number_format($data->get('count')['items']['all'], 0, ',', ' '); ?></strong> destinataire<?php echo ($data->get('count')['items']['all'] > 1) ? 's' : ''; ?></span>
                </li>
                <?php endif; ?>
                <?php if ($data->get('type') == 'email' && $data->get('status') == 'open') : ?>
                <li class="utilisateurs inscrit">
                    <span>Ciblage de la campagne</span>
                    <span><strong><?php echo number_format($data->get('count')['target'], 0, ',', ' '); ?></strong> destinataire<?php echo ($data->get('count')['target'] > 1) ? 's' : ''; ?> / <?php echo $data->get('count')['emails']; ?> email<?php echo ($data->get('count')['emails'] > 1) ? 's' : ''; ?></span>
                </li>
                <li class="date">
                    <span>Temps estimé pour l'envoi</span>
                    <span><?php echo $data->display_estimated_time(); ?></span>
                </li>
                <?php endif; ?>
                <li class="prix">
                    <span>Coût potentiel de la campagne</span>
                    <span><?php if ($data->get('type') == 'sms') { echo number_format($data->price(), 2, ',', ' ').'&nbsp;&euro;'; } elseif ($data->price() >= 1) { echo number_format($data->price(), 2, ',', ' ').'&nbsp;&euro;'; } else { echo 'coût négligeable'; } ?></span>
                </li>
            </ul>
        </section>
        
        <?php if ($data->get('status') == 'open' && ( ($data->get('type') == 'email' && !empty($data->get('objet'))) || ($data->get('type') == 'sms' && !empty($data->get('message'))) )) : ?>
        <section class="contenu demi">
            <a href="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'launch', 'volet' => 'launch')); ?>" class="nostyle"><button class="vert clair long" style="margin: .25em auto .15em;">Envoi de la campagne</button></a>
        </section>
        <?php endif; ?>
    </div>
    
    <div class="colonne demi droite">
        <?php if ($data->get('type') == 'email') : ?>
        <section class="contenu demi">
            <h4>Objet de la campagne</h4>
            <?php if (isset($_GET['operation']) && $_GET['operation'] == 'modificationObjet') : ?>
                <form action="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'modificationObjetCampagne')); ?>" method="post">
                    <ul class="formulaire">
                        <li>
                            <label class="small" for="objet">Objet de la campagne email</label>
                            <span class="form-icon decalage"><input type="text" name="objet" value="<?php echo $data->get('objet'); ?>"></span>
                        </li>
                        <li>
                            <button type="submit" class="vert clair long">Valider l'objet</button>
                        </li>
                    </ul>
                </form>
            <?php else : ?>
                <p><?php if (empty($data->get('objet'))) : echo 'Aucun titre actuellement, ce titre est nécessaire pour l\'envoi.'; else: echo $data->get('objet'); endif; ?></p>
                <?php if ($data->get('status') == 'open') : ?><p style="text-align: center;"><a href="<?php echo Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'modificationObjet')); ?>" class="modifierObjet">Modifier cet objet</a></p><?php endif; ?>
            <?php endif; ?>
        </section>
        <?php elseif ($data->get('type') == 'sms') : ?>
        <section class="contenu demi">
            <h4>Contenu du SMS</h4>
            <?php if (isset($_GET['operation']) && $_GET['operation'] == 'modificationSMS') : ?>
                <form action="<?php Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'modificationSMSCampagne')); ?>" method="post">
                    <ul class="formulaire">
                        <li>
                            <label class="small" for="objet">Texte envoyé aux destinataires</label>
                            <span class="form-icon decalage sms"><textarea type="text" name="message"><?php echo $data->get('message'); ?></textarea></span>
                        </li>
                        <li>
                            <button type="submit" class="vert clair long">Valider l'objet</button>
                        </li>
                    </ul>
                </form>
            <?php else : ?>
                <p><?php if (empty($data->get('message'))) : echo 'Aucun message actuellement, ce paramètre est nécessaire pour l\'envoi.'; else: echo $data->get('message'); endif; ?></p>
                <?php if ($data->get('status') == 'open') : ?><p style="text-align: center;"><a href="<?php echo Core::tpl_go_to('campagne', array('id' => $_GET['id'], 'operation' => 'modificationSMS')); ?>" class="modifierObjet">Modifier le message</a></p><?php endif; ?>
            <?php endif; ?>
        </section>
        <?php endif; ?>
    </div>
    
<?php endswitch; ?>