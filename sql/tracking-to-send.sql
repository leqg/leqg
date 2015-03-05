SELECT      `campaign`,
            `email`
FROM        `tracking`
WHERE       `status` = "pending"
ORDER BY    `time`, `email` ASC
LIMIT       0, 3
