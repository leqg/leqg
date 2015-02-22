SELECT      `historique_id` AS `id`,
            MD5(`historique_id`) AS `md5`,
            `contact_id` AS `people`,
            `compte_id` AS `user`,
            `dossier_id` AS `folder`,
            `historique_type` AS `type`,
            `historique_lieu` AS `lieu`,
            `historique_date` AS `date`,
            `historique_objet` AS `objet`,
            `historique_notes` AS `notes`,
            `historique_timestamp` AS `time`,
            `campagne_id` AS `campaign`
FROM        `historique`
WHERE       `historique_id` = :event