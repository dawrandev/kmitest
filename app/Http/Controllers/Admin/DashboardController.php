<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Admin dashboard sahifasini ko'rsatish
     */
    public function index()
    {
        try {
            $data = $this->dashboardService->getDashboardData();

            return view('admin.dashboard.index', compact('data'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            return view('admin.dashboard.index')->with('error', 'Dashboard ma\'lumotlarini yuklashda xatolik yuz berdi.');
        }
    }

    /**
     * Real-time statistikalarni API orqali olish
     */
    public function getRealTimeStats()
    {
        try {
            $data = $this->dashboardService->getDashboardData();

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $data['overview']['secondary_stats'],
                    'real_time' => $data['widgets']['system_health'],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ma\'lumotlarni yuklashda xatolik yuz berdi.'
            ], 500);
        }
    }

    /**
     * Chart ma'lumotlarini API orqali olish
     */
    public function getChartData(Request $request)
    {
        try {
            $chartType = $request->get('type', 'daily_activity');
            $data = $this->dashboardService->getDashboardData();

            if (!isset($data['charts'][$chartType])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chart turi topilmadi.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data['charts'][$chartType]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Chart ma\'lumotlarini yuklashda xatolik yuz berdi.'
            ], 500);
        }
    }

    /**
     * Table ma'lumotlarini API orqali olish
     */
    public function getTableData(Request $request)
    {
        try {
            $tableType = $request->get('type', 'recent_tests');
            $limit = $request->get('limit', 10);

            $data = $this->dashboardService->getDashboardData();

            if (!isset($data['tables'][$tableType])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table turi topilmadi.'
                ], 404);
            }

            $tableData = $data['tables'][$tableType];

            // Limit qo'llash
            if (is_array($tableData) || $tableData instanceof \Illuminate\Support\Collection) {
                $tableData = collect($tableData)->take($limit);
            }

            return response()->json([
                'success' => true,
                'data' => $tableData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Table ma\'lumotlarini yuklashda xatolik yuz berdi.'
            ], 500);
        }
    }

    /**
     * Statistikalarni export qilish
     */
    public function exportStats(Request $request)
    {
        try {
            $format = $request->get('format', 'excel'); // excel, csv, pdf

            // Export logikasi bu yerda bo'ladi
            // Laravel Excel yoki boshqa package ishlatiladi

            return response()->json([
                'success' => true,
                'message' => 'Export jarayoni boshlandi.',
                'download_url' => '#' // Haqiqiy download URL
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export jarayonida xatolik yuz berdi.'
            ], 500);
        }
    }

    /**
     * Tizim holatini tekshirish
     */
    public function systemHealth()
    {
        try {
            $data = $this->dashboardService->getDashboardData();

            return response()->json([
                'success' => true,
                'data' => $data['widgets']['system_health']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tizim holatini tekshirishda xatolik yuz berdi.'
            ], 500);
        }
    }
}
