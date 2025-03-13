SET SESSION group_concat_max_len = 1000000;

-- Generar dinámicamente las columnas de las categorías importantes
SET @sql = NULL;
SELECT GROUP_CONCAT(DISTINCT
    CONCAT('SUM(CASE WHEN c.is_important = 1 AND p.category_id = ', c.id, ' THEN oref.quantity ELSE 0 END) AS `', c.name, '`')
) INTO @sql
FROM categories c;

-- Agregar la columna "otros" para las categorías que no son importantes
SET @sql = CONCAT(@sql, ',
    SUM(CASE WHEN c.is_important = 0 THEN oref.quantity ELSE 0 END) AS `otros`');

-- Construir la consulta completa
SET @sql = CONCAT('
    SELECT o.id AS orden, o.reference_name, ', @sql, '
    FROM orders o
    JOIN order_references oref ON o.id = oref.order_id
    JOIN products p ON p.id = oref.product_id
    JOIN categories c ON p.category_id = c.id
    GROUP BY o.id, o.reference_name
');

-- Ejecutar la consulta
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
para cronograma



CREATE VIEW order_reference_summaries AS
SELECT 
    CONCAT(oref.order_id, '-', oref.product_id, '-', oref.size_id) AS id, -- Clave primaria falsa
    oref.order_id,
    product_id,
    size_id,
    CONCAT(REPLACE(p.code, '-', ''), REPLACE(s.name, '-', '')) AS new_code, -- Genera el new_code sin guiones
    SUM(oref.quantity) AS total_quantity,
    SUM(oref.price) AS total_price
FROM order_references oref
JOIN products p ON p.id = oref.product_id
JOIN sizes s ON s.id = oref.size_id
GROUP BY oref.order_id, product_id, size_id;






docker build . -t abprod
docker run -it -p 8000:8000 abprod