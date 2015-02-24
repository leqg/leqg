SELECT      `zipcode`,
            COUNT(`zipcode`) AS `nb`
FROM        `address`
WHERE       `street` = :street
GROUP BY    `zipcode`
ORDER BY    `nb` DESC
LIMIT       0, 1
