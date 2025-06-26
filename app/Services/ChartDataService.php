<?php

namespace App\Services;

use App\Models\ViewModels\ChartDataViewModel;
use App\Repositories\TestPyDbConRepository;
use Illuminate\Support\Facades\Log;

class ChartDataService
{
    protected $repository;
    
    public function __construct(TestPyDbConRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Get chart data for pie chart based on CONTYPE grouping
     */
    public function getChartData(string $valueType = 'CO_TO', array $dateFilters = []): ChartDataViewModel
    {
        try {
            Log::info('Getting chart data', [
                'valueType' => $valueType,
                'dateFilters' => $dateFilters
            ]);
            
            // Validate value type
            $validValueTypes = ['CO_TO', 'CO_SP', 'CO_OP', 'CO_FP'];
            if (!in_array($valueType, $validValueTypes)) {
                Log::warning('Invalid value type, using default', ['requested' => $valueType]);
                $valueType = 'CO_TO'; // Default fallback
            }
            
            // Get data from repository
            Log::info('Fetching data from repository');
            $chartData = $this->repository->getChartDataByContype($valueType, $dateFilters);
            Log::info('Retrieved chart data', ['count' => count($chartData)]);
            
            // Extract labels and data
            $labels = [];
            $data = [];
            
            foreach ($chartData as $item) {
                // Repository now uses quoted identifiers ensuring consistent lowercase column names
                $labels[] = $item['contype'] ?? 'Unknown';
                $data[] = (float) ($item['total_value'] ?? 0);
            }
            
            Log::info('Created chart data', [
                'labels_count' => count($labels),
                'data_count' => count($data)
            ]);
            
            // Create view model
            return new ChartDataViewModel($labels, $data, $valueType);
            
        } catch (\Exception $e) {
            Log::error('Error fetching chart data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty chart data on error
            return new ChartDataViewModel([], [], $valueType);
        }
    }
    
    /**
     * Get summary statistics
     */
    public function getSummaryStats(array $dateFilters = []): array
    {
        try {
            Log::info('Getting summary stats', ['dateFilters' => $dateFilters]);
            $stats = $this->repository->getSummaryStats($dateFilters);
            Log::info('Retrieved summary stats successfully');
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error fetching summary stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'total_records' => 0,
                'unique_contypes' => 0,
                'total_co_to' => 0,
                'total_co_sp' => 0,
                'total_co_op' => 0,
                'total_co_fp' => 0
            ];
        }
    }
    
    /**
     * Get available value types for the chart
     * Now uses repository method for consistency
     */
    public function getAvailableValueTypes(): array
    {
        $valueTypes = $this->repository->getAvailableValueTypes();
        
        // Map to display labels
        $mapped = [];
        foreach ($valueTypes as $type) {
            $mapped[$type] = match ($type) {
                'CO_TO' => 'Count',
                'CO_SP' => 'Shift Price',
                'CO_OP' => 'Overlap Price',
                'CO_FP' => 'Full Price',
                default => $type
            };
        }
        
        return $mapped;
    }
    
    /**
     * Test database connection
     * Now uses dedicated health check method
     */
    public function testConnection(): bool
    {
        try {
            Log::info('Testing database connection');
            
            $isHealthy = $this->repository->checkConnection();
            
            if ($isHealthy) {
                Log::info('Database connection test successful');
            } else {
                Log::warning('Database connection test failed');
            }
            
            return $isHealthy;
            
        } catch (\Exception $e) {
            Log::error('Database connection test failed with exception', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            return false;
        }
    }

    /**
     * Get available conflict types from database
     * Useful for building filters in the future
     */
    public function getAvailableConflictTypes(): array
    {
        try {
            Log::info('Getting available conflict types');
            $types = $this->repository->getAvailableConflictTypes();
            Log::info('Retrieved conflict types successfully', ['count' => count($types)]);
            return $types;
        } catch (\Exception $e) {
            Log::error('Error fetching conflict types', [
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ]);
            return [];
        }
    }
} 