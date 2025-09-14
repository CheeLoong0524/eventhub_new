# User Authentication & Management API Documentation

## Overview
This API provides comprehensive user authentication and management services for the EventHub system with both external and internal consumption support.

## Base URL
```
http://your-domain.com/api/v1
```

## Auto-Detection Pattern
All endpoints support auto-detection for external vs internal consumption:
- **External API**: Add `?use_api=true` to URL
- **Internal Service**: Default behavior (no parameter needed)

## Authentication
- **Public Endpoints**: No authentication required
- **Protected Endpoints**: Bearer token authentication

## Endpoints

### 1. Get All Users (XML)
**GET** `/users-xml`

**Query Parameters:**
- `use_api` (boolean): Set to `true` to consume external API, `false` for internal service

**Response Format:** XML

**Example Response:**
```xml
<users>
    <user>
        <user_id>1</user_id>
        <name>John Doe</name>
        <email>john@example.com</email>
        <role>admin</role>
        <auth_method>laravel</auth_method>
        <is_active>1</is_active>
        <phone>+1234567890</phone>
        <address>123 Main St</address>
        <created_at>2024-01-15T10:30:00Z</created_at>
        <last_login_at>2024-01-15T14:30:00Z</last_login_at>
        <vendor>
            <vendor_id>1</vendor_id>
            <business_name>John's Business</business_name>
            <status>approved</status>
        </vendor>
    </user>
</users>
```

### 2. Get User by ID (XML)
**GET** `/users-xml/{id}`

**Path Parameters:**
- `id` (integer): User ID

**Query Parameters:**
- `use_api` (boolean): Set to `true` to consume external API, `false` for internal service

**Response Format:** XML

### 3. Get User Authentication Status (JSON)
**GET** `/users/{id}/auth-status`

**Path Parameters:**
- `id` (integer): User ID

**Query Parameters:**
- `use_api` (boolean): Set to `true` to consume external API, `false` for internal service

**Response Format:** JSON

**Example Response:**
```json
{
    "status": "success",
    "data": {
        "user_id": 1,
        "email": "john@example.com",
        "name": "John Doe",
        "role": "admin",
        "auth_method": "laravel",
        "is_active": true,
        "is_firebase_managed": false,
        "can_change_password": true,
        "last_login_at": "2024-01-15T14:30:00Z",
        "email_verified_at": "2024-01-15T10:30:00Z"
    }
}
```

### 4. Create User (JSON)
**POST** `/users`

**Query Parameters:**
- `use_api` (boolean): Set to `true` to consume external API, `false` for internal service

**Request Body:**
```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "role": "customer",
    "auth_method": "firebase_email",
    "firebase_uid": "firebase_uid_123",
    "phone": "+1234567890",
    "address": "456 Oak St"
}
```

**Response Format:** JSON

### 5. Update User (JSON)
**PUT** `/users/{id}`

**Path Parameters:**
- `id` (integer): User ID

**Query Parameters:**
- `use_api` (boolean): Set to `true` to consume external API, `false` for internal service

**Request Body:**
```json
{
    "name": "Jane Smith",
    "phone": "+0987654321",
    "address": "789 Pine St"
}
```

**Response Format:** JSON

## Error Responses

### XML Error Response
```xml
<error>
    <message>User not found</message>
    <user_id>999</user_id>
    <error>Detailed error message</error>
</error>
```

### JSON Error Response
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## Usage Examples

### Internal Service Consumption (Default)
```php
// In your controller - no parameters needed
$response = Http::get(url('/api/v1/users-xml'));
$xml = simplexml_load_string($response->body());
```

### External API Consumption (Testing)
```php
// In your controller - add use_api parameter
$response = Http::get(url('/api/v1/users-xml?use_api=true'));
$xml = simplexml_load_string($response->body());
```

### Auto-Detection Pattern (Recommended)
```php
// In your controller - auto-detect based on request
$useApi = $request->query('use_api', false);

if ($useApi) {
    // External API consumption (simulate another module)
    $response = Http::timeout(10)
        ->get(url('/api/v1/users-xml'));
    $xml = simplexml_load_string($response->body());
} else {
    // Internal service consumption
    $userService = app(\App\Services\UserService::class);
    $xml = $userService->generateUsersXml();
}
```

## Rate Limiting
- 100 requests per minute per IP
- 1000 requests per hour per authenticated user

## Caching
- User data cached for 60 seconds
- User statistics cached for 5 minutes
- Cache cleared on user updates

## Testing Endpoints

### Test External API Consumption
**GET** `/test-user-api`

### Test Internal Service
**GET** `/test-user-internal`

## Assignment Compliance

### ✅ Web Service Technologies
- **External APIs**: Available for other modules
- **Internal Services**: Fast, reliable local consumption
- **Auto-Detection**: Seamless switching between external/internal
- **Error Handling**: Graceful fallback on API failures

### ✅ Design Patterns
- **Factory Pattern**: UserFactory for user creation
- **Service Pattern**: UserService for internal operations
- **Strategy Pattern**: Auto-detection strategy

### ✅ Secure Coding Practices
- **Input Validation**: Comprehensive validation rules
- **Error Handling**: Secure error responses
- **Rate Limiting**: Protection against abuse
- **Caching**: Performance optimization

### ✅ MVC Architecture
- **Models**: User model with relationships
- **Views**: Blade templates for UI
- **Controllers**: API and web controllers

### ✅ ORM Usage
- **Eloquent ORM**: Used throughout the application
- **Relationships**: Proper model relationships
- **Query Optimization**: Efficient database queries
