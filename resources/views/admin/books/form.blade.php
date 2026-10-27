@extends('admin.layout')
@section('title', ($book->exists ? 'Sửa sách' : 'Thêm sách') . ' | BookNest')
@section('page-heading', $book->exists ? 'Cập nhật sách' : 'Thêm sách mới')
@section('content')
<form id="book-form" method="POST" novalidate enctype="multipart/form-data" action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}">
    @csrf
    @if($book->exists) @method('PUT') @endif
    <div class="admin-panel form-panel">
        <div class="panel-heading">
            <div><h3>Thông tin sách</h3><p>Nhập thông tin sách và ảnh bìa. Các trường có dấu * là bắt buộc.</p></div>
            <a href="{{ route('admin.books.index') }}" class="btn btn-light">Quay lại</a>
        </div>
        @if($errors->any())
            <div class="alert alert-danger m-3"><strong>Chưa lưu được sách.</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="row g-3 p-3">
            <div class="col-md-8"><label class="form-label">Tên sách *</label><input class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $book->title) }}" required maxlength="255">@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Mã sách / SKU *</label><input class="form-control @error('sku') is-invalid @enderror" name="sku" list="book-sku-list" value="{{ old('sku', $book->sku) }}" required maxlength="50" placeholder="Nhập mã mới, ví dụ BN-VH-004" autocomplete="off"><datalist id="book-sku-list">@foreach($existingSkus as $sku)<option value="{{ $sku }}">@endforeach</datalist><small class="text-muted">Có thể gõ mã mới hoặc mở mũi tên để xem mã cũ. Mã đã dùng không thể trùng với sách khác.</small>@error('sku')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label">Danh mục có sẵn</label><select class="form-select" name="category_id" id="category_id"><option value="">-- Chọn danh mục --</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select><small class="text-muted">Chọn loại sách đã có trong hệ thống.</small></div>
            <div class="col-md-6"><label class="form-label">Hoặc tạo loại sách mới</label><input class="form-control" name="category_name" id="category_name" value="{{ old('category_name') }}" maxlength="120" placeholder="Ví dụ: Tâm lý học, Thiết kế..."><small class="text-muted">Nhập tên mới nếu chưa có trong danh mục; hệ thống sẽ tạo danh mục khi lưu.</small></div>
            <div class="col-md-6"><label class="form-label">Tác giả *</label><input class="form-control" name="author" value="{{ old('author', $book->author) }}" required maxlength="180"></div>
            <div class="col-md-6"><label class="form-label">Nhà xuất bản</label><input class="form-control" name="publisher" value="{{ old('publisher', $book->publisher) }}" maxlength="180"></div>
            <div class="col-md-6"><label class="form-label">ISBN (nếu có)</label><input class="form-control" name="isbn" value="{{ old('isbn', $book->isbn) }}" maxlength="20"></div>
            <div class="col-md-4"><label class="form-label">Giá bán (VNĐ) *</label><input class="form-control @error('price') is-invalid @enderror" type="number" min="0" step="any" name="price" id="book-price" value="{{ old('price', $book->price) }}" required aria-describedby="price-error">
                @error('price')<div class="invalid-feedback d-block" id="price-error">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Giá gốc (VNĐ)</label><input class="form-control @error('original_price') is-invalid @enderror" type="number" min="0" step="any" name="original_price" id="book-original-price" value="{{ old('original_price', $book->original_price) }}">
                @error('original_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Tồn kho *</label><input class="form-control @error('stock') is-invalid @enderror" type="number" min="0" step="1" name="stock" id="book-stock" value="{{ old('stock', $book->stock ?? 0) }}" required>
                @error('stock')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div class="col-md-8">
                <label class="form-label">Ảnh bìa (JPG, PNG, WEBP – tối đa 5 MB)</label>
                <input class="form-control @error('image') is-invalid @enderror" type="file" name="image" id="book-image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <small class="text-muted d-block mt-1">Chỉ chấp nhận ảnh JPG, JPEG, PNG hoặc WEBP có dung lượng tối đa 5 MB.</small>
                @error('image')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4"><label class="form-label">Trạng thái</label><select class="form-select" name="status"><option value="active" @selected(old('status', $book->status ?? 'active') === 'active')>Đang bán</option><option value="inactive" @selected(old('status', $book->status) === 'inactive')>Ngừng bán</option></select></div>
            <div class="col-12">
                <div class="d-flex flex-wrap align-items-start gap-3">
                    <div><div class="form-label">Ảnh xem trước</div><img id="book-image-preview" src="{{ $book->image_path ? route('book.image', ['path' => $book->image_path]) : '' }}" alt="Xem trước ảnh bìa" style="{{ $book->image_path ? '' : 'display:none;' }}max-width:150px;max-height:190px;object-fit:contain;border:1px solid #eadbd6;border-radius:10px;padding:5px;background:#fff;"></div>
                    @if($book->image_path)<div class="small text-muted mt-4">Đang sử dụng: {{ $book->image_path }}. Nếu không chọn ảnh mới, ảnh hiện tại được giữ nguyên.</div>@endif
                </div>
            </div>
            <div class="col-12"><label class="form-label">Mô tả</label><textarea class="form-control" name="description" rows="4">{{ old('description', $book->description) }}</textarea></div>
            <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featured" @checked(old('is_featured', $book->is_featured))><label class="form-check-label" for="featured">Hiển thị trong sách nổi bật</label></div></div>
        </div>
        <div class="form-footer"><button type="submit" class="btn btn-orange"><i class="bi bi-check2-circle me-1"></i>{{ $book->exists ? 'Lưu thay đổi' : 'Thêm sách' }}</button><a href="{{ route('admin.books.index') }}" class="btn btn-light ms-2">Hủy</a></div>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('book-form');
    const categorySelect = document.getElementById('category_id');
    const categoryName = document.getElementById('category_name');
    const imageInput = document.getElementById('book-image');
    const preview = document.getElementById('book-image-preview');
    const priceInput = document.getElementById('book-price');
    const originalPriceInput = document.getElementById('book-original-price');
    const stockInput = document.getElementById('book-stock');
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxImageSize = 5 * 1024 * 1024;

    categoryName.addEventListener('input', function () {
        if (this.value.trim() !== '') categorySelect.value = '';
    });

    categorySelect.addEventListener('change', function () {
        if (this.value !== '') categoryName.value = '';
    });

    [priceInput, originalPriceInput, stockInput].forEach(function (input) {
        if (!input) return;
        input.addEventListener('input', function () {
            this.setCustomValidity('');
        });
    });

    imageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            alert('Tệp tải lên không phải là ảnh hợp lệ. Vui lòng chọn ảnh JPG, JPEG, PNG hoặc WEBP.');
            this.value = '';
            return;
        }

        if (file.size > maxImageSize) {
            alert('Ảnh bìa vượt quá 5 MB. Vui lòng chọn ảnh có dung lượng tối đa 5 MB.');
            this.value = '';
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });

    form.addEventListener('submit', function (event) {
        [priceInput, originalPriceInput, stockInput].forEach(function (input) {
            if (input) input.setCustomValidity('');
        });

        if (priceInput.value.trim() === '') {
            priceInput.setCustomValidity('Vui lòng nhập giá bán.');
        } else if (!Number.isFinite(Number(priceInput.value))) {
            priceInput.setCustomValidity('Giá bán phải là một số hợp lệ.');
        } else if (Number(priceInput.value) < 0) {
            priceInput.setCustomValidity('Giá bán không được âm.');
        }

        if (originalPriceInput.value.trim() !== '' && Number(originalPriceInput.value) < 0) {
            originalPriceInput.setCustomValidity('Giá gốc không được âm.');
        }

        if (stockInput.value.trim() === '') {
            stockInput.setCustomValidity('Vui lòng nhập số lượng tồn kho.');
        } else if (!Number.isInteger(Number(stockInput.value))) {
            stockInput.setCustomValidity('Số lượng tồn kho phải là số nguyên.');
        } else if (Number(stockInput.value) < 0) {
            stockInput.setCustomValidity('Số lượng tồn kho không được âm.');
        }

        const invalidInput = form.querySelector('input:invalid, select:invalid, textarea:invalid');
        if (invalidInput) {
            event.preventDefault();
            invalidInput.reportValidity();
            invalidInput.focus();
        }
    });

    const firstError = form.querySelector('.is-invalid');
    if (firstError) firstError.focus({ preventScroll: false });
});
</script>
@endsection
