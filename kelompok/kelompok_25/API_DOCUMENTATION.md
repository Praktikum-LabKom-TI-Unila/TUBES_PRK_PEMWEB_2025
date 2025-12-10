# API Documentation - Reports & Analytics + Activity Logs

## Base URL
```
http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_25/src/public
```

## Authentication
All API endpoints require authentication. Include session cookie or authentication headers.

---

## Reports & Analytics API

### 1. Inventory Summary
**GET** `/api/reports/inventory`

Returns dashboard summary with key metrics.

**Response:**
```json
{
  "success": true,
  "message": "Inventory summary retrieved successfully",
  "data": {
    "total_materials": 150,
    "total_stock_value": 2500000.00,
    "low_stock_count": 5,
    "out_of_stock_count": 2,
    "recent_stock_in": {
      "quantity": 1000.00,
      "value": 150000.00
    },
    "recent_stock_out": {
      "quantity": 500.00
    }
  }
}
```

### 2. Transaction Summary
**GET** `/api/reports/transactions`

**Parameters:**
- `start_date` (optional): YYYY-MM-DD format, default: first day of current month
- `end_date` (optional): YYYY-MM-DD format, default: today

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_stock_in": {
        "quantity": 5000.00,
        "value": 750000.00,
        "transaction_count": 25
      },
      "total_stock_out": {
        "quantity": 3000.00,
        "transaction_count": 18
      },
      "net_change": 2000.00,
      "by_material": [...],
      "by_supplier": [...]
    },
    "date_range": {
      "start_date": "2024-01-01",
      "end_date": "2024-01-31"
    }
  }
}
```

### 3. Low Stock Alert
**GET** `/api/reports/low-stock`

**Response:**
```json
{
  "success": true,
  "data": {
    "materials": [
      {
        "id": 1,
        "code": "MAT001",
        "name": "Material A",
        "unit": "kg",
        "current_stock": 5.00,
        "min_stock": 10.00,
        "category_name": "Raw Materials"
      }
    ],
    "count": 1
  }
}
```

### 4. Material Trend
**GET** `/api/reports/material-trend/{id}`

**Parameters:**
- `days` (optional): Number of days, default: 30

**Response:**
```json
{
  "success": true,
  "data": {
    "material_id": 1,
    "days": 30,
    "trend_data": [
      {
        "date": "2024-01-01",
        "type": "stock_in",
        "quantity": 100.00
      },
      {
        "date": "2024-01-02",
        "type": "stock_out",
        "quantity": 50.00
      }
    ]
  }
}
```

### 5. Category Distribution
**GET** `/api/reports/category-distribution`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "category_name": "Raw Materials",
      "material_count": 50,
      "total_stock": 1000.00
    }
  ]
}
```

### 6. Supplier Performance
**GET** `/api/reports/supplier-performance`

**Parameters:**
- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format

**Response:**
```json
{
  "success": true,
  "data": {
    "performance": [
      {
        "name": "PT Supplier A",
        "transaction_count": 15,
        "total_quantity": 2000.00,
        "total_value": 300000.00,
        "avg_unit_price": 150.00
      }
    ],
    "date_range": {
      "start_date": "2024-01-01",
      "end_date": "2024-01-31"
    }
  }
}
```

### 7. Stock Movement Detail
**GET** `/api/reports/stock-movement/{id}`

**Parameters:**
- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format

**Response:**
```json
{
  "success": true,
  "data": {
    "material_id": 1,
    "date_range": {
      "start_date": "2024-01-01",
      "end_date": "2024-01-31"
    },
    "movements": [
      {
        "type": "IN",
        "date": "2024-01-15",
        "quantity": 100.00,
        "unit_price": 150.00,
        "total_price": 15000.00,
        "reference_number": "PO001",
        "supplier_name": "PT Supplier A",
        "note": "Regular order",
        "created_at": "2024-01-15 10:30:00"
      }
    ]
  }
}
```

### 8. Top Materials
**GET** `/api/reports/top-materials`

**Parameters:**
- `type` (optional): value|quantity|usage, default: value
- `limit` (optional): Number of results, default: 10

**Response:**
```json
{
  "success": true,
  "data": {
    "type": "value",
    "limit": 10,
    "materials": [
      {
        "id": 1,
        "code": "MAT001",
        "name": "Material A",
        "current_stock": 100.00,
        "unit_price": 150.00,
        "stock_value": 15000.00,
        "total_usage": 50.00
      }
    ]
  }
}
```

### 9. Stock Value by Category
**GET** `/api/reports/stock-value-by-category`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "category_name": "Raw Materials",
      "material_count": 50,
      "total_value": 500000.00
    }
  ]
}
```

---

## Activity Logs API

### 1. Get All Activity Logs
**GET** `/api/activity-logs`

**Parameters:**
- `page` (optional): Page number, default: 1
- `per_page` (optional): Items per page, default: 20, max: 100
- `user_id` (optional): Filter by user ID
- `action` (optional): Filter by action
- `entity_type` (optional): Filter by entity type
- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "action": "CREATE",
        "entity_type": "material",
        "entity_id": 5,
        "description": "Created new material: Material A",
        "ip_address": "127.0.0.1",
        "user_agent": "Mozilla/5.0...",
        "created_at": "2024-01-15 10:30:00",
        "user_name": "Admin User",
        "user_email": "admin@example.com"
      }
    ],
    "total": 150,
    "page": 1,
    "per_page": 20,
    "total_pages": 8
  }
}
```

### 2. Get Activity Logs by User
**GET** `/api/activity-logs/user/{id}`

**Parameters:**
- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format

**Response:**
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "date_range": null,
    "logs": [...],
    "count": 25
  }
}
```

### 3. Get Activity Logs by Action
**GET** `/api/activity-logs/action/{action}`

**Parameters:**
- `start_date` (optional): YYYY-MM-DD format
- `end_date` (optional): YYYY-MM-DD format

**Response:**
```json
{
  "success": true,
  "data": {
    "action": "CREATE",
    "date_range": null,
    "logs": [...],
    "count": 15
  }
}
```

### 4. Get Activity Logs by Entity
**GET** `/api/activity-logs/entity/{type}/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "entity_type": "material",
    "entity_id": 1,
    "logs": [...],
    "count": 8
  }
}
```

### 5. Get Recent Activity Logs
**GET** `/api/activity-logs/recent`

**Parameters:**
- `limit` (optional): Number of results, default: 10, max: 100

**Response:**
```json
{
  "success": true,
  "data": {
    "limit": 10,
    "logs": [...],
    "count": 10
  }
}
```

### 6. Cleanup Old Logs (Admin Only)
**POST** `/api/activity-logs/cleanup`

**Request Body:**
```json
{
  "days": 90
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "days": 90,
    "affected_rows": 150
  }
}
```

---

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE"
}
```

**Common HTTP Status Codes:**
- `200`: Success
- `400`: Bad Request (validation errors)
- `401`: Unauthorized (not logged in)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found
- `500`: Internal Server Error

---

## Activity Log Actions

**Authentication:**
- `LOGIN` - User login
- `LOGOUT` - User logout
- `LOGIN_FAILED` - Failed login attempt

**CRUD Operations:**
- `CREATE` - Create new record
- `UPDATE` - Update existing record
- `DELETE` - Delete record
- `VIEW` - View record/report

**Stock Operations:**
- `STOCK_IN` - Stock in transaction
- `STOCK_OUT` - Stock out transaction
- `ADJUSTMENT` - Stock adjustment

**Data Operations:**
- `EXPORT` - Data export
- `IMPORT` - Data import

**System:**
- `SECURITY` - Security events

---

## Entity Types

- `user` - User management
- `material` - Material operations
- `supplier` - Supplier operations
- `category` - Category operations
- `stock_in` - Stock in transactions
- `stock_out` - Stock out transactions
- `stock_adjustment` - Stock adjustments
- `report` - Report access
- `activity_log` - Activity log operations
- `system` - System operations
- `file` - File operations