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
    "transaction_type": "credit" // or "debit"
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

To take this system to the next level, I'd focus on these architectural improvements:

1. **Horizontal Scaling**:
   - **Deploy Multiple Servers**: Running the application on multiple servers behind a load balancer helps handle more traffic and prevents overloading a single server.
   - **Read Replicas**: Separate database instances for read-only operations (like balance checks) reduce load on the primary database.
   - **Caching Layer**: Tools like Redis or Memcached cache frequently accessed data (like balances) to speed up retrieval and reduce database load.

2. **Performance Optimizations**:
   - **Database Indexing**: Indexes on frequently queried fields improve query performance.
   - **Rate Limiting**: Prevents system overload by controlling the rate of incoming requests.
   - **Caching Frequent Queries**: Caching balances and other frequently queried data further reduces database load.
   - **Asynchronous Queues**: Offloading non-critical tasks (e.g., logging) to queues improves responsiveness in high-volume scenarios.
   - **Distributed Locks**: Using locks like Redis-based locks prevents race conditions in environments with high concurrency.

3. **Monitoring & Logging**:
   - **Comprehensive Logging**: Logs provide a record of operations and are essential for troubleshooting issues.
   - **Transaction Monitoring**: Monitoring for failures helps detect issues early.
   - **Performance Metrics**: Tracking response times, transaction throughput, and error rates helps gauge performance and reliability.

4. **Security Enhancements**:
   - **Two-Factor Authentication (2FA)**: Adding 2FA for sensitive operations (like withdrawals) strengthens security.
   - **Request Signing**: Ensures request integrity by requiring signed requests.
   - **Regular Audits**: Frequent security audits help identify and mitigate vulnerabilities. 

These steps help create a scalable, efficient, and secure transaction processing system ideal for production environments.