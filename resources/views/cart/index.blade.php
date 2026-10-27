@extends('layouts.app')

@section('title', 'Giỏ hàng | BookNest')

@section('content')
    <div class="container page-container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">SẴN SÀNG CHO HÀNH TRÌNH MỚI?</span>
                <h1>Giỏ hàng của bạn</h1>
                <p>Kiểm tra sách và số lượng trước khi đặt hàng.</p>
            </div>
        </div>

        @if (count($cart) && $books->count())
            <div class="row g-4">
                <div class="col-lg-8">
                    <form method="post" action="{{ route('cart.update') }}">
                        @csrf
                        <div class="cart-table">
                            <div class="cart-table-head">
                                <span>Sản phẩm</span>
                                <span>Đơn giá</span>
                                <span>Số lượng</span>
                                <span>Tạm tính</span>
                            </div>

                            @foreach ($cart as $id => $row)
                                @if (isset($books[$id]))
                                    @php($book = $books[$id])
                                    <div class="cart-row">
                                        <div class="cart-product">
                                            <a href="{{ route('books.show', $book) }}" class="cart-thumb">
                                                <img src="{{ route('book.image', ['path' => $book->image_path]) }}" alt="Bìa {{ $book->title }}">
                                            </a>
                                            <div>
                                                <a href="{{ route('books.show', $book) }}">
                                                    <b>{{ $book->title }}</b>
                                                </a>
                                                <small>{{ $book->sku }}</small>
                                                <button type="submit" form="remove-book-{{ $book->id }}" class="remove-link">
                                                    <i class="bi bi-trash3"></i> Xóa
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cart-cell">
                                            {{ number_format($book->price, 0, ',', '.') }}đ
                                        </div>

                                        <div class="cart-cell">
                                            <input
                                                class="form-control quantity-input"
                                                type="number"
                                                min="0"
                                                max="{{ $book->stock }}"
                                                data-stock-title="{{ $book->title }}"
                                                name="quantities[{{ $book->id }}]"
                                                value="{{ $row['quantity'] }}"
                                                aria-label="Số lượng {{ $book->title }}"
                                            >
                                            <small class="d-block text-muted mt-1">
                                                Trong kho còn {{ $book->stock }} quyển
                                            </small>
                                        </div>

                                        <div class="cart-cell fw-bold">
                                            {{ number_format($book->price * $row['quantity'], 0, ',', '.') }}đ
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <div class="cart-update">
                                <button class="btn btn-outline-navy btn-sm" type="submit">Cập nhật giỏ hàng</button>
                                <a href="{{ route('home') }}" class="text-link">← Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    </form>

                    @foreach ($cart as $id => $row)
                        @if (isset($books[$id]))
                            <form id="remove-book-{{ $id }}" method="post" action="{{ route('cart.remove', $books[$id]) }}" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    @endforeach
                </div>

                <div class="col-lg-4">
                    <div class="summary-card">
                        <h3>Tóm tắt đơn hàng</h3>
                        <div class="summary-line">
                            <span>Tạm tính</span>
                            <b>{{ number_format($total, 0, ',', '.') }}đ</b>
                        </div>
                        <div class="summary-line">
                            <span>Phí vận chuyển tạm tính</span>
                            <b>20.000đ</b>
                        </div>
                        <hr>
                        <div class="summary-line summary-total">
                            <span>Tổng cộng dự kiến</span>
                            <b>{{ number_format($total + 20000, 0, ',', '.') }}đ</b>
                        </div>
                        <a href="{{ route('checkout.show') }}" class="btn btn-orange w-100 mt-3">
                            Tiến hành thanh toán <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <p class="secure-note">
                            <i class="bi bi-shield-check"></i> Thông tin của bạn được bảo vệ
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-bag"></i>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p>Thêm vài cuốn sách yêu thích để bắt đầu nhé.</p>
                <a class="btn btn-orange" href="{{ route('home') }}">Khám phá sách</a>
            </div>
        @endif
    </div>
@endsection
