SELECT      COUNT(*)
FROM        `people`
WHERE       `people`.`id` IN (
    SELECT      `address`.`people`
    FROM        `address`
    WHERE       `address`.`city` = :city
)
AND         `people`.`id` IN (
    SELECT      `coordonnees`.`contact_id`
    FROM        `coordonnees`
    WHERE       `coordonnee_type` = :type
)