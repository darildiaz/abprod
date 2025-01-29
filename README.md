php artisan make:filament-resource User --generate
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Size --generate
php artisan make:filament-resource Product --generate
php artisan make:filament-resource Center --generate
php artisan make:filament-resource ProductCenter --generate
php artisan make:filament-resource Operator --generate
php artisan make:filament-resource Production --generate
php artisan make:filament-resource QuestionCategory --generate
php artisan make:filament-resource Question --generate
php artisan make:filament-resource Customer --generate
php artisan make:filament-resource Order --generate
php artisan make:filament-resource OrderItem --generate
php artisan make:filament-resource OrderItemProduct --generate
php artisan make:filament-resource OrderReference --generate
php artisan make:filament-resource OrderQuestionAnswer --generate
php artisan make:filament-resource OrderMold --generate




base de datos s
https://dbdiagram.io/d/abprod10-679439ed263d6cf9a0034b09

Table users {
  id int [pk] // Primary key
  name varchar // Nombre del usuario
  email varchar // Correo electrónico
  password varchar // Contraseña
  created_at timestamp
  updated_at timestamp
}

Table categories {
  id int [pk] // Primary key
  name varchar // Nombre de la categoría
  description text // Descripción de la categoría
  order int // Orden para mostrar la categoría
  is_important boolean // Indicador de si es importante
  created_at timestamp
  updated_at timestamp
}

Table sizes {
  id int [pk] // Primary key
  name varchar // Nombre del tamaño/talle (e.g., Small, Medium, Large)
  created_at timestamp
  updated_at timestamp
}



Table products {
  id int [pk] // Primary key
  code varchar [unique] // Código único del producto (e.g., camf03tl01)
  category_id int [ref: > categories.id] // Relación con categorías
  price int // Precio del producto
  created_at timestamp
  updated_at timestamp
}

Table centers {
  id int [pk] // Primary key
  name varchar // Nombre del centro de producción
  level int // Nivel del centro 
  created_at timestamp
  updated_at timestamp
}
Table product_center{
  id int [pk]
  product_id int [ref:> products.id]
  center_id int [ref:> centers.id]
  price int

}
Table operators {
  id int [pk] // Primary key
  name varchar // Nombre del operador
  position varchar // Cargo del operador
  user_id int [ref: > users.id] // Relación con usuarios (vendedores u operadores)
  center_id int [ref: > centers.id]
  created_at timestamp
  updated_at timestamp
}

Table production {
  id int [pk] // Primary key
  date date // Fecha de producción
  order_id int [ref:> orders.id]
  center_id int [ref: > centers.id] // Relación con centros
  operator_id int [ref: > operators.id] // Relación con operadores
  product_id int [ref: > products.id] // Relación con productos
  quantity int // Cantidad producida
  created_at timestamp
  updated_at timestamp
}



Table question_categories {
  id int [pk] // Primary key
  name varchar // Nombre de la clasificación (e.g., Deportivo, Estudiantil)
  description text // Descripción opcional
  created_at timestamp
  updated_at timestamp
}

Table questions {
  id int [pk] // Primary key
  text varchar // Texto de la pregunta (e.g., "¿Cuál es la talla?")
  type enum("string", "integer", "list") // Tipo de dato de la pregunta
  options text // Opciones en caso de que el tipo sea "list" (JSON, separadas por comas, etc.)
  is_required boolean // Si la pregunta es obligatoria
  category_id int [ref: > question_categories.id] // Relación con una categoría
  created_at timestamp
  updated_at timestamp
}

table customers{
  id int [pk]
  nif varchar //cedula numero
  name varchar //nombre del cliente 
  address varchar // direccion
  phone varchar //telefono
  user_id int [ref :> users.id] //vendedor
}
Table orders {
  id int [pk] // Primary key
  customer_id int [ref: > customers.id]
  seller_id int [ref: > users.id] // Relación con el vendedor
  reference_name varchar // Nombre de referencia del pedido
  issue_date date // Fecha de emisión del pedido
  delivery_date date // Fecha de entrega
  total int // Total del pedido
  classification_id int [ref: > question_categories.id] // Clasificación del pedido
  status int
  created_at timestamp
  updated_at timestamp
}

Table order_question_answers {
  id int [pk] // Primary key
  order_id int [ref: > orders.id] // Relación con el pedido
  question_id int [ref: > questions.id] // Relación con la pregunta
  answer text // Respuesta (puede ser texto, número, opción seleccionada, etc.)
  created_at timestamp
  updated_at timestamp
}



Table order_references {
  id int [pk] // Primary key
  name varchar // Nombre del producto de referencia
  product_id int [ref: > products.id] // Relación con productos
  price decimal(10, 2) // Precio predefinido
  created_at timestamp
  updated_at timestamp
}

Table order_items {
  id int [pk] // Primary key
  order_id int [ref: > orders.id] // Relación con pedidos
  model_id int [ref: > order_molds.id]
  name varchar // Nombre personalizado del ítem
  number varchar // Número personalizado
  other varchar // otros personalizado
  size_id int [ref: > sizes.id] // Relación con talles
  quantity int // Cantidad total
  price int // Cantidad total
  created_at timestamp
  updated_at timestamp
}

Table order_item_products {
  id int [pk] // Primary key
  order_item_id int [ref: > order_items.id] // Relación con ítems
  reference_id int [ref: > order_references.id] // Relación con el diccionario
  quantity int // Cantidad de este producto específico en el ítem
  created_at timestamp
  updated_at timestamp
}
Table order_molds {
  id int [pk] // Primary key
  title varchar //titulo
  imagen text //imagen
  created_at timestamp
  updated_at timestamp
}

Ref: "orders"."id" < "orders"."issue_date"





SELECT PARA MI CRONOGRAMA
SELECT 
    o.id AS order_id, -- ID de la orden
    o.reference_name AS order_name, -- Nombre de referencia de la orden
    c.name AS category_name, -- Nombre de la categoría
    p.code AS product_name, -- Nombre del producto
    SUM(oip.quantity) AS total_quantity -- Cantidad total del producto en la orden
FROM orders o
JOIN order_items oi ON o.id = oi.order_id -- Relación entre órdenes y sus ítems
JOIN order_item_products oip ON oi.id = oip.order_item_id -- Relación entre ítems y productos
JOIN order_references orf ON oip.reference_id = orf.id -- Relación entre productos de ítems y referencias
JOIN products p ON orf.product_id = p.id -- Relación entre referencias y productos
JOIN categories c ON p.category_id = c.id -- Relación entre productos y categorías
GROUP BY 
    o.id, 
    o.reference_name, 
    c.name, 
    p.code -- Agrupar por orden, categoría y producto
ORDER BY 
    o.id, 
    c.name, 
    p.code;


select para mi produccion
SELECT 
    p.id,p.code AS product_code, 
    SUM(oip.quantity) AS total_quantity
FROM order_item_products oip
JOIN order_items oi ON oip.order_item_id = oi.id
JOIN order_references orf ON oip.reference_id = orf.id
JOIN products p ON orf.product_id = p.id
WHERE oi.order_id = 21
GROUP BY p.code;






