SELECT      `id`
FROM        `people`
WHERE       `tags` LIKE :search
OR          `id` IN (
    SELECT      `contact_id`
    FROM        `historique`
    WHERE       `historique_objet` LIKE :search
    OR          `historique_notes` LIKE :search
)
OR          `id` IN (
    SELECT      `contact_id`
    FROM        `fichiers`
    WHERE       `fichier_nom` LIKE :search
    OR          `fichier_description` LIKE :search
)
ORDER BY    `nom` ASC,
            `nom_usage` ASC,
            `prenoms` ASC
            