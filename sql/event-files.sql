SELECT      `fichier_id` AS `id`,
            `contact_id` AS `contact`,
            `compte_id` AS `user`,
            `interaction_id` AS `event`,
            `fichier_nom` AS `name`,
            `fichier_description` AS `description`,
            `fichier_url` AS `url`, 
            `fichier_timestamp` AS `time`
FROM        `fichiers`
WHERE       `interaction_id` = :event
ORDER BY    `fichier_timestamp` DESC
