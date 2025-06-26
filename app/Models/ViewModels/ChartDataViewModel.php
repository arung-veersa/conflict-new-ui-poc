<?php

namespace App\Models\ViewModels;

/**
 * View Model for Chart Data Presentation
 * Handles chart-specific formatting and display logic
 */
class ChartDataViewModel
{
    public array $labels;
    public array $data;
    public array $backgroundColor;
    public array $borderColor;
    public string $valueType;
    
    public function __construct(array $labels = [], array $data = [], string $valueType = 'CO_TO')
    {
        $this->labels = $labels;
        $this->data = $data;
        $this->valueType = $valueType;
        $this->backgroundColor = $this->generateColors(count($labels));
        $this->borderColor = $this->generateBorderColors(count($labels));
    }
    
    private function generateColors(int $count): array
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
        ];
        
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $colors[$i % count($colors)];
        }
        
        return $result;
    }
    
    private function generateBorderColors(int $count): array
    {
        $colors = [
            '#E53E5F', '#2D8BC0', '#E6B800', '#3A9A9A', '#7A4DB3',
            '#E68A00', '#E53E5F', '#A8A9AC', '#3A9A9A', '#E53E5F'
        ];
        
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $colors[$i % count($colors)];
        }
        
        return $result;
    }
    
    public function toArray(): array
    {
        return [
            'labels' => $this->labels,
            'datasets' => [
                [
                    'data' => $this->data,
                    'backgroundColor' => $this->backgroundColor,
                    'borderColor' => $this->borderColor,
                    'borderWidth' => 2
                ]
            ],
            'valueType' => $this->valueType
        ];
    }
} 