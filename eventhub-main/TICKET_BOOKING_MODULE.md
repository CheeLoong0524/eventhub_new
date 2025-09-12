# Ticket Booking and Payment Module

This document describes the implementation of the ticket booking and payment module for the EventHub application, built using the Strategy pattern for event filtering.

## Features Implemented

### 1. Event Management
- **Event Model**: Complete event management with categories, venues, dates, and popularity scoring
- **Ticket Types**: Multiple ticket types per event with different pricing and availability
- **Event Filtering**: Strategy pattern implementation for filtering events by:
  - Category (Technology, Music, Food & Drink, etc.)
  - Date ranges (Today, Tomorrow, This Week, etc.)
  - Popularity (Most Popular, Least Popular, Trending)
  - Venue location

### 2. Shopping Cart System
- **Cart Management**: Persistent cart for both authenticated and guest users
- **Cart Items**: Individual ticket selections with quantity management
- **Cart Validation**: Real-time validation of ticket availability and pricing
- **Session Management**: Cart persistence across browser sessions

### 3. User Interface
- **Event Listing**: Responsive grid layout with filtering and search
- **Event Details**: Comprehensive event information with ticket selection
- **Cart Interface**: Shopping cart with item management and checkout preparation
- **Responsive Design**: Mobile-friendly Bootstrap 5 implementation

## Architecture

### Strategy Pattern Implementation

The filtering system uses the Strategy pattern to provide flexible and extensible event filtering:

```php
// Strategy Interface
interface EventFilterStrategy
{
    public function apply(Builder $query, $value): Builder;
}

// Concrete Strategies
- CategoryFilterStrategy: Filter by event category
- DateFilterStrategy: Filter by date ranges
- PopularityFilterStrategy: Sort by popularity metrics
- VenueFilterStrategy: Filter by venue location
```

### Models

1. **Event**: Main event entity with relationships to ticket types and creator
2. **TicketType**: Individual ticket types with pricing and availability
3. **Cart**: Shopping cart container for users
4. **CartItem**: Individual items in the cart

### Controllers

1. **EventController**: Handles event listing, details, and API endpoints
2. **CartController**: Manages cart operations (add, update, remove, clear)

### Services

1. **EventFilterService**: Orchestrates the Strategy pattern for event filtering

## Database Schema

### Events Table
- `id`, `name`, `description`, `date`, `time`
- `venue`, `location`, `category`, `image_url`
- `status`, `max_attendees`, `created_by`
- `is_featured`, `popularity_score`

### Ticket Types Table
- `id`, `event_id`, `name`, `description`
- `price`, `available_quantity`, `sold_quantity`
- `max_per_order`, `is_active`
- `sale_start_date`, `sale_end_date`

### Carts Table
- `id`, `user_id`, `session_id`, `expires_at`

### Cart Items Table
- `id`, `cart_id`, `event_id`, `ticket_type_id`
- `quantity`, `price`

## API Endpoints

### Public Routes
- `GET /events` - Event listing with filtering
- `GET /events/{event}` - Event details
- `GET /events/api/list` - AJAX event listing
- `GET /events/api/{event}` - AJAX event details
- `GET /events/api/featured` - Featured events
- `GET /events/api/upcoming` - Upcoming events

### Authenticated Routes
- `GET /cart` - View shopping cart
- `POST /cart/add` - Add items to cart
- `PUT /cart/items/{cartItem}` - Update cart item quantity
- `DELETE /cart/items/{cartItem}` - Remove cart item
- `DELETE /cart/clear` - Clear entire cart
- `GET /cart/summary` - Get cart summary

## Usage Examples

### Filtering Events
```php
// Filter by category
$events = $filterService->getFilteredEvents(['category' => 'Technology']);

// Filter by date range
$events = $filterService->getFilteredEvents(['date' => 'this_week']);

// Multiple filters
$events = $filterService->getFilteredEvents([
    'category' => 'Music',
    'popularity' => 'most_popular',
    'venue' => 'Blue Note'
]);
```

### Adding Items to Cart
```javascript
// Add tickets to cart
fetch('/cart/add', {
    method: 'POST',
    body: new FormData(form),
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Update UI
    }
});
```

## Sample Data

The system includes a comprehensive seeder with 8 Malaysian events across different categories:
- Malaysia Tech Summit 2024 (Technology) - Kuala Lumpur Convention Centre
- Jazz Night at The Majestic Hotel (Music) - Kuala Lumpur
- Malaysian Food & Culture Festival (Food & Drink) - Dataran Merdeka, KL
- Penang Marathon Training Workshop (Sports & Fitness) - Batu Ferringhi Beach
- Melaka Art Gallery Opening (Arts & Culture) - Melaka
- Malaysian Startup Pitch Competition (Business) - KLCC Convention Centre
- Port Dickson Wellness Retreat (Wellness) - Lexis Hibiscus Port Dickson
- Comedy Night at Zouk KL (Entertainment) - Kuala Lumpur

Each event includes multiple ticket types with different pricing tiers in Malaysian Ringgit (MYR) and availability across major Malaysian cities.

## Future Enhancements

1. **Payment Integration**: Stripe, PayPal, or other payment gateways
2. **Order Management**: Order history and ticket management
3. **Email Notifications**: Booking confirmations and reminders
4. **Advanced Filtering**: More sophisticated search and filter options
5. **Event Recommendations**: AI-powered event suggestions
6. **Social Features**: Event sharing and reviews
7. **Mobile App**: React Native or Flutter mobile application

## Installation

1. Run migrations: `php artisan migrate`
2. Seed sample data: `php artisan db:seed --class=EventSeeder`
3. Access the application and navigate to `/events`

## Testing

The module includes comprehensive validation and error handling:
- Real-time ticket availability checking
- Cart item validation
- User authentication requirements
- Input validation and sanitization
- Responsive error messages

This implementation provides a solid foundation for a complete event ticketing system with room for future enhancements and scalability.
