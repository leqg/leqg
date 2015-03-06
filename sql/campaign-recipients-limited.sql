SELECT      *
FROM        `tracking`
WHERE       `campaign` = :campaign
ORDER BY    `contact` ASC
LIMIT       :first , 10
