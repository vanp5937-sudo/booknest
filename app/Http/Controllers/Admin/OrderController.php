<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $r)
    {
        $orders = Order::when($r->filled('q'), fn($q) => $q->where(fn($search) => $search->where('order_code', 'like', '%' . $r->q . '%')->orWhere('customer_name', 'like', '%' . $r->q . '%')))->when($r->filled('status'), fn($q) => $q->where('status', $r->status))->latest()->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }
    public function show(Order $order)
    {
        $order->load('items');
        return view('admin.orders.show', compact('order'));
    }
    public function update(Request $r, Order $order)
    {
        $d = $r->validate(['status' => 'required|in:pending,confirmed,shipping,completed,cancelled']);
        $order->update($d);
        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng “' . $order->order_code . '” thành công.');
    }
}
