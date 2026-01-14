# Architecture

## Overview

This document describes the architecture of the E-commerce API built with Laravel, following a **DDD Hybrid** (Hybrid Domain-Driven Design) pattern that combines DDD principles with Laravel's pragmatic structure.

## Directory Structure

The project follows a hybrid structure, combining Laravel's standard structure with DDD (Domain-Driven Design) principles:

```
app/
├── Domain/              # Domain Layer (Business Logic)
│   ├── Exceptions/      # Domain Exceptions
│   ├── ValueObjects/    # Value Objects
│   ├── Services/        # Domain Services
│   ├── Repositories/    # Repository Interfaces
│   └── Events/          # Domain Events
│
├── Application/        # Application Layer (Use Cases)
│   ├── Services/       # Application Services
│   └── UseCases/       # Application Use Cases
│
├── Infrastructure/      # Infrastructure Layer (External Concerns)
│   ├── Persistence/    # Repository Implementations
│   └── Providers/      # Service Providers
│
├── Http/               # Presentation Layer (HTTP/API)
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
│
└── Models/            # Domain Entities (Eloquent Models)
```

## Design Patterns

- **Repository Pattern**: Used for data access abstraction
- **Use Case Pattern**: Application layer orchestration
- **Observer Pattern**: Event handling (Domain Events)
- **DDD Hybrid**: Models as domain entities, Use Cases decoupled from infrastructure
- **Dependency Inversion**: Use Cases depend only on Domain interfaces
- **Exception Handling**: Global exception handler for Domain Exceptions

> **Note:** For complete details about the DDD Hybrid pattern, see the project's architectural documentation.

## Current Implementation Status

### ✅ Implemented (Module 1)

- **Authentication**: Register, Login, Password Reset
- **RBAC**: Roles, Permissions, Middleware
- **Email Notifications**: Welcome email, Password reset email
- **Domain Exceptions**: 5 custom exceptions with global handler
- **Time-based Protection**: Password reset has 5-minute interval between requests (implemented in Use Case)
- **Repository Pattern**: All data access through repositories
- **Use Cases**: 8 use cases for authentication and authorization
- **Event-Driven**: Domain events for user registration and password reset

### ✅ Implemented (Module 2)

- **Products**: Full CRUD with advanced filtering, pagination, and sorting
- **Categories**: Full CRUD with parent-child relationships
- **Filtering**: By category, price range, and text search
- **Pagination**: Configurable per_page and page parameters
- **Sorting**: Multiple fields with ascending/descending support
- **Domain Exceptions**: ProductNotFoundException, CategoryNotFoundException
- **Use Cases**: 9 use cases for products and categories
- **Eager Loading**: Optimized queries to prevent N+1 problems
- **Factories & Seeders**: ProductFactory, CategoryFactory, and seeders for test data

### ✅ Implemented (Module 3)

- **Cart**: Full shopping cart management (add, remove, clear, get)
- **Cart Items**: Automatic quantity updates when adding same product
- **Stock Validation**: Prevents adding more items than available stock
- **Price Snapshot**: Stores product price at time of addition (price_at_time)
- **Total Calculation**: Automatic cart total calculation
- **Domain Exceptions**: CartNotFoundException, CartItemNotFoundException, InsufficientStockException
- **Domain Service**: CartService for complex business logic
- **Use Cases**: 4 use cases for cart operations
- **One Cart Per User**: Automatic cart creation and retrieval
- **Factories**: CartFactory, CartItemFactory for testing

## Technology Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.3+
- **Database**: PostgreSQL 16
- **Cache**: Redis 7.2
- **Queue**: Redis / RabbitMQ
- **Authentication**: Laravel Sanctum (JWT)

## API Design

### Principles

- **RESTful endpoints**: Following REST conventions
- **JSON responses**: All responses in JSON format
- **Status codes**: Proper HTTP status codes (200, 201, 400, 401, 404, 422, 500)
- **Error handling**: Global exception handler for Domain Exceptions
- **Time-based Protection**: Password reset has 5-minute interval between requests (prevents spam while allowing legitimate use)

### Current Endpoints

#### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Authenticate user
- `POST /api/auth/forgot-password` - Request password reset (rate limited)
- `POST /api/auth/reset-password` - Reset password with token
- `GET /api/auth/me` - Get authenticated user (protected)

#### Roles & Permissions
- `GET /api/roles` - List all roles (protected)
- `POST /api/roles/assign-to-user` - Assign role to user (protected)
- `POST /api/roles/remove-from-user` - Remove role from user (protected)
- `POST /api/roles/assign-permission` - Assign permission to role (protected)
- `POST /api/roles/remove-permission` - Remove permission from role (protected)

#### Products
- `GET /api/products` - List products with filters (public)
- `GET /api/products/{id}` - Get product by ID (public)
- `POST /api/products` - Create product (protected)
- `PUT /api/products/{id}` - Update product (protected)
- `DELETE /api/products/{id}` - Delete product (protected)

#### Categories
- `GET /api/categories` - List all categories (public)
- `GET /api/categories/{id}` - Get category by ID (public)
- `POST /api/categories` - Create category (protected)
- `PUT /api/categories/{id}` - Update category (protected)
- `DELETE /api/categories/{id}` - Delete category (protected)

#### Cart
- `GET /api/cart` - Get user's shopping cart (protected)
- `POST /api/cart/add` - Add product to cart (protected)
- `DELETE /api/cart/items/{id}` - Remove item from cart (protected)
- `POST /api/cart/clear` - Clear all items from cart (protected)

#### Health Check
- `GET /api/health` - API health status

## Database Schema

Document your database schema and relationships here.

## Security

### Implemented Security Measures

- **Authentication**: Laravel Sanctum (API tokens)
- **Authorization**: RBAC with roles and permissions
- **Input Validation**: Form Requests for all endpoints
- **Time-based Protection**: Password reset has 5-minute interval between requests (prevents spam)
- **Password Hashing**: Bcrypt via Laravel's Hash facade
- **SQL Injection Protection**: Eloquent ORM with parameter binding
- **XSS Protection**: Laravel's built-in protection
- **CSRF Protection**: Enabled for web routes

### Authentication Flow

1. User registers → receives API token
2. User logs in → receives API token
3. Token included in `Authorization: Bearer {token}` header
4. Protected routes require valid token via `auth:sanctum` middleware

### Authorization Flow

1. User has roles (e.g., admin, user, moderator)
2. Roles have permissions (e.g., products.manage, orders.view)
3. Middleware checks: `role:admin` or `permission:products.manage`
4. Access granted/denied based on user's roles and permissions

## Performance Considerations

Document performance optimizations:

- Caching strategies
- Query optimization
- Eager loading
- Index usage

## Deployment

Document deployment architecture:

- Containerization
- Scaling strategy
- Health checks
- Monitoring

