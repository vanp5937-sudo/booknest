# ERD – BookNest

```mermaid
erDiagram
    USERS ||--o{ ORDERS : places
    CATEGORIES ||--o{ BOOKS : contains
    ORDERS ||--|{ ORDER_ITEMS : includes
    BOOKS o|--o{ ORDER_ITEMS : referenced_by

    USERS {
        bigint id PK
        string name
        string email UK
        string role
        string password
    }
    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        boolean is_active
    }
    BOOKS {
        bigint id PK
        bigint category_id FK
        string sku UK
        string title
        string slug UK
        decimal price
        int stock
    }
    ORDERS {
        bigint id PK
        bigint user_id FK
        string order_code UK
        decimal subtotal
        decimal total
        string status
    }
    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint book_id FK
        string book_title
        int quantity
        decimal unit_price
        decimal line_total
    }
```

`orders.user_id` cho phép NULL để lưu đơn khách vãng lai nếu nghiệp vụ cho phép; trong luồng checkout hiện tại người dùng cần đăng nhập. `order_items.book_id` cho phép NULL để giữ lịch sử đơn nếu sách bị xóa.
