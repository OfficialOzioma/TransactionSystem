# Transaction Processing System

A secure Laravel-based transaction processing system that handles concurrent operations with data integrity.

## Features

- Secure REST API endpoints for transactions and balance inquiries
- Concurrent transaction handling with database locks
- Authentication using Laravel Sanctum
- Comprehensive test suite

## Requirements

- PHP >= 8.1
- Composer
- MySQL/sqlite
- Laravel 11.x

## Installation

1. Clone the repository:
```bash
git clone git@github.com:OfficialOzioma/TransactionSystem.git
cd transaction-system
```

2. Install dependencies:
```bash
composer install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transaction_system
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations:
```bash
php artisan migrate
```

6. Generate API token:
```bash
php artisan sanctum:install
```

## API Endpoints

### Register as user
```
POST /api/v1/register
Body:
{
    "name":"John Doe",
    "email": "john@example.com",
    "password":"password",
    "password_confirmation": "password"
}
```

### Login
```
POST /api/v1/login
Body:
{
    "email": "john@example.com",
    "password":"password"
}
```

### Create Transaction
```
POST /api/v1/transaction
Headers:
  - Authorization: Bearer {token}
Body:
{
    "amount": 100.00,
    "type": "credit" // or "debit"
}
```

### Get Balance
```
GET /api/v1/balance
Headers:
  - Authorization: Bearer {token}
```

### Logout
```
POST /api/v1/logout
Headers:
  - Authorization: Bearer {token}
```


## Testing

Run the test suite:
```bash
php artisan test
```

## Scaling Considerations

1. **Horizontal Scaling**:
   - Deploy multiple application servers behind a load balancer
   - Use read replicas for balance queries
   - Implement caching layer with Redis/Memcached

2. **Performance Optimizations**:
   - Add indexes for frequently queried fields
   - Implement request rate limiting
   - Cache frequent balance queries

3. **Monitoring & Logging**:
   - Implement comprehensive logging
   - Set up monitoring for transaction failures
   - Add performance metrics tracking

4. **Security Enhancements**:
   - Implement 2FA for sensitive operations
   - Add request signing for API calls
   - Regular security audits