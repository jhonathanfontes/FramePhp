<?php

namespace App\Controllers;

use Core\Controller;
use Core\Controller\BaseController;
use Core\View\TwigManager;

class DashboardController extends BaseController
{
    public function index()
    {
        // Dados para os cards
        $dashboardData = [
            'totalSales' => [
                'value' => 45000,
                'change' => 12,
                'changeType' => 'up'
            ],
            'newUsers' => [
                'value' => 150,
                'change' => 8,
                'changeType' => 'up'
            ],
            'pendingOrders' => [
                'value' => 23,
                'change' => 0,
                'changeType' => 'neutral'
            ],
            'conversionRate' => [
                'value' => 2.4,
                'change' => 3,
                'changeType' => 'down'
            ]
        ];

        // Dados para os gráficos
        $chartData = [
            'sales' => [
                'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                'data' => [12000, 19000, 15000, 25000, 22000, 30000]
            ],
            'users' => [
                'labels' => ['Ativos', 'Inativos', 'Novos'],
                'data' => [65, 25, 10]
            ]
        ];
    
        // Renderizar a view
        echo $this->view('dashboard/index', [
            'dashboardData' => $dashboardData,
            'chartData' => $chartData
        ]);
    }
} 