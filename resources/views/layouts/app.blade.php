<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','BookNest - Nhà sách trực tuyến')</title>
<meta name="description" content="BookNest - Góc nhỏ dành cho người mê sách.">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/booknest.css') }}">
</head>
<body>
<div class="top-strip"><i class="bi bi-book-heart me-2"></i>Gửi trao tri thức · Phí vận chuyển tạm tính 20.000đ</div>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top main-nav"><div class="container">
<a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}"><img src="{{ asset('images/logo/logo.png') }}" alt="Logo BookNest" class="brand-logo"><span>Book<span class="brand-accent">Nest</span><small>Góc nhỏ của người mê sách</small></span></a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Mở menu"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="mainNav">
<ul class="navbar-nav main-menu mx-lg-auto">
<li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
<li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Giới thiệu</a></li>
<li class="nav-item"><a class="nav-link {{ request()->routeIs('products') || request()->routeIs('books.show') ? 'active' : '' }}" href="{{ route('products') }}">Sản phẩm</a></li>
<li class="nav-item"><a class="nav-link {{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a></li>
<li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Liên hệ</a></li>
</ul>
<form class="nav-search my-3 my-lg-0" method="get" action="{{ route('products') }}"><i class="bi bi-search"></i><input name="q" value="{{ request('q') }}" placeholder="Tìm tên sách, tác giả, mã sách..." aria-label="Tìm sách"><button type="submit">Tìm sách</button></form>
<div class="nav-actions d-flex align-items-center gap-3 ms-lg-3"><a href="{{ route('cart.index') }}" class="nav-icon" aria-label="Giỏ hàng"><i class="bi bi-bag"></i><span class="nav-label">Giỏ hàng</span><b id="nav-cart-count">{{ collect(session('cart',[]))->sum('quantity') }}</b></a>
@auth <div class="dropdown"><button class="btn btn-link nav-icon dropdown-toggle text-decoration-none" data-bs-toggle="dropdown"><i class="bi bi-person"></i><span class="nav-label">{{ Str::limit(auth()->user()->name,12) }}</span></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="{{ route('orders.mine') }}">Đơn hàng của tôi</a></li>@if(auth()->user()->role==='admin')<li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Quản trị Admin</a></li>@endif<li><hr class="dropdown-divider"></li><li><form method="post" action="{{ route('logout') }}">@csrf<button class="dropdown-item">Đăng xuất</button></form></li></ul></div>
@else<a class="nav-icon" href="{{ route('login') }}"><i class="bi bi-person"></i><span class="nav-label">Tài khoản</span></a>@endauth
</div></div></div></nav>
<main>@if(session('success'))<div class="container pt-3"><div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button></div></div>@endif @if(session('error'))<div class="container pt-3"><div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button></div></div>@endif @yield('content')</main>
<footer class="site-footer"><div class="container"><div class="row g-4"><div class="col-md-5"><a class="footer-brand-wrap" href="{{ route('home') }}"><img src="{{ asset('images/logo/logo.png') }}" alt="Logo BookNest"><span class="footer-brand">Book<span>Nest</span></span></a><p>Không chỉ là một cuốn sách, mà còn là một hành trình mới. Cùng BookNest tìm thấy câu chuyện dành riêng cho bạn.</p></div><div class="col-md-3"><h6>Khám phá</h6><a href="{{ route('products') }}">Tất cả sách</a><a href="{{ route('products',['category'=>'cong-nghe-lap-trinh']) }}">Sách công nghệ</a><a href="{{ route('products',['category'=>'van-hoc-tieu-thuyet']) }}">Văn học</a><a href="{{ route('blog.index') }}">Blog đọc sách</a></div><div class="col-md-4"><h6>BookNest hỗ trợ</h6><p><i class="bi bi-envelope me-2"></i>hello@booknest.local</p><p><i class="bi bi-clock me-2"></i>Hỗ trợ: 8:00 – 21:00</p><a href="{{ route('contact') }}">Gửi lời nhắn cho BookNest</a></div></div><div class="footer-bottom">© {{ date('Y') }} BookNest · Đồ án Lập trình mã nguồn mở</div></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('input', function (event) {
    if (event.target && event.target.id === 'quantity') {
        const buyNowQuantity = document.querySelector('.buy-now-detail-form input[name="quantity"]');
        if (buyNowQuantity) {
            buyNowQuantity.value = event.target.value;
        }
    }
});

document.addEventListener('submit', async function (event) {
    const form = event.target.closest('form[data-cart-add-form]');
    if (!form) return;

    event.preventDefault();
    const button = form.querySelector('button[type="submit"], button:not([type])');
    if (button && button.disabled) return;

    const originalHtml = button ? button.innerHTML : '';
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Đang thêm';
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        const result = await response.json();

        if (!response.ok || !result.ok) {
            throw new Error(result.message || 'Chưa thể thêm sách vào giỏ hàng.');
        }

        const badge = document.getElementById('nav-cart-count');
        if (badge) badge.textContent = result.cartCount;

        let notice = document.getElementById('booknest-cart-notice');
        if (!notice) {
            notice = document.createElement('div');
            notice.id = 'booknest-cart-notice';
            notice.setAttribute('role', 'status');
            notice.setAttribute('aria-live', 'polite');
            notice.style.cssText = 'position:fixed;right:22px;top:105px;z-index:2000;max-width:min(360px,calc(100vw - 32px));padding:13px 18px;border-radius:12px;background:#edf8f0;color:#216b43;border:1px solid #c7e8d1;box-shadow:0 8px 28px rgba(30,30,30,.12);font-weight:600;';
            document.body.appendChild(notice);
        }
        notice.textContent = '✓ ' + result.message;
        notice.style.display = 'block';
        if (notice._hideTimer) clearTimeout(notice._hideTimer);
        notice._hideTimer = setTimeout(() => { notice.style.display = 'none'; }, 3200);
    } catch (error) {
        let notice = document.getElementById('booknest-cart-notice');
        if (!notice) {
            notice = document.createElement('div');
            notice.id = 'booknest-cart-notice';
            notice.setAttribute('role', 'alert');
            notice.style.cssText = 'position:fixed;right:22px;top:105px;z-index:2000;max-width:min(360px,calc(100vw - 32px));padding:13px 18px;border-radius:12px;background:#fff0f0;color:#9c2435;border:1px solid #f0c7cc;box-shadow:0 8px 28px rgba(30,30,30,.12);font-weight:600;';
            document.body.appendChild(notice);
        }
        notice.textContent = error.message || 'Có lỗi xảy ra khi thêm sách.';
        notice.style.display = 'block';
        if (notice._hideTimer) clearTimeout(notice._hideTimer);
        notice._hideTimer = setTimeout(() => { notice.style.display = 'none'; }, 4000);
    } finally {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalHtml;
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stockInputs = document.querySelectorAll('input.quantity-input[max], input#quantity[max]');

    const validateStockInput = function (input) {
        input.setCustomValidity('');

        if (input.value.trim() === '') {
            return;
        }

        const quantity = Number(input.value);
        const stock = Number(input.max);
        const bookTitle = input.dataset.stockTitle || 'sản phẩm này';

        if (Number.isFinite(quantity) && Number.isFinite(stock) && quantity > stock) {
            input.setCustomValidity('Số lượng tồn kho không đủ cho sách “' + bookTitle + '”. Hiện còn ' + stock + ' quyển.');
        }
    };

    stockInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            validateStockInput(input);
        });
        input.addEventListener('change', function () {
            validateStockInput(input);
        });
        input.addEventListener('invalid', function () {
            validateStockInput(input);
        });
        validateStockInput(input);
    });
});
</script>
</body></html>