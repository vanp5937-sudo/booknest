@extends('layouts.app') @section('title','BookNest | Mở trang sách, mở chân trời') @section('content')
<section class="hero-wrap">
<div class="container">
  <div id="bookHero" class="carousel slide hero-card" data-bs-ride="carousel" data-bs-interval="10000" data-bs-pause="false" data-bs-touch="true" aria-label="Banner giới thiệu BookNest">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#bookHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Hiển thị banner 1"></button>
      <button type="button" data-bs-target="#bookHero" data-bs-slide-to="1" aria-label="Hiển thị banner 2"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img class="hero-banner-image" src="{{ asset('images/banner/banner1.png') }}" alt="Không gian sách ấm áp của BookNest">
        <div class="hero-html-overlay"><span class="eyebrow">MỞ TRANG SÁCH, MỞ CHÂN TRỜI</span><h1>Mỗi cuốn sách<br>là một <em>khởi đầu mới.</em></h1><p>Từ công nghệ, kỹ năng sống đến văn học và truyện tranh – tìm cuốn sách dành riêng cho bạn.</p><a href="{{ route('products') }}" class="btn btn-orange">Khám phá sách <i class="bi bi-arrow-right ms-2"></i></a><div class="hero-note"><i class="bi bi-heart"></i>Đọc một chút, trưởng thành mỗi ngày</div></div>
      </div>
      <div class="carousel-item">
        <img class="hero-banner-image" src="{{ asset('images/banner/banner2.png') }}" alt="Khám phá các danh mục sách tại BookNest">
        <div class="hero-html-overlay"><span class="eyebrow">GÓC NHỎ DÀNH CHO NGƯỜI MÊ SÁCH</span><h1>Tìm thấy cuốn sách<br><em>hợp với bạn.</em></h1><p>Khám phá nhiều thể loại sách được chọn lọc, cùng bạn học hỏi và nuôi dưỡng cảm hứng mỗi ngày.</p><a href="{{ route('products') }}" class="btn btn-orange">Xem danh mục <i class="bi bi-arrow-right ms-2"></i></a><div class="hero-note"><i class="bi bi-book-heart"></i>Một cuốn sách hay, một hành trình mới</div></div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#bookHero" data-bs-slide="prev" aria-label="Banner trước"><span class="hero-arrow-icon"><i class="bi bi-chevron-left"></i></span><span class="visually-hidden">Trước</span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#bookHero" data-bs-slide="next" aria-label="Banner tiếp theo"><span class="hero-arrow-icon"><i class="bi bi-chevron-right"></i></span><span class="visually-hidden">Tiếp</span></button>
  </div>
</div>
</section>
<section class="benefit-row"><div class="container"><div class="row g-3"><div class="col-6 col-lg-3"><div class="benefit"><i class="bi bi-truck"></i><div><b>Giao hàng tận nơi</b><small>Nhận sách ngay tại nhà</small></div></div></div><div class="col-6 col-lg-3"><div class="benefit"><i class="bi bi-shield-check"></i><div><b>Thanh toán an tâm</b><small>Thanh toán khi nhận hàng</small></div></div></div><div class="col-6 col-lg-3"><div class="benefit"><i class="bi bi-book"></i><div><b>Đa dạng thể loại</b><small>Chọn sách theo sở thích</small></div></div></div><div class="col-6 col-lg-3"><div class="benefit"><i class="bi bi-chat-heart"></i><div><b>Luôn đồng hành</b><small>Hỗ trợ bạn mỗi ngày</small></div></div></div></div></div></section>
<section class="section-block" id="categories"><div class="container"><div class="section-heading"><div><span class="eyebrow">TÌM THEO SỞ THÍCH</span><h2>Khám phá <em>thế giới sách</em></h2><p>Một danh mục phù hợp, một khởi đầu đầy cảm hứng.</p></div><a class="text-link" href="#catalog">Tất cả sách <i class="bi bi-arrow-right"></i></a></div><div class="category-grid">@foreach($categories as $category)<a class="category-chip {{ request('category')===$category->slug?'active':'' }}" href="{{ route('products',['category'=>$category->slug]) }}"><span><i class="bi {{ ['bi-journal-richtext','bi-stars','bi-graph-up-arrow','bi-code-slash','bi-translate','bi-mortarboard','bi-book-half','bi-balloon'][($loop->index)%8] }}"></i></span><b>{{ $category->name }}</b><i class="bi bi-arrow-up-right"></i></a>@endforeach</div></div></section>
@if($featured->isNotEmpty())<section class="section-block featured-section"><div class="container"><div class="section-heading"><div><span class="eyebrow">ĐƯỢC BOOKNEST GỢI Ý</span><h2>Những cuốn sách <em>nổi bật</em></h2><p>Lựa chọn đáng đọc cho hành trình phát triển bản thân.</p></div></div><div class="row g-4">@foreach($featured as $book)<div class="col-6 col-lg-3">@include('store.partials.book-card',['book'=>$book])</div>@endforeach</div></div></section>@endif
<section class="newsletter"><div class="container"><div><span class="eyebrow">MỘT CHÚT CẢM HỨNG MỖI NGÀY</span><h2>Để sách đưa bạn <em>đi xa hơn.</em></h2><p>BookNest – nơi hành trình đọc sách bắt đầu từ những lựa chọn nhỏ.</p></div><a href="{{ route('products') }}" class="btn btn-orange">Khám phá kho sách <i class="bi bi-arrow-right ms-2"></i></a></div></section>@endsection
