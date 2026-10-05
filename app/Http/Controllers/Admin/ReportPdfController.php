<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function exportPdf(Request $request)
    {
        $period = $request->query('period', 'month');

        $query = Order::with(['customer', 'payment', 'orderItems']);
        $paymentQuery = Payment::where('status', 'PAID');

        $periodLabel = 'Bulan Ini (' . now()->format('F Y') . ')';

        if ($period === 'today') {
            $startDate = now()->startOfDay();
            $query->where('created_at', '>=', $startDate);
            $paymentQuery->where('paid_at', '>=', $startDate);
            $periodLabel = 'Hari Ini (' . now()->format('d M Y') . ')';
        } elseif ($period === 'week') {
            $startDate = now()->startOfWeek();
            $query->where('created_at', '>=', $startDate);
            $paymentQuery->where('paid_at', '>=', $startDate);
            $periodLabel = 'Minggu Ini (' . now()->startOfWeek()->format('d M') . ' - ' . now()->endOfWeek()->format('d M Y') . ')';
        } elseif ($period === 'month') {
            $startDate = now()->startOfMonth();
            $query->where('created_at', '>=', $startDate);
            $paymentQuery->where('paid_at', '>=', $startDate);
            $periodLabel = 'Bulan Ini (' . now()->format('F Y') . ')';
        } elseif ($period === 'all') {
            $periodLabel = 'Semua Periode Transaksi';
        }

        $orders = $query->latest()->get();

        $totalRevenue = $paymentQuery->sum('amount');
        $totalOrders = $orders->count();
        $completedOrders = $orders->whereIn('status', ['COMPLETED', 'ORDER_SELESAI'])->count();
        $cancelledOrders = $orders->where('status', 'CANCELLED')->count();
        $inProgressOrders = $orders->whereNotIn('status', ['COMPLETED', 'ORDER_SELESAI', 'CANCELLED'])->count();

        // Top Selling Items
        $topItems = OrderItem::selectRaw('service_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('service_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Driver Performance Summary
        $drivers = User::role('driver')
            ->withCount(['pickupTasks as completed_pickups' => function ($q) {
                $q->where('status', 'COMPLETED');
            }])
            ->withCount(['deliveryTasks as completed_deliveries' => function ($q) {
                $q->where('status', 'COMPLETED');
            }])
            ->get();

        $printedAt = now()->format('d M Y, H:i') . ' WIB';
        $printedBy = auth()->user()->name ?? 'Administrator';

        $data = [
            'periodLabel' => $periodLabel,
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'inProgressOrders' => $inProgressOrders,
            'topItems' => $topItems,
            'drivers' => $drivers,
            'printedAt' => $printedAt,
            'printedBy' => $printedBy,
        ];

        $pdf = Pdf::loadView('pdf.executive-report', $data)
            ->setPaper('a4', 'portrait');

        $fileName = 'Laporan_Eksekutif_Washly_' . str_replace(' ', '_', $period) . '_' . date('Ymd_His') . '.pdf';

        return $pdf->stream($fileName);
    }
}
