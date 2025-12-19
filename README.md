## 🔐 Authentication & Authorization

This API uses **Laravel Sanctum** for stateless, token-based authentication.

### Authentication
- Users authenticate using email and password
- A Bearer token is issued upon successful login
- All protected endpoints require a valid Sanctum token

### Authorization
Authorization is enforced using **Laravel Policies**.

Role-based access control:
- **Admin**: manage categories and events
- **User**: browse events, book and cancel bookings
- **Guest**: read-only access

All authorization failures return structured JSON responses.

---

## 🧪 Demo Credentials

### Admin
- Email: `admin@example.com`
- Password: `password`

### User
- Email: `user@example.com`
- Password: `password`

---

## ❗ Error Handling

The API returns consistent, predictable error responses for all failure cases.

### Error Responses
| Status | Description |
|------|------------|
| **401** | Unauthenticated (missing or invalid token) |
| **403** | Forbidden (insufficient permissions) |
| **404** | Resource or endpoint not found |
| **422** | Validation error |
| **500** | Internal server error |

### Example Error Response
```json
{
  "success": false,
  "message": "Unauthenticated. Please login.",
  "error": "unauthenticated"
}
