SELECT      `dossier_id` AS `id`,
            `dossier_nom` AS `name`,
            `dossier_description` AS `description`,
            `dossier_statut` AS `statut`,
            `dossier_date_ouverture` AS `begin`,
            `dossier_date_fermeture` AS `end`,
            `dossier_notes` AS `notes`
FROM        `dossiers`
WHERE       `dossier_id` = :folder