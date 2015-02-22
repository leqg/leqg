SELECT      *,
            MD5(`id`) AS `md5`
FROM        `people`
WHERE       `id` = :person
