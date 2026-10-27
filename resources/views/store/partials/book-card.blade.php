<div class="book-card">
    <a class="book-cover" href="{{ route('books.show', $book) }}">
        @if ($book->image_path)
            <img src="{{ route('book.image', ['path' => $book->image_path]) }}" alt="Bìa {{ $book->title }}" loading="lazy">
        @else
            <div class="cover-placeholder">
                <i class="bi bi-book-half"></i>
                <span>BookNest</span>
            </div>
        @endif
        @if ($book->is_featured)
            <span class="book-badge">Gợi ý</span>
        @endif
    </a>
    <div class="book-info">
        <div class="book-category">{{ $book->category?->name }}</div>
        <h3><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></h3>
        <p class="book-author">{{ $book->author }}</p>
        <div class="book-price">{{ number_format($book->price, 0, ',', '.') }}đ</div>
        <div class="stock-label {{ $book->stock > 0 ? 'in-stock' : 'out-stock' }}">
            <i class="bi bi-circle-fill"></i>
            {{ $book->stock > 0 ? 'Còn hàng · ' . $book->stock . ' quyển' : 'Hết hàng' }}
        </div>
        <div class="book-actions">
            <a class="btn btn-sm btn-outline-navy book-action-detail" href="{{ route('books.show', $book) }}">
                <i class="bi bi-eye"></i><span>Chi tiết</span>
            </a>
            @auth
                <form method="post" action="{{ route('cart.add', $book) }}" data-cart-add-form>
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-sm btn-outline-navy" type="submit" {{ $book->stock < 1 ? 'disabled' : '' }}>
                        <i class="bi bi-bag-plus"></i><span>Thêm giỏ</span>
                    </button>
                </form>
                <form method="post" action="{{ route('cart.buy-now', $book) }}" class="book-buy-now-form">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-sm btn-orange" type="submit" {{ $book->stock < 1 ? 'disabled' : '' }}>
                        <i class="bi bi-lightning-charge"></i><span>Mua ngay</span>
                    </button>
                </form>
            @else
                <a class="btn btn-sm btn-outline-navy" href="{{ route('login', ['redirect' => url()->current()]) }}">
                    <i class="bi bi-bag-plus"></i><span>Thêm giỏ</span>
                </a>
                <a class="btn btn-sm btn-orange" href="{{ route('login', ['redirect' => url()->current()]) }}">
                    <i class="bi bi-lightning-charge"></i><span>Mua ngay</span>
                </a>
            @endauth
        </div>
    </div>
</div>
