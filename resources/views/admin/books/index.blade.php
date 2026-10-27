@extends('admin.layout') @section('title','Quản lý sách | BookNest Admin') @section('page-heading','Quản lý sách') @section('content')<div class="admin-panel">
    <div class="panel-heading">
        <div>
            <h3>Danh sách sách</h3>
            <p>{{ $books->total() }} sản phẩm trong hệ thống</p>
        </div><a href="{{ route('admin.books.create') }}" class="btn btn-orange"><i class="bi bi-plus-lg me-1"></i> Thêm sách</a>
    </div>
    <form class="admin-filter" method="get"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Tìm tên sách, mã SKU, tác giả"><select class="form-select" name="category_id">
            <option value="">Tất cả danh mục</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id')==$c->id)>{{ $c->name }}</option>@endforeach
        </select><select class="form-select" name="status">
            <option value="">Mọi trạng thái</option>
            <option value="active" @selected(request('status')==='active' )>Đang bán</option>
            <option value="inactive" @selected(request('status')==='inactive' )>Ngừng bán</option>
        </select><button class="btn btn-navy">Lọc</button></form>
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>Sách</th>
                    <th>SKU</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>@forelse($books as $book)<tr>
                    <td>
                        <div class="admin-book-cell">@if (!empty($book->image_path))
                            <img
                                src="{{ route('book.image', ['path' => $book->image_path]) }}"
                                alt="{{ $book->title }}">
                            @else
                            <span>Chưa có ảnh bìa</span>
                            @endif
                            <div><b>{{ $book->title }}</b><small>{{ $book->author }}</small></div>
                        </div>
                    </td>
                    <td>{{ $book->sku }}</td>
                    <td>{{ $book->category->name }}</td>
                    <td>{{ number_format($book->price,0,',','.') }}đ</td>
                    <td>{{ $book->stock }}</td>
                    <td><span class="status-pill {{ $book->status==='active'?'status-good':'status-muted' }}">{{ $book->status==='active'?'Đang bán':'Ngừng bán' }}</span></td>
                    <td>
                        <div class="d-flex gap-1"><a href="{{ route('admin.books.edit',$book) }}" class="btn btn-light btn-sm" title="Sửa"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="{{ route('admin.books.destroy',$book) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa sách này?')">@csrf @method('DELETE')<button class="btn btn-light btn-sm text-danger" title="Xóa"><i class="bi bi-trash"></i></button></form>
                        </div>
                    </td>
                </tr>@empty<tr>
                    <td colspan="7" class="text-center py-4">Không có sách phù hợp.</td>
                </tr>@endforelse</tbody>
        </table>
    </div>
    <div class="px-3 pb-3">{{ $books->links() }}</div>
</div>@endsection