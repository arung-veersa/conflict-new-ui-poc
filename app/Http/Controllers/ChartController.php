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
            $costTypeFilter = $request->input('cost_type_filter');
            $visitTypeFilter = $request->input('visit_type_filter');
            
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
            
            // Validate cost type filter
            if ($costTypeFilter && $costTypeFilter !== 'all' && !in_array($costTypeFilter, ['Avoidance', 'Recovery'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cost type filter value'
                ], 400);
            }
            
            // Validate visit type filter
            if ($visitTypeFilter && $visitTypeFilter !== 'all' && !in_array($visitTypeFilter, ['Scheduled', 'Confirmed', 'Billed', 'Paid'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid visit type filter value'
                ], 400);
            }
            
            $filters = [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'status_filter' => $statusFilter,
                'cost_type_filter' => $costTypeFilter,
                'visit_type_filter' => $visitTypeFilter
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