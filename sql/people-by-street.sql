SELECT      `people`
FROM        `address`
WHERE       `street` IN (:streets)
GROUP BY    `people`
