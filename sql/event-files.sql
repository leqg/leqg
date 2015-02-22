SELECT      `fichier_id` AS `id`,
            `contact_id` AS `contact`,
            `compte_id` AS `user`,
            `interaction_id` AS `event`,
            `dossier_id` AS `folder`,
            `fichier_nom` AS `nom`,
            `fichier_labels` AS `label`,
            `fichier_description` AS `description`,
            `fichier_url` AS `url`,
            `fichier_reference` AS `reference`, 
            `fichier_timestamp` AS `time`
FROM        `fichiers`
WHERE       `interaction_id` = :event
ORDER BY    `fichier_timestamp` DESC
