SELECT      `contact_id`
FROM        `coordonnees`
WHERE       `coordonnee_type` = "email"
GROUP BY    `contact_id`