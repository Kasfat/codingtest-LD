# Laravel API Documentation,

A RESTful API for user registration, login, authentication, and URL shortening built with Laravel and Sanctum.

## Base URL

```
http://localhost:8000
```

## Authentication

This API uses Laravel Sanctum for token-based authentication. Include the bearer token in the `Authorization` header for protected endpoints:

```
Authorization: Bearer {your-token}
```

---

## API Endpoints

### 1. Register User

Register a new user account.

**Endpoint:** `POST /api/register`

**Authentication:** Not required

**Request Body:**

| Field    | Type   | Required | Description                           |
| -------- | ------ | -------- | ------------------------------------- |
| name     | string | Yes      | User's full name                      |
| email    | string | Yes      | User's email address (must be unique) |
| password | string | Yes      | Password (minimum 8 characters)       |

**Example Request:**

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "test@gmail.com",
    "password": "password123"
  }'
```

**Success Response (201):**

```json
{
    "status": "success",
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Test",
            "email": "test@gmail.com"
        },
        "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456789"
    }
}
```

**Error Response (422):**

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email has already been taken."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

### 2. Login User

Authenticate a user and receive an API token.

**Endpoint:** `POST /api/login`

**Authentication:** Not required

**Request Body:**

| Field    | Type   | Required | Description          |
| -------- | ------ | -------- | -------------------- |
| email    | string | Yes      | User's email address |
| password | string | Yes      | User's password      |

**Example Request:**

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@gmail.com",
    "password": "password123"
  }'
```

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Test",
            "email": "test@gmail.com"
        },
        "token": "2|ZnVjS2V5QXlCbERlRmdISGpKTE1OT3BQclJT"
    }
}
```

**Error Response (401):**

```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

---

### 3. Logout User

Revoke the current API token.

**Endpoint:** `POST /api/logout`

**Authentication:** Required

**Example Request:**

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {your-token}"
```

**Success Response (200):**

```json
{
    "status": "success",
    "message": "Logged out successfully"
}
```

**Error Response (401):**

```json
{
    "message": "Unauthenticated."
}
```

---

### 4. Shorten URL

Create a shortened URL for the given original URL.

**Endpoint:** `POST /api/shorten`

**Authentication:** Required

**Request Body:**

| Field | Type   | Required | Description                                       |
| ----- | ------ | -------- | ------------------------------------------------- |
| url   | string | Yes      | The original URL to shorten (max 2048 characters) |

**Example Request:**

```bash
curl -X POST http://localhost:8000/api/shorten \
  -H "Authorization: Bearer {your-token}" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://example.com/very/long/url/that/needs/to/be/shortened"
  }'
```

**Success Response (201):**

```json
{
    "status": "success",
    "message": "URL shortened successfully",
    "data": {
        "original_url": "https://example.com/very/long/url/that/needs/to/be/shortened",
        "short_code": "aBc123",
        "short_url": "http://localhost:8000/aBc123"
    }
}
```

**Duplicate URL Response (200):**

```json
{
    "status": "success",
    "message": "URL already shortened",
    "data": {
        "original_url": "https://example.com/very/long/url/that/needs/to/be/shortened",
        "short_code": "aBc123",
        "short_url": "http://localhost:8000/aBc123"
    }
}
```

**Error Response (422):**

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "url": ["The url field is required.", "The url format is invalid."]
    }
}
```

**Error Response (401):**

```json
{
    "message": "Unauthenticated."
}
```

---

### 5. Get User URLs

Retrieve all shortened URLs created by the authenticated user.

**Endpoint:** `GET /api/urls`

**Authentication:** Required

**Example Request:**

```bash
curl -X GET http://localhost:8000/api/urls \
  -H "Authorization: Bearer {your-token}"
```

**Success Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "original_url": "https://example.com/very/long/url",
            "short_code": "aBc123",
            "short_url": "http://localhost:8000/aBc123",
            "created_at": "2025-01-15T10:30:00.000000Z"
        },
        {
            "id": 2,
            "original_url": "https://google.com",
            "short_code": "XyZ789",
            "short_url": "http://localhost:8000/XyZ789",
            "created_at": "2025-01-15T11:45:00.000000Z"
        }
    ]
}
```

**Error Response (401):**

```json
{
    "message": "Unauthenticated."
}
```

---

### 6. Redirect to Original URL

Redirect from the short code to the original URL.

**Endpoint:** `GET /{shortCode}`

**Authentication:** Not required

**URL Parameter:**

| Parameter | Type   | Description                |
| --------- | ------ | -------------------------- |
| shortCode | string | The 6-character short code |

**Example Request:**

```bash
curl -X GET http://localhost:8000/aBc123
```

**Success Response (302 Redirect):**

Redirects to the original URL stored in the database.

---

## HTTP Status Codes

| Code | Description                             |
| ---- | --------------------------------------- |
| 200  | Success                                 |
| 201  | Created                                 |
| 302  | Redirect                                |
| 401  | Unauthorized (invalid or missing token) |
| 404  | Not Found                               |
| 422  | Validation Error                        |
| 500  | Server Error                            |

---

## Error Handling

All error responses follow this format:

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["Error message 1", "Error message 2"]
    }
}
```

The API will be available at `http://localhost:8000`.
