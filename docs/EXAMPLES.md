# Code Examples

> **Note**: This is a template file. Each service should provide code examples here.

## Overview

This document provides code examples for common use cases in this microservice.

## Authentication

### Register Example

```bash
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "email_verified_at": null,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Login Example

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "email_verified_at": null,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

**Error Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials."
}
```

**Error Response (404 Not Found - Domain Exception):**
```json
{
  "message": "User with ID '999' not found."
}
```

**Error Response (400 Bad Request - Domain Exception):**
```json
{
  "message": "Invalid or expired token."
}
```

### Forgot Password Example

```bash
POST /api/auth/forgot-password
Content-Type: application/json

{
  "email": "user@example.com"
}
```

**Response (200 OK):**
```json
{
  "message": "If the email exists, a password reset link has been sent."
}
```

### Reset Password Example

```bash
POST /api/auth/reset-password
Content-Type: application/json

{
  "email": "user@example.com",
  "token": "reset-token-from-email",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Response (200 OK):**
```json
{
  "message": "Password has been reset successfully."
}
```

**Response (200 OK):**
```json
{
  "message": "Password has been reset successfully."
}
```

**Error Response (400 Bad Request - Invalid Token):**
```json
{
  "message": "Invalid or expired token."
}
```

**Error Response (400 Bad Request - Generic):**
```json
{
  "message": "Unable to reset password. Please check your token and try again."
}
```

### Using Authentication Token

```bash
Authorization: Bearer 1|abc123...
```

Include the token in the Authorization header for protected endpoints.

### Get Current User

```bash
GET /api/auth/me
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "user@example.com",
  "email_verified_at": null,
  "roles": [
    {
      "id": 1,
      "name": "User",
      "slug": "user"
    }
  ],
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T00:00:00+00:00"
}
```

## Roles and Permissions (RBAC)

### List Roles

```bash
GET /api/roles
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "name": "Administrator",
    "slug": "admin",
    "description": "Full system access",
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
]
```

### Assign Role to User

```bash
POST /api/roles/assign-to-user
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "user_id": 2,
  "role_slug": "admin"
}
```

**Response (200 OK):**
```json
{
  "message": "Role assigned successfully."
}
```

**Error Response (404 Not Found - User):**
```json
{
  "message": "User with ID '999' not found."
}
```

**Error Response (404 Not Found - Role):**
```json
{
  "message": "Role with slug 'invalid-role' not found."
}
```

### Remove Role from User

```bash
POST /api/roles/remove-from-user
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "user_id": 2,
  "role_slug": "admin"
}
```

**Response (200 OK):**
```json
{
  "message": "Role removed successfully."
}
```

### Assign Permission to Role

```bash
POST /api/roles/assign-permission
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "role_slug": "admin",
  "permission_slug": "products.manage"
}
```

**Response (200 OK):**
```json
{
  "message": "Permission assigned to role successfully."
}
```

**Error Response (404 Not Found - Permission):**
```json
{
  "message": "Permission with slug 'invalid.permission' not found."
}
```

### Remove Permission from Role

```bash
POST /api/roles/remove-permission
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "role_slug": "admin",
  "permission_slug": "products.manage"
}
```

**Response (200 OK):**
```json
{
  "message": "Permission removed from role successfully."
}
```

---

## Products

### List Products Example

```bash
GET /api/products?category=1&min_price=10&max_price=100&search=phone&page=1&per_page=15&sort=-price,name
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Smartphone",
      "slug": "smartphone",
      "description": "Latest smartphone model",
      "price": 599.99,
      "stock": 10,
      "image_url": "https://example.com/image.jpg",
      "category": {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics"
      },
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": "2024-01-01T00:00:00+00:00"
    }
  ],
  "links": {
    "first": "http://localhost/api/products?page=1",
    "last": "http://localhost/api/products?page=10",
    "prev": null,
    "next": "http://localhost/api/products?page=2"
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

### Get Product Example

```bash
GET /api/products/1
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Smartphone",
    "slug": "smartphone",
    "description": "Latest smartphone model",
    "price": 599.99,
    "stock": 10,
    "image_url": "https://example.com/image.jpg",
    "category": {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    },
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
}
```

### Create Product Example

```bash
POST /api/products
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "name": "Laptop",
  "slug": "laptop",
  "description": "High-performance laptop",
  "price": 1299.99,
  "stock": 5,
  "category_id": 1,
  "image_url": "https://example.com/laptop.jpg"
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 2,
    "name": "Laptop",
    "slug": "laptop",
    "description": "High-performance laptop",
    "price": 1299.99,
    "stock": 5,
    "image_url": "https://example.com/laptop.jpg",
    "category": {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    },
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
}
```

**Error Response (422 Validation Error):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The name field is required."],
    "price": ["The price must be at least 0."],
    "category_id": ["The selected category does not exist."]
  }
}
```

**Error Response (404 Not Found - Category):**
```json
{
  "message": "Category with ID '999' not found."
}
```

### Update Product Example

```bash
PUT /api/products/1
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "name": "Updated Smartphone",
  "price": 699.99,
  "stock": 15
}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Updated Smartphone",
    "slug": "smartphone",
    "price": 699.99,
    "stock": 15,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T01:00:00+00:00"
  }
}
```

### Delete Product Example

```bash
DELETE /api/products/1
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "message": "Product deleted successfully."
}
```

**Error Response (404 Not Found):**
```json
{
  "message": "Product with ID '999' not found."
}
```

---

## Categories

### List Categories Example

```bash
GET /api/categories
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "description": "Electronic devices and gadgets",
      "parent_id": null,
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": "2024-01-01T00:00:00+00:00"
    },
    {
      "id": 2,
      "name": "Smartphones",
      "slug": "smartphones",
      "description": "Mobile phones and smartphones",
      "parent_id": 1,
      "parent": {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics"
      },
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": "2024-01-01T00:00:00+00:00"
    }
  ]
}
```

### Get Category Example

```bash
GET /api/categories/1
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Electronics",
    "slug": "electronics",
    "description": "Electronic devices and gadgets",
    "parent_id": null,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
}
```

### Create Category Example

```bash
POST /api/categories
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "name": "Clothing",
  "slug": "clothing",
  "description": "Apparel and fashion items",
  "parent_id": null
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 3,
    "name": "Clothing",
    "slug": "clothing",
    "description": "Apparel and fashion items",
    "parent_id": null,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
}
```

### Create Subcategory Example

```bash
POST /api/categories
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "name": "T-Shirts",
  "slug": "t-shirts",
  "description": "T-shirts and casual wear",
  "parent_id": 3
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 4,
    "name": "T-Shirts",
    "slug": "t-shirts",
    "description": "T-shirts and casual wear",
    "parent_id": 3,
    "parent": {
      "id": 3,
      "name": "Clothing",
      "slug": "clothing"
    },
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
}
```

### Update Category Example

```bash
PUT /api/categories/1
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "name": "Updated Electronics",
  "description": "Updated description"
}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Updated Electronics",
    "slug": "electronics",
    "description": "Updated description",
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T01:00:00+00:00"
  }
}
```

### Delete Category Example

```bash
DELETE /api/categories/1
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "message": "Category deleted successfully."
}
```

**Error Response (404 Not Found):**
```json
{
  "message": "Category with ID '999' not found."
}
```

### Using Middleware

You can protect routes using role or permission middleware:

```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Routes for admin only
});

Route::middleware(['auth:sanctum', 'permission:products.manage'])->group(function () {
    // Routes requiring specific permission
});
```

## API Requests

### Health Check

```bash
curl -X GET http://localhost:8000/api/health
```

### Creating a Resource

```php
// POST /api/resources
{
  "name": "Example Resource",
  "description": "This is an example"
}

// Response
{
  "id": 1,
  "name": "Example Resource",
  "description": "This is an example",
  "created_at": "2024-01-01T00:00:00.000000Z"
}
```

### Updating a Resource

```php
// PUT /api/resources/1
{
  "name": "Updated Resource",
  "description": "Updated description"
}
```

### Deleting a Resource

```php
// DELETE /api/resources/1
// Response: 204 No Content
```

## Database Queries

### Using Eloquent

```php
use App\Models\User;

// Find user
$user = User::find(1);

// Create user
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
]);

// Query with relationships
$users = User::with('posts')->get();
```

### Using Query Builder

```php
use Illuminate\Support\Facades\DB;

$users = DB::table('users')
    ->where('active', true)
    ->orderBy('name')
    ->get();
```

## Caching

### Cache a Value

```php
use Illuminate\Support\Facades\Cache;

Cache::put('key', 'value', 3600); // Cache for 1 hour
```

### Retrieve from Cache

```php
$value = Cache::get('key', 'default');
```

### Cache with Remember

```php
$users = Cache::remember('users', 3600, function () {
    return User::all();
});
```

## Queue Jobs

### Dispatch a Job

```php
use App\Jobs\ProcessOrder;

ProcessOrder::dispatch($order);
```

### Create a Job

```php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public $order)
    {
        //
    }

    public function handle(): void
    {
        // Process the order
    }
}
```

## Events and Listeners

### Dispatch an Event

```php
use App\Events\OrderCreated;

event(new OrderCreated($order));
```

### Create an Event Listener

```php
namespace App\Listeners;

use App\Events\OrderCreated;

class SendOrderConfirmation
{
    public function handle(OrderCreated $event): void
    {
        // Send confirmation email
    }
}
```

## Validation

### Form Request Validation

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ];
    }
}
```

## Cart

### Get Cart

```bash
GET /api/cart
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "items": [
    {
      "id": 1,
      "quantity": 2,
      "price_at_time": 99.99,
      "subtotal": 199.98,
      "product": {
        "id": 1,
        "name": "Laptop",
        "slug": "laptop",
        "price": 99.99,
        "stock": 10,
        "category": {
          "id": 1,
          "name": "Electronics",
          "slug": "electronics"
        }
      },
      "created_at": "2024-01-01T00:00:00+00:00",
      "updated_at": "2024-01-01T00:00:00+00:00"
    }
  ],
  "total": 199.98,
  "items_count": 1,
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T00:00:00+00:00"
}
```

### Add to Cart

```bash
POST /api/cart/add
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "items": [
    {
      "id": 1,
      "quantity": 2,
      "price_at_time": 99.99,
      "subtotal": 199.98,
      "product": {
        "id": 1,
        "name": "Laptop",
        "price": 99.99
      }
    }
  ],
  "total": 199.98
}
```

**Error Response (422 Unprocessable Entity - Insufficient Stock):**
```json
{
  "message": "Insufficient stock. Requested: 11, Available: 10."
}
```

**Error Response (422 Unprocessable Entity - Validation):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "product_id": ["The selected product does not exist."],
    "quantity": ["The quantity must be at least 1."]
  }
}
```

### Remove from Cart

```bash
DELETE /api/cart/items/1
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "items": [],
  "total": 0,
  "items_count": 0
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "message": "Cart item with ID '999' not found."
}
```

### Clear Cart

```bash
POST /api/cart/clear
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "items": [],
  "total": 0,
  "items_count": 0
}
```

## Orders

### Create Order Example

```bash
POST /api/orders
Authorization: Bearer 1|abc123...
```

**Response (201 Created):**
```json
{
  "id": 1,
  "user_id": 1,
  "status": "pending",
  "total": 199.98,
  "items": [
    {
      "id": 1,
      "order_id": 1,
      "product_id": 1,
      "quantity": 2,
      "price_at_time": 99.99,
      "subtotal": 199.98,
      "product": {
        "id": 1,
        "name": "Laptop",
        "slug": "laptop"
      }
    }
  ],
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T00:00:00+00:00"
}
```

**Error Response (422 Unprocessable Entity - Empty Cart):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "cart": ["Cart is empty."]
  }
}
```

### List Orders Example

```bash
GET /api/orders
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "status": "pending",
    "total": 199.98,
    "created_at": "2024-01-01T00:00:00+00:00",
    "updated_at": "2024-01-01T00:00:00+00:00"
  }
]
```

### Get Order Example

```bash
GET /api/orders/1
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "status": "pending",
  "total": 199.98,
  "items": [
    {
      "id": 1,
      "order_id": 1,
      "product_id": 1,
      "quantity": 2,
      "price_at_time": 99.99,
      "subtotal": 199.98
    }
  ],
  "payment": null,
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T00:00:00+00:00"
}
```

### Update Order Status Example

```bash
PATCH /api/orders/1/status
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "status": "processing"
}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "status": "processing",
  "total": 199.98,
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T01:00:00+00:00"
}
```

### Cancel Order Example

```bash
DELETE /api/orders/1
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "status": "cancelled",
  "total": 199.98,
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T01:00:00+00:00"
}
```

**Error Response (422 Unprocessable Entity - Cannot Cancel):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "order": ["Cannot cancel order that has been delivered or refunded."]
  }
}
```

## Payments

### Process Payment Example

```bash
POST /api/orders/1/payment
Authorization: Bearer 1|abc123...
Content-Type: application/json

{
  "amount": 199.98,
  "payment_method": "credit_card"
}
```

**Response (201 Created):**
```json
{
  "id": 1,
  "order_id": 1,
  "amount": 199.98,
  "status": "paid",
  "payment_method": "credit_card",
  "transaction_id": "mock_abc123",
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T00:00:00+00:00"
}
```

**Error Response (422 Unprocessable Entity - Wrong Amount):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "amount": ["Payment amount does not match order total."]
  }
}
```

**Error Response (422 Unprocessable Entity - Already Paid):**
```json
{
  "message": "Order with ID '1' has already been paid."
}
```

### Get Payment Example

```bash
GET /api/orders/1/payment
Authorization: Bearer 1|abc123...
```

**Response (200 OK):**
```json
{
  "id": 1,
  "order_id": 1,
  "amount": 199.98,
  "status": "paid",
  "payment_method": "credit_card",
  "transaction_id": "mock_abc123",
  "created_at": "2024-01-01T00:00:00+00:00",
  "updated_at": "2024-01-01T00:00:00+00:00"
}
```

**Error Response (404 Not Found):**
```json
{
  "message": "Payment not found for order with ID '1'."
}
```

## Testing

### Feature Test Example

```php
namespace Tests\Feature;

use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_can_create_user(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
            ]);
    }
}
```

## Service Classes

### Example Service

```php
namespace App\Services;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
```

