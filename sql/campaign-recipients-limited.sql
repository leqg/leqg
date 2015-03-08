SELECT      *
FROM        `tracking`
WHERE       `campaign` = :campaign
ORDER BY    `email` ASC
LIMIT       :first , 10
