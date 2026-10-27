@extends('layouts.app')

@section('title', 'Đơn hàng của tôi | BookNest')

@section('content')
    <div class="container page-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">TÀI KHOẢN BOOKNEST</span>
                <h1>Đơn hàng của tôi</h1>
            </div>
        </div>

        @forelse ($orders as $order)
            <div class="order-card">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <b>{{ $order->order_code }}</b>
                    <span class="status-pill">{{ $order->status }}</span>
                </div>

                <p class="text-muted small">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </p>

                @foreach ($order->items as $item)
                    <div class="summary-line">
                        <span>{{ $item->book_title }} × {{ $item->quantity }}</span>
                        <b>{{ number_format($item->line_total, 0, ',', '.') }}đ</b>
                    </div>
                @endforeach

                <div class="summary-line summary-total">
                    <span>Tổng cộng</span>
                    <b>{{ number_format($order->total, 0, ',', '.') }}đ</b>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-receipt"></i>
                <h3>Bạn chưa có đơn hàng nào</h3>
                <a href="{{ route('home') }}" class="btn btn-orange">Mua sách ngay</a>
            </div>
        @endforelse

        <div class="pagination-wrap">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
