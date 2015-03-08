SELECT      `coordonnee_email` 
FROM        `coordonnees` 
WHERE       `coordonnee_type` = 'email' 
AND         `contact_id` = :contact
AND         `optout` = 0