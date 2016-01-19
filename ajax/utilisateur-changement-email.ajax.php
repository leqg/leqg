<?php
/**
 * Changement de l'email d'un utilisateur
 *
 * PHP version 5
 *
 * @category Ajax
 * @package  LeQG
 * @author   Damien Senger <hi@hiwelo.co>
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3.0
 * @link     http://leqg.info
 */


// On récupère les informations envoyées
$donnees = $_POST;

// On fabrique le hash de validation
$hash = sha1($donnees['email'] . time());

// On enregistre le nouvel email dans la base de données
// en attendant qu'il soit validé
$query = 'UPDATE `users`
          SET `user_new_email` = "' . $donnees['email'] . '",
              `user_new_email_hash` = "' . $hash . '"
          WHERE `user_id` = ' . $donnees['user'];
$noyau->query($query);

// On fait partir le mail permettant de valider le changement d'email
$email = file_get_contents('tpl/mail/changement-email.tpl.html');
$objet = 'LeQG – Changement de l\'adresse email de votre compte.';

// On insère dans le mail l'URL du fichier pour qu'il puisse être téléchargé
$email = strtr(
    $email,
    array('{USER}' => $user->get_the_nickname())
);
$email = strtr(
    $email,
    array(
        '{URL}' => 'http://' . $config['SERVER']['url'] .
                   '/validation-email.php?email=' . $hash
    )
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
$mail->AddAddress($donnees['email'], $user->get_the_nickname());
$mail->Subject = $objet;
$mail->MsgHTML($email);

// On redirige vers la page de profil avec un message spécifique
if ($mail->Send()) {
    $core->tpl_go_to('utilisateur', array('message' => 'changement-email'), true);
}
