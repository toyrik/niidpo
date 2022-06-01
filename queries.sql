-- Получаем список клиентов из таблицы test_client, кто хоть раз совершил покупку
SELECT 
    DISTINCT tc.name 
FROM test_client tc
LEFT JOIN test_purchase tp on tp.client_id = tc.client_id
WHERE 1=1
AND tp.date is not NULL

-- Получаем список клиентов из таблицы test_client, 
-- кто совершил 2 и больше покупок. 
-- В дополнительном поле выводим название последнего 
-- купленного товара (по дате). 
SELECT 
    tc.name
    ,tp2.name 
FROM test_client tc 
LEFT JOIN test_purchase tp on tp.client_id = tc.client_id
LEFT JOIN test_product tp2 on tp.prod_id = tp2.prod_id 
WHERE 1=1
AND (SELECT count(client_id) FROM test_purchase tp3 WHERE client_id = tc.client_id) >= 2
and tp.`date` = (
        SELECT MAX(tp3.`date`)
        FROM test_purchase tp3 
        WHERE tp3.client_id = tc.client_id
        GROUP BY tp.client_id
        )

-- Получаем список товаров из таблицы test_product, которые ни разу не купили.
SELECT 
    pr.name
FROM test_product pr
LEFT JOIN test_purchase pu on pr.prod_id = pu.prod_id
WHERE 1=1
AND pu.date is NULL
