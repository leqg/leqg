SELECT      COUNT(*)
FROM        `coordonnees`
WHERE       `coordonnee_type` = :type 
AND         `contact_id` = :contact
