<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ChartDataService;
use App\Repositories\TestPyDbConRepository;
use App\Models\Entities\TestPyDbCon;
use App\Models\ViewModels\ChartDataViewModel;
use Mockery;

class ChartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that the main chart page loads successfully
     */
    public function test_chart_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('chart.index');
        $response->assertViewHas('valueTypes');
        $response->assertViewHas('connectionStatus');
    }

    /**
     * Test that the load chart data endpoint accepts POST requests
     */
    public function test_load_chart_data_endpoint_exists(): void
    {
        $response = $this->withoutMiddleware()
            ->post('/chart/load-data', [
                'value_type' => 'CO_TO'
            ]);

        // Should return successful response (might be JSON or redirect)
        $response->assertStatus(200);
    }

    /**
     * Test that the test connection endpoint exists
     */
    public function test_connection_test_endpoint_exists(): void
    {
        $response = $this->get('/chart/test-connection');

        // Should return JSON response
        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Test chart data service returns proper structure
     */
    public function test_chart_data_service_structure(): void
    {
        // Mock the repository
        $mockRepository = Mockery::mock(TestPyDbConRepository::class);
        $mockRepository->shouldReceive('getChartDataByContype')
            ->with('CO_TO')
            ->andReturn([
                ['contype' => 'Type1', 'total_value' => 100],
                ['contype' => 'Type2', 'total_value' => 200]
            ]);

        $service = new ChartDataService($mockRepository);
        $chartData = $service->getChartData('CO_TO');

        $this->assertInstanceOf(ChartDataViewModel::class, $chartData);
        $this->assertEquals(['Type1', 'Type2'], $chartData->labels);
        $this->assertEquals([100.0, 200.0], $chartData->data);
    }

    /**
     * Test available value types
     */
    public function test_available_value_types(): void
    {
        $mockRepository = Mockery::mock(TestPyDbConRepository::class);
        $mockRepository->shouldReceive('getAvailableValueTypes')
            ->andReturn(['CO_TO', 'CO_SP', 'CO_OP', 'CO_FP']);
            
        $service = new ChartDataService($mockRepository);
        
        $valueTypes = $service->getAvailableValueTypes();
        
        $expected = [
            'CO_TO' => 'Count',
            'CO_SP' => 'Shift Price',
            'CO_OP' => 'Overlap Price',
            'CO_FP' => 'Full Price'
        ];
        
        $this->assertEquals($expected, $valueTypes);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 