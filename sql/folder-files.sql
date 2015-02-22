SELECT      `fichier_id`
FROM        `fichiers`
LEFT JOIN   `historique`
ON          `fichiers`.`interaction_id` = `historique`.`historique_id`
WHERE       `historique`.`dossier_id` = :folder
ORDER BY    `fichier_timestamp` DESC
