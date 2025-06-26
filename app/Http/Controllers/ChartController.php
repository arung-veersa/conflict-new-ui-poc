<?php

namespace App\Http\Controllers;

use App\Services\ChartDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ChartController extends Controller
{
    protected $chartService;
    
    public function __construct(ChartDataService $chartService)
    {
        $this->chartService = $chartService;
    }
    
    /**
     * Display the main chart page
     */
    public function index(): View
    {
        $valueTypes = $this->chartService->getAvailableValueTypes();
        $connectionStatus = $this->chartService->testConnection();
        
        return view('chart.index', compact('valueTypes', 'connectionStatus'));
    }
    
    /**
     * Load chart data via AJAX
     */
    public function loadChartData(Request $request): JsonResponse
    {
        try {
            $valueType = $request->input('value_type', 'CO_TO');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $statusFilter = $request->input('status_filter');
            $visitStatusFilter = $request->input('visit_status_filter');
            $billedStatusFilter = $request->input('billed_status_filter');
            $serviceCodeFilter = $request->input('service_code_filter');
            
            // Validate date inputs
            if ($fromDate && $toDate && $fromDate >= $toDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'To date must be greater than from date'
                ], 400);
            }
            
            // Validate status filter
            if ($statusFilter && !in_array($statusFilter, ['U', 'D', 'R'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status filter value'
                ], 400);
            }
            
            // Validate visit status filter
            if ($visitStatusFilter && !in_array($visitStatusFilter, ['Confirmed', 'Scheduled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid visit status filter value'
                ], 400);
            }
            
            // Validate billed status filter
            if ($billedStatusFilter && !in_array($billedStatusFilter, ['yes', 'no'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid billed status filter value'
                ], 400);
            }
            
            // Validate service code filter (basic length and character validation)
            if ($serviceCodeFilter && (strlen($serviceCodeFilter) > 50 || strlen(trim($serviceCodeFilter)) === 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service code filter must be between 1 and 50 characters'
                ], 400);
            }
            
            $filters = [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'status_filter' => $statusFilter,
                'visit_status_filter' => $visitStatusFilter,
                'billed_status_filter' => $billedStatusFilter,
                'service_code_filter' => $serviceCodeFilter ? trim($serviceCodeFilter) : null
            ];
            
            $chartData = $this->chartService->getChartData($valueType, $filters);
            $summaryStats = $this->chartService->getSummaryStats($filters);
            
            return response()->json([
                'success' => true,
                'chartData' => $chartData->toArray(),
                'summaryStats' => $summaryStats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading chart data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Test database connection
     */
    public function testConnection(): JsonResponse
    {
        try {
            $isConnected = $this->chartService->testConnection();
            
            return response()->json([
                'success' => true,
                'connected' => $isConnected,
                'message' => $isConnected ? 'Database connection successful' : 'Database connection failed'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'connected' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 