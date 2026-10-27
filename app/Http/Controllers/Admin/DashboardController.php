<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'books' => Book::count(),
            'categories' => Category::count(),
            'orders' => Order::count(),
            'users' => User::count(),
        ];

        $orders = Order::latest()->take(6)->get();

        // Biểu đồ ghi nhận doanh thu theo ngày đơn hàng được cập nhật/xác nhận hoàn tất trong tháng hiện tại.
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfToday = $now->copy()->endOfDay();

        $dailyRevenue = Order::query()
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startOfMonth, $endOfToday])
            ->selectRaw('DATE(updated_at) as revenue_date, SUM(total) as revenue_total')
            ->groupBy('revenue_date')
            ->pluck('revenue_total', 'revenue_date');

        $months = [];
        $revenues = [];

        // Chỉ vẽ từ ngày đầu tháng đến hôm nay theo múi giờ Việt Nam; không đưa ngày tương lai vào biểu đồ.
        for ($day = 1; $day <= $now->day; $day++) {
            $date = $now->copy()->startOfMonth()->addDays($day - 1);
            $dateKey = $date->toDateString();
            $months[] = $date->format('d/m');
            $revenues[] = (float) ($dailyRevenue[$dateKey] ?? 0);
        }

        return view('admin.dashboard', compact('stats', 'orders', 'months', 'revenues'));
    }
}
