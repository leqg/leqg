SELECT      `historique_id` AS `id`,
            MD5(`historique_id`) AS `md5`,
            `contact_id` AS `contact`,
            `compte_id` AS `user`,
            `dossier_id` AS `folder`,
            `historique_type` AS `type`,
            `historique_date` AS `date`,
            `historique_objet` AS `objet`,
            `historique_notes` AS `notes`,
            `historique_timestamp` AS `time`,
            `campagne_id` AS `campagne`
FROM        `historique`
WHERE       `historique_type` = "contact"
OR          `historique_type` = "telephone"
OR          `historique_type` = "courriel"
OR          `historique_type` = "courrier"
OR          `historique_type` = "autre"
ORDER BY    `historique_date` DESC
LIMIT       0, 15