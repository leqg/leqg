SELECT      `id` 
FROM        `people`
WHERE       CONCAT_WS(
                " ", 
                `prenoms`, 
                `nom`,
                `nom_usage`,
                `nom`,
                `prenoms`
            ) LIKE :search
ORDER BY    `nom` ASC,
            `nom_usage` ASC,
            `prenoms` ASC
