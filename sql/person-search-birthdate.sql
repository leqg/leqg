SELECT      `id`
FROM        `people`
WHERE       `date_naissance` = :date
ORDER BY    `contact_nom` ASC,
            `contact_nom_usage` ASC,
            `contact_prenoms` ASC
