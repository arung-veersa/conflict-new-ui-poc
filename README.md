# Snowflake Data Visualization Laravel App

## ✅ Status: Production Ready

This Laravel application provides an interactive data visualization dashboard for Snowflake conflict data analysis with pie charts and summary statistics.

## 🚀 Features

- **Real-time Data Visualization**: Interactive pie charts powered by Chart.js
- **Dynamic Value Types**: Switch between Count, Shift Price, Overlap Price, and Full Price
- **Summary Statistics**: Real-time dashboard showing total records, unique types, and aggregated values
- **Snowflake Integration**: Direct connection to Snowflake database with optimized queries
- **Responsive Design**: Modern Bootstrap UI with mobile-friendly design
- **Error Handling**: Comprehensive error handling and user feedback

## 🏗️ Technical Architecture

- **Backend**: Laravel 11 with PHP 8.2+
- **Database**: Snowflake via ODBC connection
- **Frontend**: Bootstrap 5 + Chart.js + jQuery
- **Authentication**: JWT-based authentication for Snowflake
- **Caching**: Laravel query caching for optimized performance

## 📊 Database Schema

The application connects to the `TEST_PY_DB_CON` table with the following key columns:
- `CONTYPE`: Conflict type categories
- `CO_TO`, `CO_SP`, `CO_OP`, `CO_FP`: Various price/count metrics
- Additional metadata fields for comprehensive analysis

## 🔧 Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Snowflake ODBC Driver configured and working
- Node.js & NPM (for asset compilation)

### Quick Start (After Fresh Pull)

Follow these steps to set up the project after cloning/pulling from the repository:

1. **Clone the Repository** (if not already done):
   ```bash
   git clone <repository-url>
   cd conflict-new-ui-poc
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**:
   ```bash
   npm install
   ```

4. **Environment Setup**:
   - Copy the `.env.example` file to `.env` (if `.env` doesn't exist):
     ```bash
     cp .env.example .env
     ```
   - Update the `.env` file with your Snowflake connection details (see below)

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Build Frontend Assets**:
   ```bash
   npm run build
   ```

7. **Start the Development Server**:
   ```bash
   php artisan serve
   ```

8. **Access the Application**:
   - Open your browser and go to: `http://localhost:8000`

### Environment Configuration

Update your `.env` file with the following Snowflake connection details:

```env
APP_NAME="Snowflake Data Visualization"
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration - Snowflake via ODBC
DB_CONNECTION=snowflake
SNOWFLAKE_ODBC_DSN=your-dsn-name
SNOWFLAKE_HOST=your-account-id
SNOWFLAKE_PORT=443
SNOWFLAKE_DATABASE=CONFLICTREPORT_SANDBOX
SNOWFLAKE_SCHEMA=PUBLIC
SNOWFLAKE_WAREHOUSE=CONFLICTREPORT_APP_WH
SNOWFLAKE_USERNAME=CONFLICTREPORT_USER
SNOWFLAKE_ROLE=ACCOUNTADMIN
SNOWFLAKE_AUTH_METHOD=keypair
SNOWFLAKE_PRIVATE_KEY_FILE=C:/path/to/your/private/key.pem

# Alternative: Password Authentication
# SNOWFLAKE_AUTH_METHOD=password
# SNOWFLAKE_PASSWORD=your-password

# SSL Configuration (for development)
SNOWFLAKE_SSL_VERIFY=false

# Laravel Configuration
LOG_CHANNEL=stack
LOG_LEVEL=debug
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Testing the Setup

1. **Test Database Connection**:
   ```bash
   php artisan tinker
   >>> DB::select('SELECT CURRENT_TIMESTAMP()');
   ```

2. **Verify Application**:
   - Visit `http://localhost:8000`
   - The dashboard should load without errors
   - Try loading chart data to verify Snowflake connectivity

### Development Commands

- **Start Development Server**: `php artisan serve`
- **Build Assets for Development**: `npm run dev`
- **Watch Assets for Changes**: `npm run dev -- --watch`
- **Run Tests**: `php artisan test`
- **Clear Cache**: `php artisan cache:clear`
- **Clear Config**: `php artisan config:clear`

## 📈 Usage

1. **Access Dashboard**: Navigate to the application URL
2. **Select Value Type**: Choose from the dropdown (Count, Shift Price, etc.)
3. **Load Data**: Click "Load Data" to generate the visualization
4. **View Summary**: Check the summary statistics panel for key metrics

## 🔒 Security Features

- JWT authentication for Snowflake connection
- Environment-based configuration
- CSRF protection on all forms
- Debug routes disabled in production
- Secure HTTPS connections to Snowflake

## 🛠️ Maintenance

### Log Management
```bash
# Clear application logs
> storage/logs/laravel.log

# Monitor application performance
php artisan route:list
php artisan config:cache
```

### Performance Optimization
- Database queries are optimized with proper indexing
- Results are cached to reduce Snowflake query load
- Chart rendering is optimized for large datasets

## 📝 API Endpoints

- `GET /` - Main dashboard
- `POST /chart/load-data` - Load chart data with specified value type
- `GET /chart/test-connection` - Test database connectivity

**Debug endpoints (development only)**:
- `GET /test-db` - Database connection test
- `GET /debug-table` - Table structure and sample data

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**:
- Verify Snowflake credentials and network connectivity
- Check ODBC driver installation
- Ensure private key file permissions are correct

**Chart Not Loading**:
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Ensure data exists in the target table

**Performance Issues**:
- Monitor Snowflake warehouse usage
- Consider implementing query result caching
- Check table indexing for optimal query performance

## 📊 Data Analysis Capabilities

The application provides insights into:
- **Conflict Distribution**: Visual breakdown by conflict type
- **Financial Impact**: Analysis of pricing across different categories  
- **Volume Analysis**: Record counts and unique type tracking
- **Trend Identification**: Pattern recognition in conflict data

## 🏆 Production Checklist

- ✅ Database connection optimized and tested
- ✅ Error handling implemented
- ✅ Security measures in place
- ✅ Debug code removed
- ✅ Logs cleaned and monitoring ready
- ✅ Performance optimized
- ✅ Documentation complete

---

**Version**: 1.0.0  
**Last Updated**: June 2025  
**Status**: Production Ready ✅
