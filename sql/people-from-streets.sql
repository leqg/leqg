SELECT      `people`
FROM        `address`
WHERE       `street` IN (:ids)
GROUP BY    `people`