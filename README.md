# Тестовое задание: API для поиска товаров с фильтрацией

## Описание

Данное решение представляет собой реализацию тестового задания на создание API для поиска товаров с фильтрацией, сортировкой и пагинацией.

**Важно:** Это не полный проект Laravel, а только файлы, необходимые для выполнения задания.

## Доступные эндпоинты

### 1. Получение списка товаров с фильтрацией

**Метод:** `GET`  
**Путь:** `/api/products`

#### Параметры фильтрации (query-параметры):

| Параметр | Тип | Описание | Пример |
|----------|-----|----------|--------|
| `q` | string | Поиск по подстроке в названии товара | `?q=телефон` |
| `price_from` | numeric | Минимальная цена | `?price_from=1000` |
| `price_to` | numeric | Максимальная цена | `?price_to=50000` |
| `category_id` | integer | ID категории товара | `?category_id=5` |
| `in_stock` | boolean | Наличие товара на складе (true/false) | `?in_stock=true` |
| `rating_from` | numeric | Минимальный рейтинг (0-5) | `?rating_from=4.5` |

#### Параметры сортировки:

| Параметр | Возможные значения | Описание |
|----------|-------------------|----------|
| `sort` | `price_asc` | Сортировка по возрастанию цены |
| | `price_desc` | Сортировка по убыванию цены |
| | `rating_desc` | Сортировка по убыванию рейтинга |
| | `newest` | Сортировка по дате создания (новые первыми) |

#### Параметры пагинации:

| Параметр | Тип | Описание | Значение по умолчанию |
|----------|-----|----------|----------------------|
| `page` | integer | Номер страницы | 1 |
| `per_page` | integer | Количество товаров на странице | 10 (макс. 100) |

#### Пример запроса:

```bash
GET /api/products?q=телефон&price_from=10000&price_to=50000&category_id=3&in_stock=true&rating_from=4&sort=price_asc&page=2&per_page=20
```

### 2. Получение товара по ID

**Метод:** `GET`  
**Путь:** `/api/products/{id}`

#### Параметры:

| Параметр | Тип | Описание |
|----------|-----|----------|
| `id` | integer | ID товара |

#### Пример запроса:

```bash
GET /api/products/42
```

## Локализация

Поддерживается локализация через заголовок `Accept-Language`:

```bash
Accept-Language: ru  # Русский
Accept-Language: en  # Английский
```

## Структура ответа

### Список товаров (с пагинацией):

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Товар 1",
            "price": "1500.00",
            "category_id": 3,
            "in_stock": true,
            "rating": 4.5,
            "created_at": "2026-02-06T10:00:00.000000Z",
            "updated_at": "2026-02-06T10:00:00.000000Z",
            "category": {
                "id": 3,
                "name": "Электроника"
            }
        }
    ],
    "first_page_url": "http://localhost/api/products?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost/api/products?page=5",
    "next_page_url": "http://localhost/api/products?page=2",
    "path": "http://localhost/api/products",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 50
}
```

### Товар по ID:

```json
{
    "data": {
        "id": 42,
        "name": "Товар 42",
        "price": "2500.00",
        "category_id": 5,
        "in_stock": true,
        "rating": 4.8,
        "created_at": "2026-02-06T10:00:00.000000Z",
        "updated_at": "2026-02-06T10:00:00.000000Z",
        "category": {
            "id": 5,
            "name": "Категория"
        }
    }
}
```

## Обработка ошибок

При ошибках валидации возвращается статус `422`:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "price_from": [
            "The price from must be at least 0."
        ]
    }
}
```

При товаре не найден возвращается статус `404`:

```json
{
    "message": "Product not found",
    "errors": {
        "id": [
            "Product with this ID not found"
        ]
    }
}
```

## Технологии

- PHP 8.2+
- Laravel 12+
- MySQL/PostgreSQL
