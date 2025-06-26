# Snowflake Laravel Application Setup Guide

This Laravel application connects to a Snowflake database and displays a pie chart based on the `TEST_PY_DB_CON` table.

## Prerequisites

1. PHP 8.2 or higher
2. Composer
3. Laravel 12.x
4. ODBC driver for PHP
5. Snowflake ODBC driver installed and configured

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the `.env.example` file to `.env`:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

## Snowflake Configuration

Update your `.env` file with the following Snowflake settings:

```env
DB_CONNECTION=snowflake
SNOWFLAKE_DSN=cmdsn
SNOWFLAKE_HOST=your-snowflake-host.snowflakecomputing.com
SNOWFLAKE_PORT=443
SNOWFLAKE_DATABASE=CONFLICTREPORT_SANDBOX
SNOWFLAKE_SCHEMA=PUBLIC
SNOWFLAKE_WAREHOUSE=your-warehouse-name
SNOWFLAKE_USERNAME=your-username
SNOWFLAKE_PASSWORD=your-password
SNOWFLAKE_ACCOUNT=your-account-identifier
SNOWFLAKE_ROLE=ACCOUNTADMIN
```

### ODBC Configuration

1. Install the Snowflake ODBC driver
2. Configure the DSN named `cmdsn` in your ODBC Data Source Administrator
3. Test the connection using the ODBC test utility

## Database Table Structure

The application expects the following table structure in Snowflake:

```sql
CREATE TABLE TEST_PY_DB_CON (
    PAYERID VARCHAR(50),
    CRDATEUNIQUE DATE,
    CONTYPE VARCHAR(50),
    CONTYPES VARCHAR(50),
    SERVICECODE VARCHAR(50),
    STATUSFLAG VARCHAR(5),
    BILLED VARCHAR(3),
    ISCONFIRMED VARCHAR(20),
    CO_TO NUMBER(38,0),
    CO_SP NUMBER(38,2),
    CO_OP NUMBER(38,2),
    CO_FP NUMBER(38,2)
);
```

## Running the Application

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Open your browser and navigate to `http://localhost:8000`

3. Click the "Load Data" button to fetch and display the pie chart

## Features

- **Pie Chart Visualization**: Displays data grouped by CONTYPE
- **Multiple Value Types**: Choose between Count (CO_TO), Shift Price (CO_SP), Overlap Price (CO_OP), or Full Price (CO_FP)
- **Real-time Data Loading**: AJAX-based data loading with loading indicators
- **Summary Statistics**: Displays total records and aggregated values
- **Connection Status**: Shows real-time database connection status
- **Responsive Design**: Modern UI with Bootstrap and Chart.js

## Architecture

The application follows the MVC pattern with additional layers:

- **Models**: `TestPyDbCon` (Entity) and `ChartDataViewModel` (View Model)
- **Repository**: `TestPyDbConRepository` for database operations
- **Service**: `ChartDataService` for business logic
- **Controller**: `ChartController` for request handling
- **View**: Blade template with Chart.js integration

## Troubleshooting

### Common Issues

1. **ODBC Connection Error**: Ensure the Snowflake ODBC driver is properly installed and the DSN is configured
2. **Permission Denied**: Verify the Snowflake user has access to the CONFLICTREPORT_SANDBOX database and PUBLIC schema
3. **Table Not Found**: Ensure the TEST_PY_DB_CON table exists in the specified database and schema

### Debug Mode

Enable debug mode in `.env` to see detailed error messages:
```env
APP_DEBUG=true
```

## Dependencies

- Laravel Framework 12.x
- yoramdelangen/laravel-pdo-odbc
- Chart.js (CDN)
- Bootstrap 5.3 (CDN)
- Font Awesome (CDN) 