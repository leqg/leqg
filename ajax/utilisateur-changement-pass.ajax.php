<?php

    // On récupère les informations envoyées
    $donnees = $_POST;
    
    // On cherche les données actuelle du mot de passe
    $query = 'SELECT * FROM `users` WHERE `user_id` = ' . $donnees['user'];
    $sql = $noyau->query($query);
    $info = $sql->fetch_assoc();
    
    // On vérifie que le mot de passe entré correspond au mot de passe existant 
if ($user->encrypt_pass($donnees['actuel']) != $info['user_password']) { $core->tpl_go_to('utilisateur', array('modification' => 'pass', 'message' => 'erreur-motdepasse'), true); 
}

    // On fabrique le nouveau mot de passe
    $new = $user->encrypt_pass($donnees['nouveau']);
    
    // On enregistre le nouvel email dans la base de données en attendant qu'il soit validé
    $query = 'UPDATE `users` SET `user_password` = "' . $new . '", `user_reinit` = NOW() WHERE `user_id` = ' . $info['user_id'];
    $noyau->query($query);
    
    // On fait partir le mail permettant de valider le changement d'email
    $email = file_get_contents('tpl/mail/changement-pass.tpl.html');
    $objet = 'LeQG – Changement du mot de passe de votre compte de votre compte.';
    
    // On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
    $email = strtr($email, array('{USER}' => $info['user_firstname'] . ' ' . $info['user_lastname']));
    
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
    $mail->AddAddress($info['user_email'], $info['user_firstname'] . ' ' . $info['user_lastname']);
    $mail->Subject = $objet;
    $mail->MsgHTML($email);

    // On redirige vers la page de profil avec un message spécifique
    if ($mail->Send()) { $core->tpl_go_to(true); 
    }
    
    
?>