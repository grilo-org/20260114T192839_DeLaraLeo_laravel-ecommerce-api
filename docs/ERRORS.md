# Error Codes

> **Note**: This is a template file. Each service should document its specific error codes here.

## Overview

This document lists all error codes and their meanings for this microservice.

## HTTP Status Codes

### 2xx Success

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `204 No Content` - Request successful, no content to return

### 4xx Client Errors

- `400 Bad Request` - Invalid request format or parameters
- `401 Unauthorized` - Authentication required or invalid credentials
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `429 Too Many Requests` - Rate limit exceeded

### 5xx Server Errors

- `500 Internal Server Error` - Unexpected server error
- `503 Service Unavailable` - Service temporarily unavailable

## Domain Exceptions

The project uses custom Domain Exceptions that are handled globally. All exceptions return JSON responses with appropriate HTTP status codes.

### Implemented Domain Exceptions

| Exception | HTTP Status | Description |
|-----------|-------------|-------------|
| `UserNotFoundException` | 404 | User not found by ID or email |
| `RoleNotFoundException` | 404 | Role not found by slug or ID |
| `PermissionNotFoundException` | 404 | Permission not found by slug or ID |
| `ProductNotFoundException` | 404 | Product not found by ID or slug |
| `CategoryNotFoundException` | 404 | Category not found by ID |
| `CartNotFoundException` | 404 | Cart not found by ID or user ID |
| `CartItemNotFoundException` | 422 | Cart item not found by ID |
| `InsufficientStockException` | 422 | Requested quantity exceeds available stock |
| `InvalidTokenException` | 400 | Invalid or malformed token |
| `TokenExpiredException` | 400 | Token has expired |

### Exception Examples

#### UserNotFoundException

**When thrown:**
- User not found by ID in `AssignRoleToUserUseCase`
- User not found by email in authentication flows

**Response (404):**
```json
{
  "message": "User with ID '123' not found."
}
```

#### RoleNotFoundException

**When thrown:**
- Role not found by slug when assigning to user
- Role not found when assigning permissions

**Response (404):**
```json
{
  "message": "Role with slug 'admin' not found."
}
```

#### PermissionNotFoundException

**When thrown:**
- Permission not found by slug when assigning to role

**Response (404):**
```json
{
  "message": "Permission with slug 'products.manage' not found."
}
```

#### InvalidTokenException

**When thrown:**
- Password reset token is invalid or malformed

**Response (400):**
```json
{
  "message": "Invalid or expired token."
}
```

#### TokenExpiredException

**When thrown:**
- Password reset token has expired

**Response (400):**
```json
{
  "message": "Token has expired."
}
```

#### ProductNotFoundException

**When thrown:**
- Product not found by ID when fetching or updating
- Product not found by slug

**Response (404):**
```json
{
  "message": "Product with ID '123' not found."
}
```

#### CategoryNotFoundException

**When thrown:**
- Category not found by ID when creating or updating products

**Response (404):**
```json
{
  "message": "Category with ID '123' not found."
}
```

#### CartNotFoundException

**When thrown:**
- Cart not found by ID or user ID

**Response (404):**
```json
{
  "message": "Cart for user ID '123' not found."
}
```

#### CartItemNotFoundException

**When thrown:**
- Cart item not found by ID when trying to remove
- Cart item does not belong to user's cart

**Response (422):**
```json
{
  "message": "Cart item with ID '123' not found."
}
```

#### InsufficientStockException

**When thrown:**
- User tries to add more items to cart than available in stock
- Total quantity in cart would exceed product stock

**Response (422):**
```json
{
  "message": "Insufficient stock. Requested: 11, Available: 10."
}
```

### Authentication Errors

| HTTP Status | Message | Description |
|-------------|---------|-------------|
| `401` | Invalid credentials | Email or password incorrect |
| `401` | Unauthenticated | Token missing or invalid |

### Validation Errors

| HTTP Status | Format | Description |
|-------------|--------|-------------|
| `422` | Laravel validation errors | Form Request validation failed |

**Example (422):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

## Error Response Format

### Domain Exceptions (404, 400)

Domain exceptions return a simple message format:

```json
{
  "message": "User with ID '123' not found."
}
```

### Validation Errors (422)

Validation errors follow Laravel's standard format:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Authentication Errors (401)

```json
{
  "message": "Invalid credentials."
}
```

or

```json
{
  "message": "Unauthenticated."
}
```

## Handling Errors

### Global Exception Handler

All Domain Exceptions are handled globally in `bootstrap/app.php`:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (UserNotFoundException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    });
    // ... other exceptions
})
```

### Error Handling Guidelines

1. **Client Errors (4xx)**: Fix the request and retry
   - 400: Bad Request - Check request format
   - 401: Unauthorized - Provide valid authentication token
   - 404: Not Found - Resource doesn't exist
   - 422: Validation Error - Fix validation errors and retry

2. **Server Errors (5xx)**: Retry with exponential backoff
   - 500: Internal Server Error - Server-side issue, retry later

3. **Time-based Protection**: Password reset has 5-minute interval between requests
   - Implemented in `RequestPasswordResetUseCase`
   - Prevents spam while allowing legitimate use
   - Returns success message even when rate-limited (prevents email enumeration)

## Logging

Errors are logged with the following information:

- Error code
- Error message
- Stack trace (for 5xx errors)
- Request context
- User information (if authenticated)

## Troubleshooting

Common error scenarios and solutions:

### Database Connection Errors

**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solution**: Check database configuration in `.env` and ensure PostgreSQL is running.

### Redis Connection Errors

**Error**: `Connection refused`

**Solution**: Verify Redis is running and check `REDIS_HOST` and `REDIS_PORT` in `.env`.

### Authentication Errors

**Error**: `401 Unauthorized`

**Solution**: Ensure valid JWT token is provided in Authorization header.

