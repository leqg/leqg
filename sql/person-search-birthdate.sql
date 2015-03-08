SELECT      `id`
FROM        `people`
WHERE       `date_naissance` = :date
ORDER BY    `nom` ASC,
            `nom_usage` ASC,
            `prenoms` ASC
