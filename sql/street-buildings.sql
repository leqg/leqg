SELECT      `building`,
            `id`
FROM        `building`
WHERE       `street` = :street
ORDER BY    `building` ASC
