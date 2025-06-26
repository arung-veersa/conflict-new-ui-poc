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
            
            // Validate date inputs
            if ($fromDate && $toDate && $fromDate >= $toDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'To date must be greater than from date'
                ], 400);
            }
            
            $dateFilters = [
                'from_date' => $fromDate,
                'to_date' => $toDate
            ];
            
            $chartData = $this->chartService->getChartData($valueType, $dateFilters);
            $summaryStats = $this->chartService->getSummaryStats($dateFilters);
            
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