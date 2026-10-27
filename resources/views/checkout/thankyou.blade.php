@extends('layouts.app') @section('title','Đặt hàng thành công | BookNest') @section('content')<div class="container page-container"><div class="success-card"><div class="success-icon"><i class="bi bi-check2"></i></div><span class="eyebrow">CẢM ƠN BẠN ĐÃ CHỌN BOOKNEST</span><h1>Đơn hàng đã <em>được ghi nhận!</em></h1><p>Mã đơn hàng của bạn là <strong>{{ $order->order_code }}</strong>. Hãy lưu lại mã này để tiện tra cứu.</p><div class="success-details"><div><span>Người nhận</span><b>{{ $order->customer_name }}</b></div><div><span>Địa chỉ</span><b>{{ $order->address }}</b></div><div><span>Trạng thái</span><b>Chờ xác nhận</b></div><div><span>Tổng thanh toán</span><b>{{ number_format($order->total,0,',','.') }}đ</b></div></div>@if($order->payment_method === 'bank')
<div class="bank-transfer-card mt-4 p-4" style="border:1px solid #e5e7eb;border-radius:16px;background:#fffaf3;text-align:center">
  <span class="eyebrow">THANH TOÁN CHUYỂN KHOẢN</span>
  <h3 class="mt-2">Quét mã QR để thanh toán</h3>
  <p class="text-muted">Mở ứng dụng ngân hàng MB hoặc ứng dụng ngân hàng hỗ trợ VietQR và quét mã bên dưới.</p>
  <img src="{{ $bankQrUrl }}" alt="Mã QR chuyển khoản BookNest" style="width:min(100%,320px);height:auto;background:#fff;border-radius:12px;padding:8px" loading="eager">
  <div class="text-start mt-3" style="max-width:420px;margin-inline:auto">
    <div class="summary-line"><span>Ngân hàng</span><b>MB Bank</b></div>
    <div class="summary-line"><span>Số tài khoản</span><b>0944323840</b></div>
    <div class="summary-line"><span>Chủ tài khoản</span><b>Đào Văn Phú</b></div>
    <div class="summary-line"><span>Số tiền</span><b>{{ number_format($order->total,0,',','.') }}đ</b></div>
    <div class="summary-line"><span>Nội dung chuyển khoản</span><b id="transfer-content">BOOKNEST {{ $order->order_code }}</b></div>
  </div>
  <button type="button" class="btn btn-outline-dark mt-3" onclick="navigator.clipboard.writeText(@js('BOOKNEST '.$order->order_code)).then(()=>{this.innerText='Đã sao chép nội dung';}).catch(()=>alert('Vui lòng sao chép nội dung chuyển khoản thủ công.'))"><i class="bi bi-copy me-1"></i> Sao chép nội dung</button>
  <div class="alert alert-warning text-start mt-3 mb-0"><strong>Lưu ý:</strong> Đơn hàng hiện đang chờ xác nhận. Việc hiển thị QR không xác nhận rằng tiền đã được chuyển thành công. BookNest chỉ xác nhận thanh toán sau khi kiểm tra giao dịch thực tế.</div>
</div>
@endif
<h4 class="mt-4">Chi tiết sản phẩm</h4>@foreach($order->items as $item)<div class="summary-line"><span>{{ $item->book_title }} × {{ $item->quantity }}</span><b>{{ number_format($item->line_total,0,',','.') }}đ</b></div>@endforeach<a href="{{ route('home') }}" class="btn btn-orange mt-4">Tiếp tục khám phá sách</a></div></div>@endsection
