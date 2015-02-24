SELECT      `fichier_id` AS `id`,
            `interaction_id` AS `event`,
            `fichier_nom` AS `name`,
            `fichier_description` AS `desc`,
            `fichier_url` AS `url`,
            `fichier_timestamp` AS `time`
FROM        `fichiers`
LEFT JOIN   `historique`
ON          `fichiers`.`interaction_id` = `historique`.`historique_id`
WHERE       `historique`.`dossier_id` = :folder
ORDER BY    `fichier_timestamp` DESC
