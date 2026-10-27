@extends('layouts.app')

@section('title', $book->title . ' | BookNest')

@section('content')
<div class="container page-container">
    <nav class="breadcrumb small">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('home', ['category' => $book->category->slug]) }}">{{ $book->category->name }}</a>
        <span>/</span>
        <span>{{ $book->title }}</span>
    </nav>

    <div class="row g-5 book-detail">
        <div class="col-md-5">
            <div class="detail-cover">
                @if ($book->image_path)
                    <img src="{{ route('book.image', ['path' => $book->image_path]) }}" alt="{{ $book->title }}">
                @else
                    <div class="cover-placeholder"><i class="bi bi-book-half"></i></div>
                @endif
            </div>
        </div>
        <div class="col-md-7">
            <span class="eyebrow">{{ $book->category->name }}</span>
            <h1>{{ $book->title }}</h1>
            <p class="detail-author">Tác giả: <strong>{{ $book->author }}</strong></p>
            <div class="detail-price">
                {{ number_format($book->price, 0, ',', '.') }}đ
                @if ($book->original_price)
                    <del>{{ number_format($book->original_price, 0, ',', '.') }}đ</del>
                @endif
            </div>
            <div class="detail-meta">
                <span><i class="bi bi-upc-scan"></i> Mã sách: {{ $book->sku }}</span>
                <span><i class="bi bi-box-seam"></i> {{ $book->stock > 0 ? 'Còn hàng · ' . $book->stock . ' quyển trong kho' : 'Tạm hết hàng' }}</span>
            </div>
            <p class="detail-description">{{ $book->description ?: 'Một lựa chọn thú vị cho tủ sách của bạn. Khám phá nội dung và bắt đầu hành trình đọc ngay hôm nay.' }}</p>

            <div class="detail-actions">
                <form method="post" action="{{ route('cart.add', $book) }}" class="add-cart-form" data-cart-add-form>
                    @csrf
                    <label for="quantity">Số lượng</label>
                    <input class="form-control" id="quantity" name="quantity" type="number" min="1" max="{{ max(1, $book->stock) }}" data-stock-title="{{ $book->title }}" value="1" {{ $book->stock < 1 ? 'disabled' : '' }}>
                    <button class="btn btn-outline-navy" type="submit" {{ $book->stock < 1 ? 'disabled' : '' }}>
                        <i class="bi bi-bag-plus me-2"></i>Thêm vào giỏ hàng
                    </button>
                </form>
                <form method="post" action="{{ route('cart.buy-now', $book) }}" class="buy-now-detail-form">
                    @csrf
                    <input type="hidden" name="quantity" value="1" data-buy-now-quantity>
                    <button class="btn btn-orange" type="submit" {{ $book->stock < 1 ? 'disabled' : '' }}>
                        <i class="bi bi-lightning-charge me-2"></i>Mua ngay
                    </button>
                </form>
                <a href="{{ route('cart.index') }}" class="btn btn-link">Xem giỏ hàng</a>
            </div>

            <div class="detail-perks">
                <span><i class="bi bi-truck"></i> Giao hàng toàn quốc</span>
                <span><i class="bi bi-shield-check"></i> Đặt hàng an tâm</span>
            </div>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="section-block">
            <div class="section-heading">
                <div><span class="eyebrow">CÙNG DANH MỤC</span><h2>Bạn có thể <em>yêu thích</em></h2></div>
            </div>
            <div class="row g-4">
                @foreach ($related as $item)
                    <div class="col-6 col-md-3">@include('store.partials.book-card', ['book' => $item])</div>
                @endforeach
            </div>
        </section>
    @endif
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quantityInput = document.getElementById('quantity');
            const buyNowQuantity = document.querySelector('[data-buy-now-quantity]');

            if (quantityInput && buyNowQuantity) {
                const syncQuantity = function () {
                    buyNowQuantity.value = quantityInput.value;
                };

                quantityInput.addEventListener('input', syncQuantity);
                syncQuantity();
            }
        });
    </script>
@endsection
