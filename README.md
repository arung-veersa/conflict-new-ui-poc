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
- Snowflake ODBC Driver
- Node.js & NPM (for asset compilation)

### Environment Configuration

Create a `.env` file with the following Snowflake connection details:

```env
APP_NAME="Snowflake Data Visualization"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database Configuration
DB_CONNECTION=snowflake
DB_HOST=your-account.snowflakecomputing.com
DB_PORT=443
DB_DATABASE=CONFLICTREPORT_SANDBOX
DB_USERNAME=CONFLICTREPORT_USER
DB_PASSWORD=
DB_WAREHOUSE=CONFLICTREPORT_APP_WH

# Snowflake Authentication
SNOWFLAKE_ACCOUNT=your-account
SNOWFLAKE_PRIVATE_KEY_PATH=/path/to/private/key.pem
SNOWFLAKE_PRIVATE_KEY_PASSPHRASE=your-passphrase
```

### Installation Steps

1. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

3. **Compile Assets**:
   ```bash
   npm run build
   ```

4. **Test Connection**:
   ```bash
   php artisan tinker
   >>> DB::select('SELECT CURRENT_TIMESTAMP()');
   ```

5. **Start Application**:
   ```bash
   php artisan serve
   ```

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
