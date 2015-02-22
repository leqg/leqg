SELECT      `people`
FROM        `address`
WHERE       `city` IN (:cities)
GROUP BY    `people`
