<?php

namespace App\Repositories;

use App\Models\Entities\TestPyDbCon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

/**
 * Enhanced Repository for ConflictData with Raw SQL
 * Optimized for analytical queries and database migration readiness
 */
class TestPyDbConRepository
{
    protected $model;
    protected $tableName;
    protected $connection;
    
    // Valid value types for validation
    private const VALID_VALUE_TYPES = ['CO_TO', 'CO_SP', 'CO_OP', 'CO_FP'];
    
    public function __construct(TestPyDbCon $model)
    {
        $this->model = $model;
        $this->tableName = $model->getTable();
        $this->connection = $model->getConnectionName();
    }
    
    /**
     * Get chart data grouped by CONTYPE with specified value type
     * Optimized for analytical queries with proper error handling
     */
    public function getChartDataByContype(string $valueType = 'CO_TO', array $filters = []): array
    {
        // Validate value type
        if (!$this->isValidValueType($valueType)) {
            throw new \InvalidArgumentException("Invalid value type: {$valueType}. Valid types: " . implode(', ', self::VALID_VALUE_TYPES));
        }

        $sql = $this->buildChartDataQuery($valueType, $filters);
        
        try {
            Log::info('Executing chart data query', [
                'valueType' => $valueType,
                'filters' => $filters,
                'sql' => $sql
            ]);
            
            $result = $this->executeQuery($sql);
            
            Log::info('Chart data query executed successfully', [
                'valueType' => $valueType,
                'filters' => $filters,
                'count' => count($result)
            ]);
            
            return $this->normalizeQueryResult($result);
            
        } catch (QueryException $e) {
            Log::error('Database error in getChartDataByContype', [
                'valueType' => $valueType,
                'filters' => $filters,
                'sql' => $sql,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ]);
            throw new \RuntimeException("Failed to retrieve chart data: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build the SQL query for chart data
     * Using unquoted identifiers that Snowflake stores as UPPERCASE
     */
    private function buildChartDataQuery(string $valueType, array $filters = []): string
    {
        $whereConditions = [
            "CONTYPE IS NOT NULL",
            "{$valueType} IS NOT NULL"
        ];
        
        // Add date filtering conditions
        $dateConditions = $this->buildDateConditions($filters);
        if (!empty($dateConditions)) {
            $whereConditions = array_merge($whereConditions, $dateConditions);
        }
        
        // Add status filtering conditions  
        $statusConditions = $this->buildStatusConditions($filters);
        if (!empty($statusConditions)) {
            $whereConditions = array_merge($whereConditions, $statusConditions);
        }
        
        // Add cost type filtering conditions
        $costTypeConditions = $this->buildCostTypeConditions($filters);
        if (!empty($costTypeConditions)) {
            $whereConditions = array_merge($whereConditions, $costTypeConditions);
        }
        
        // Add visit type filtering conditions
        $visitTypeConditions = $this->buildVisitTypeConditions($filters);
        if (!empty($visitTypeConditions)) {
            $whereConditions = array_merge($whereConditions, $visitTypeConditions);
        }
        
        return "
            SELECT 
                CONTYPE,
                SUM({$valueType}) as total_value,
                COUNT(*) as record_count
            FROM {$this->tableName}
            WHERE " . implode("\n              AND ", $whereConditions) . "
            GROUP BY CONTYPE 
            ORDER BY total_value DESC
        ";
    }
    
    /**
     * Get all records from the table (use with caution - for debugging only)
     * Consider adding LIMIT for production use
     */
    public function getAll(int $limit = 1000): array
    {
        $sql = "SELECT * FROM {$this->tableName} LIMIT {$limit}";
        
        try {
            Log::info('Executing get all records query', ['limit' => $limit]);
            
            $result = $this->executeQuery($sql);
            
            Log::info('All records retrieved successfully', [
                'count' => count($result),
                'limit' => $limit
            ]);
            
            return $this->normalizeQueryResult($result);
            
        } catch (QueryException $e) {
            Log::error('Database error in getAll', [
                'sql' => $sql,
                'limit' => $limit,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ]);
            throw new \RuntimeException("Failed to retrieve records: " . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Get count of total records
     * Simple count query with proper error handling
     */
    public function getTotalCount(): int
    {
        $sql = "SELECT COUNT(*) as total_count FROM {$this->tableName}";
        
        try {
            Log::info('Executing total count query');
            
            $result = $this->executeQuery($sql);
            
            if (empty($result)) {
                return 0;
            }
            
            // Normalize the result to handle database case sensitivity
            $normalized = $this->normalizeQueryResult($result)[0];
            $count = (int) ($normalized['total_count'] ?? 0);
            
            Log::info('Total count retrieved successfully', ['count' => $count]);
            
            return $count;
            
        } catch (QueryException $e) {
            Log::error('Database error in getTotalCount', [
                'sql' => $sql,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ]);
            throw new \RuntimeException("Failed to retrieve total count: " . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Get comprehensive summary statistics
     * Optimized single query for better performance
     */
    public function getSummaryStats(array $filters = []): array
    {
        $sql = $this->buildSummaryStatsQuery($filters);
        
        try {
            Log::info('Executing summary stats query', [
                'filters' => $filters,
                'sql' => $sql
            ]);
            
            $result = $this->executeQuery($sql);
            
            if (empty($result)) {
                Log::warning('No data found for summary stats');
                return $this->getEmptySummaryStats();
            }
            
            $stats = $this->normalizeQueryResult($result)[0];
            
            Log::info('Summary stats retrieved successfully', [
                'total_records' => $stats['total_records'] ?? 0,
                'filters' => $filters
            ]);
            
            return $stats;
            
        } catch (QueryException $e) {
            Log::error('Database error in getSummaryStats', [
                'sql' => $sql,
                'filters' => $filters,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ]);
            throw new \RuntimeException("Failed to retrieve summary statistics: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build the SQL query for summary statistics
     * Using unquoted identifiers that Snowflake stores as UPPERCASE
     */
    private function buildSummaryStatsQuery(array $filters = []): string
    {
        $whereConditions = ["1=1"]; // Always true condition as base
        
        // Add date filtering conditions
        $dateConditions = $this->buildDateConditions($filters);
        if (!empty($dateConditions)) {
            $whereConditions = array_merge($whereConditions, $dateConditions);
        }
        
        // Add status filtering conditions
        $statusConditions = $this->buildStatusConditions($filters);
        if (!empty($statusConditions)) {
            $whereConditions = array_merge($whereConditions, $statusConditions);
        }
        
        // Add cost type filtering conditions
        $costTypeConditions = $this->buildCostTypeConditions($filters);
        if (!empty($costTypeConditions)) {
            $whereConditions = array_merge($whereConditions, $costTypeConditions);
        }
        
        // Add visit type filtering conditions
        $visitTypeConditions = $this->buildVisitTypeConditions($filters);
        if (!empty($visitTypeConditions)) {
            $whereConditions = array_merge($whereConditions, $visitTypeConditions);
        }
        
        return "
            SELECT
                COUNT(*) as total_records,
                COUNT(DISTINCT CONTYPE) as unique_contypes,
                COALESCE(SUM(CO_TO), 0) as total_co_to,
                COALESCE(SUM(CO_SP), 0) as total_co_sp,
                COALESCE(SUM(CO_OP), 0) as total_co_op,
                COALESCE(SUM(CO_FP), 0) as total_co_fp,
                MIN(CRDATEUNIQUE) as earliest_date,
                MAX(CRDATEUNIQUE) as latest_date
            FROM {$this->tableName}
            WHERE " . implode("\n              AND ", $whereConditions) . "
        ";
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Execute SQL query with proper connection handling
     */
    private function executeQuery(string $sql): array
    {
        if ($this->connection) {
            return DB::connection($this->connection)->select($sql);
        }
        
        return DB::select($sql);
    }

    /**
     * Normalize query results to handle database case sensitivity
     * Converts objects to arrays and normalizes column names to lowercase
     */
    private function normalizeQueryResult(array $result): array
    {
        if (empty($result)) {
            return [];
        }

        return array_map(function ($row) {
            // Convert object to array
            $array = json_decode(json_encode($row), true);
            
            // Normalize keys to lowercase for consistency
            $normalized = [];
            foreach ($array as $key => $value) {
                $normalized[strtolower($key)] = $value;
            }
            
            return $normalized;
        }, $result);
    }

    /**
     * Validate if the provided value type is allowed
     */
    private function isValidValueType(string $valueType): bool
    {
        return in_array($valueType, self::VALID_VALUE_TYPES);
    }
    
    /**
     * Build date filtering conditions for SQL queries
     * Handles from_date and to_date filtering logic
     */
    private function buildDateConditions(array $filters): array
    {
        $conditions = [];
        
        if (!empty($filters['from_date'])) {
            $conditions[] = "CRDATEUNIQUE >= '{$filters['from_date']}'";
        }
        
        if (!empty($filters['to_date'])) {
            $conditions[] = "CRDATEUNIQUE <= '{$filters['to_date']}'";
        }
        
        return $conditions;
    }

    /**
     * Build status filtering conditions for SQL queries
     * Handles status_filter logic for STATUSFLAG column
     */
    private function buildStatusConditions(array $filters): array
    {
        $conditions = [];
        
        if (!empty($filters['status_filter'])) {
            $statusFilter = $filters['status_filter'];
            // Validate status filter value
            if (in_array($statusFilter, ['U', 'D', 'R'])) {
                $conditions[] = "STATUSFLAG = '{$statusFilter}'";
            }
        }
        
        return $conditions;
    }

    /**
     * Build cost type filtering conditions for SQL queries
     * Handles cost_type_filter logic for COSTTYPE column
     */
    private function buildCostTypeConditions(array $filters): array
    {
        $conditions = [];
        
        if (!empty($filters['cost_type_filter']) && $filters['cost_type_filter'] !== 'all') {
            $costTypeFilter = $filters['cost_type_filter'];
            // Validate cost type filter value
            if (in_array($costTypeFilter, ['Avoidance', 'Recovery'])) {
                $conditions[] = "COSTTYPE = '{$costTypeFilter}'";
            }
        }
        
        return $conditions;
    }

    /**
     * Build visit type filtering conditions for SQL queries
     * Handles visit_type_filter logic for VISITTYPE column
     */
    private function buildVisitTypeConditions(array $filters): array
    {
        $conditions = [];
        
        if (!empty($filters['visit_type_filter']) && $filters['visit_type_filter'] !== 'all') {
            $visitTypeFilter = $filters['visit_type_filter'];
            // Validate visit type filter value
            if (in_array($visitTypeFilter, ['Scheduled', 'Confirmed', 'Billed', 'Paid'])) {
                $conditions[] = "VISITTYPE = '{$visitTypeFilter}'";
            }
        }
        
        return $conditions;
    }

    /**
     * Get empty summary stats structure for fallback
     */
    private function getEmptySummaryStats(): array
    {
        return [
            'total_records' => 0,
            'unique_contypes' => 0,
            'total_co_to' => 0,
            'total_co_sp' => 0,
            'total_co_op' => 0,
            'total_co_fp' => 0,
            'earliest_date' => null,
            'latest_date' => null
        ];
    }

    /**
     * Get available value types for validation and UI
     */
    public function getAvailableValueTypes(): array
    {
        return self::VALID_VALUE_TYPES;
    }

    /**
     * Get distinct conflict types from the database
     * Useful for building filter options in the future
     * Using unquoted identifiers that Snowflake stores as UPPERCASE
     */
    public function getAvailableConflictTypes(): array
    {
        $sql = "SELECT DISTINCT CONTYPE FROM {$this->tableName} WHERE CONTYPE IS NOT NULL ORDER BY CONTYPE";
        
        try {
            Log::info('Executing available conflict types query');
            
            $result = $this->executeQuery($sql);
            
            if (empty($result)) {
                Log::info('No conflict types found');
                return [];
            }
            
            $normalized = $this->normalizeQueryResult($result);
            $types = array_column($normalized, 'contype');
            
            Log::info('Available conflict types retrieved successfully', [
                'count' => count($types),
                'types' => $types
            ]);
            
            // Remove any null or empty values
            return array_filter($types, fn($type) => !empty($type));
            
        } catch (QueryException $e) {
            Log::error('Database error in getAvailableConflictTypes', [
                'sql' => $sql,
                'error' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ]);
            throw new \RuntimeException("Failed to retrieve conflict types: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Check database connection health
     */
    public function checkConnection(): bool
    {
        try {
            $sql = "SELECT 1 as health_check";
            $result = $this->executeQuery($sql);
            
            return !empty($result) && count($result) === 1;
            
        } catch (QueryException $e) {
            Log::error('Database connection check failed', [
                'error' => $e->getMessage(),
                'connection' => $this->connection
            ]);
            return false;
        }
    }
} 