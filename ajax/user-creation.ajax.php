<?php

    // On récupère les informations
    $infos = array('firstname' => $core->securisation_string($_POST['firstname']),
                   'lastname' => $core->securisation_string($_POST['lastname']),
                   'email' => $core->securisation_string($_POST['email']),
                   'auth' => $_POST['auth']);
                   
    // On va calculer le mot de passe
    $infos['pass'] = $user->pass_generator();
    
    // On va créer l'utilisateur
    $user->creation($infos);
    
    // On va envoyer le mail à l'utilisateur contenant son mot de passe
    $email = file_get_contents('tpl/mail/user-creation.tpl.html');
    $objet = 'LeQG – Création de votre compte utilisateur.';
    
    // On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
    $email = strtr(
        $email, array('{COMPTE}' => $infos['firstname'] ,
        '{USER}' => $user->get_login_by_ID($_COOKIE['leqg-user']) ,
        '{EMAIL}' => $infos['email'] ,
        '{PASS}' => $infos['pass'] )
    );
        
    // On démarre l'instance
    $mail = new PHPMailer();
    
    // On contacte le serveur d'envoi SMTP
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = $api['mail']['smtp']['host'];
    $mail->Port = $api['mail']['smtp']['port'];
    $mail->Username = $api['mail']['smtp']['user'];
    $mail->Password = $api['mail']['smtp']['pass'];

    // On configure le mail à envoyer
    $mail->CharSet = $api['mail']['charset'];
    $mail->SetFrom('noreply@leqg.info', 'LeQG');
    $mail->AddReplyTo('tech@leqg.info', 'LeQG équipe technique');
    $mail->AddAddress($infos['email'], $infos['firstname'] . ' ' . $infos['lastname']);
    $mail->Subject = $objet;
    $mail->MsgHTML($email);

    $mail->Send();
    
    
    $core->tpl_go_to('administration', true);
?>