SELECT      `campagne_id` AS `id`,
            `campagne_type` AS `type`,
            `objet`,
            `template`
FROM        `campagne`
WHERE       `template` != ''
OR          `template` IS NOT NULL
ORDER BY    `campagne_date` DESC
