<?php
if (isset($_POST)) {
    // On récupère les informations envoyées
    $form = $_POST;
        
    // On lance la création d'un mot de passe
    function randomPassword() 
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    $form['pass'] = randomPassword();
        
    // On hash le mot de passe
    $form['pass_sec'] = password_hash($form['pass'], PASSWORD_BCRYPT);
        
    // On enregistre les informations
    $link = Configuration::read('db.core');
    $client = Configuration::read('ini')['LEQG']['compte'];

    $query = $link->prepare('INSERT INTO `user` (`client`, `email`, `password`, `firstname`, `lastname`, `auth_level`) VALUES (:client, :email, :pass, :first, :last, :auth)');
    $query->bindParam(':client', $client);
    $query->bindParam(':email', $form['email']);
    $query->bindParam(':pass', $form['pass_sec']);
    $query->bindParam(':first', $form['prenom']);
    $query->bindParam(':last', $form['nom']);
    $query->bindParam(':auth', $form['selectAuth']);
    $query->execute();
        
    // On lance l'envoi du mail avec les informations de connexion
    $email = file_get_contents('tpl/mail/user-creation.tpl.html');
    $objet = 'LeQG – Votre compte a été créé par ' . User::get_login_by_ID(User::ID()) . '.';
        
    // On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
    $email = strtr($email, array('{COMPTE}' => $form['prenom'] . ' ' . $form['nom']));
    $email = strtr($email, array('{USER}' => User::get_login_by_ID(User::ID())));
    $email = strtr($email, array('{EMAIL}' => $form['email']));
    $email = strtr($email, array('{PASS}' => $form['pass']));
        
    // On démarre l'instance
    $mail = new PHPMailer();
        
    // On récupère les informations sur l'API
    $api = Configuration::read('api');
        
    // On contacte le serveur d'envoi SMTP
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = $api['mail']['smtp']['host'];
    $mail->Port = $api['mail']['smtp']['port'];
    $mail->Username = $api['mail']['smtp']['user'];
    $mail->Password = $api['mail']['smtp']['pass'];
    
    // On configure le mail à envoyer
    $mail->CharSet = $api['mail']['charset'];
    $mail->SetFrom('ne-pas-repondre@leqg.info', 'LeQG');
    $mail->AddReplyTo('tech@leqg.info', 'LeQG équipe technique');
    $mail->AddAddress($form['email'], $form['prenom'] . ' ' . $form['nom']);
    $mail->Subject = $objet;
    $mail->MsgHTML($email);
        
    $mail->Send();
        
    Core::goTo('administration', true);
        
        
} else {
    Core::goTo('administration', true);
}
?>
