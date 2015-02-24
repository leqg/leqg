SELECT      `status`, 
            COUNT(`status`) AS `nb`
FROM        `tracking`
WHERE       `campaign` = :campaign
GROUP BY    `status`