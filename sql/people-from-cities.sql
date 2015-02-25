SELECT      `people`
FROM        `address`
WHERE       `city` IN (:ids)
GROUP BY    `people`