# Interface Agreement (IFA) - Vendor Module Web Services

## Overview
The Vendor Module exposes REST API web services for integration with other system modules. These services enable seamless data exchange and functionality sharing across the EventHub platform.

## Web Service Technology Used
- **Protocol**: REST API (JSON-based)
- **Framework**: Laravel API Routes
- **Authentication**: Laravel Sanctum (for protected endpoints)
- **Response Format**: JSON
- **HTTP Methods**: GET, POST, PUT, DELETE

---

## 1. Vendor Information Service

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Retrieves vendor information by vendor ID |
| **Source Module** | Vendor Management |
| **Target Module** | Event Management, Customer Service, Analytics Module |
| **URL** | `GET /api/v1/vendors/{id}` |
| **Function Name** | getVendorInfo |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| id | Integer | Mandatory | Unique ID of the vendor | Must be valid vendor ID |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| data | Object | Mandatory | Vendor information object | Contains vendor details |
| vendor_id | Integer | Mandatory | Unique ID of the vendor | Numeric ID |
| business_name | String | Mandatory | Name of the business | Alphanumeric characters |
| business_type | String | Mandatory | Type of business | Predefined categories |
| contact_email | String | Mandatory | Email of the vendor | Valid email format |
| contact_phone | String | Mandatory | Phone number of the vendor | Phone number format |
| rating | Decimal | Optional | Vendor rating | Decimal number (0.00-5.00) |
| is_verified | Boolean | Mandatory | Verification status | true/false |

---

## 2. Vendor Status Service

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Retrieves vendor status information |
| **Source Module** | Vendor Management |
| **Target Module** | Event Management, Admin Panel |
| **URL** | `GET /api/v1/vendors/{id}/status` |
| **Function Name** | getVendorStatus |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| id | Integer | Mandatory | Unique ID of the vendor | Must be valid vendor ID |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| data | Object | Mandatory | Vendor status object | Contains status information |
| vendor_id | Integer | Mandatory | Unique ID of the vendor | Numeric ID |
| business_name | String | Mandatory | Name of the business | Alphanumeric characters |
| status | String | Mandatory | Current vendor status | "pending", "approved", "rejected", "suspended" |
| is_verified | Boolean | Mandatory | Verification status | true/false |
| approved_at | DateTime | Optional | Approval timestamp | ISO 8601 format |

---

## 3. Vendor Search Service

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Search vendors by various criteria |
| **Source Module** | Vendor Management |
| **Target Module** | Event Management, Customer Service |
| **URL** | `GET /api/v1/vendors/search` |
| **Function Name** | searchVendors |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| query | String | Optional | Search query string | Alphanumeric characters |
| service_type | String | Optional | Type of service | "food", "equipment", "decoration", "entertainment", "logistics", "other" |
| status | String | Optional | Vendor status | "pending", "approved", "rejected", "suspended" |
| page | Integer | Optional | Page number for pagination | Positive integer |
| per_page | Integer | Optional | Number of results per page | 1-100 |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| data | Object | Mandatory | Search results object | Contains vendors and pagination |
| vendors | Array | Mandatory | Array of vendor objects | List of vendor information |
| pagination | Object | Mandatory | Pagination information | Contains page details |

---

## 4. Event Applications Service

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Retrieves event applications for a specific event |
| **Source Module** | Vendor Management |
| **Target Module** | Event Management, Admin Panel |
| **URL** | `GET /api/v1/events/{eventId}/applications` |
| **Function Name** | getEventApplications |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| eventId | Integer | Mandatory | Unique ID of the event | Must be valid event ID |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| data | Object | Mandatory | Applications data object | Contains applications list |
| event_id | Integer | Mandatory | Event ID | Numeric ID |
| applications | Array | Mandatory | Array of application objects | List of application information |
| application_id | Integer | Mandatory | Unique application ID | Numeric ID |
| vendor_id | Integer | Mandatory | Vendor ID | Numeric ID |
| vendor_name | String | Mandatory | Business name | Alphanumeric characters |
| booth_size | String | Mandatory | Size of the booth | "10x10", "20x20", "30x30" |
| status | String | Mandatory | Application status | "pending", "approved", "rejected", "paid", "cancelled" |

---

## 5. Event Application Submission Service

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Submit event application for a vendor |
| **Source Module** | Vendor Management |
| **Target Module** | Event Management |
| **URL** | `POST /api/v1/events/{eventId}/apply` |
| **Function Name** | submitEventApplication |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| eventId | Integer | Mandatory | Unique ID of the event | Must be valid event ID |
| vendor_id | Integer | Mandatory | Unique ID of the vendor | Must be valid vendor ID |
| booth_size | String | Mandatory | Size of the booth | "10x10", "20x20", "30x30" |
| booth_quantity | Integer | Mandatory | Number of booths | 1-10 |
| service_type | String | Mandatory | Type of service | "food", "equipment", "decoration", "entertainment", "logistics", "other" |
| service_description | String | Mandatory | Description of services | Max 1000 characters |
| requested_price | Decimal | Mandatory | Requested price | Positive number |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| message | String | Mandatory | Response message | Descriptive text |
| data | Object | Mandatory | Application data object | Contains application details |
| application_id | Integer | Mandatory | Unique application ID | Numeric ID |
| vendor_id | Integer | Mandatory | Vendor ID | Numeric ID |
| event_id | Integer | Mandatory | Event ID | Numeric ID |
| status | String | Mandatory | Application status | "pending" |
| submitted_at | DateTime | Mandatory | Submission timestamp | ISO 8601 format |

---

## 6. Vendor Management Service (Admin)

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Get all vendors for admin management |
| **Source Module** | Vendor Management |
| **Target Module** | Admin Panel |
| **URL** | `GET /api/v1/admin/vendors` |
| **Function Name** | getAllVendors |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Optional | Filter by vendor status | "pending", "approved", "rejected", "suspended" |
| service_type | String | Optional | Filter by service type | "food", "equipment", "decoration", "entertainment", "logistics", "other" |
| search | String | Optional | Search query | Alphanumeric characters |
| page | Integer | Optional | Page number | Positive integer |
| per_page | Integer | Optional | Results per page | 1-100 |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| data | Object | Mandatory | Vendors data object | Contains vendors and pagination |
| vendors | Array | Mandatory | Array of vendor objects | List of vendor information |
| pagination | Object | Mandatory | Pagination information | Contains page details |

---

## 7. Vendor Approval Service (Admin)

### Webservice Mechanism
| Field | Value |
|-------|-------|
| **Protocol** | RESTFUL |
| **Function** | Approve vendor application |
| **Source Module** | Vendor Management |
| **Target Module** | Admin Panel |
| **URL** | `POST /api/v1/admin/vendors/{id}/approve` |
| **Function Name** | approveVendor |

### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| id | Integer | Mandatory | Unique ID of the vendor | Must be valid vendor ID |
| admin_notes | String | Optional | Admin notes | Max 1000 characters |

### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| status | String | Mandatory | Status of the request | "success" or "error" |
| message | String | Mandatory | Response message | Descriptive text |
| data | Object | Mandatory | Approval data object | Contains approval details |
| vendor_id | Integer | Mandatory | Vendor ID | Numeric ID |
| business_name | String | Mandatory | Business name | Alphanumeric characters |
| status | String | Mandatory | New status | "approved" |
| approved_at | DateTime | Mandatory | Approval timestamp | ISO 8601 format |

---

## Error Response Format

All services return consistent error responses:

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

## Authentication

- **Public Endpoints**: No authentication required
- **Protected Endpoints**: Require Bearer token authentication
- **Admin Endpoints**: Require admin role authentication

## Rate Limiting

- **Public APIs**: 100 requests per minute per IP
- **Authenticated APIs**: 1000 requests per minute per user
- **Admin APIs**: 2000 requests per minute per admin user

## Base URL

- **Development**: `http://localhost:8000/api/v1`
- **Production**: `https://yourdomain.com/api/v1`
