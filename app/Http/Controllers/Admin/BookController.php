<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('category')
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($sub) =>
                $sub->where('title', 'like', '%'.$request->q.'%')
                    ->orWhere('sku', 'like', '%'.$request->q.'%')
                    ->orWhere('author', 'like', '%'.$request->q.'%')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->category_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()->paginate(12)->withQueryString();

        $categories = Category::orderBy('name')->get();
        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        return view('admin.books.form', [
            'book' => new Book(),
            'categories' => Category::orderBy('name')->get(),
            'existingSkus' => Book::orderBy('sku')->pluck('sku'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['category_id'] = $this->resolveCategory($request, $data['category_id'] ?? null);
        unset($data['category_name']);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? null) ?: $data['title']);
        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->storePublicly('books', 'public');
            if (!$storedPath) {
                return back()->withInput()->withErrors(['image' => 'Không lưu được ảnh bìa. Hãy kiểm tra quyền ghi thư mục storage/app/public.']);
            }
            $data['image_path'] = $storedPath;
        }
        $data['is_featured'] = $request->boolean('is_featured');
        $book = Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Đã thêm sách “' . $book->title . '” thành công.');
    }

    public function edit(Book $book)
    {
        return view('admin.books.form', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
            'existingSkus' => Book::where('id', '!=', $book->id)->orderBy('sku')->pluck('sku'),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $data = $this->validated($request, $book);
        $data['category_id'] = $this->resolveCategory($request, $data['category_id'] ?? null);
        unset($data['category_name']);
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? null) ?: $data['title'], $book->id);

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->storePublicly('books', 'public');
            if ($newPath === false) {
                return back()->withInput()->withErrors(['image' => 'Không thể lưu ảnh. Hãy kiểm tra quyền ghi thư mục storage/app/public.']);
            }
            if ($book->image_path && !str_starts_with($book->image_path, 'images/')) {
                Storage::disk('public')->delete($book->image_path);
            }
            $data['image_path'] = $newPath;
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $book->update($data);
        return redirect()->route('admin.books.index')->with('success', 'Đã cập nhật sách “' . $book->title . '” thành công.');
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        $data = $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id|required_without:category_name',
            'category_name' => 'nullable|string|max:120|required_without:category_id',
            'sku' => 'required|string|max:50|unique:books,sku'.($book ? ','.$book->id : ''),
            'title' => 'required|string|max:255|unique:books,title'.($book ? ','.$book->id : ''),
            'slug' => 'nullable|string|max:280|unique:books,slug'.($book ? ','.$book->id : ''),
            'author' => 'required|string|max:180',
            'publisher' => 'nullable|string|max:180',
            'isbn' => 'nullable|string|max:20|unique:books,isbn'.($book ? ','.$book->id : ''),
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_featured' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tên sách.',
            'title.unique' => 'Tên sách này đã tồn tại trong hệ thống.',
            'author.required' => 'Vui lòng nhập tên tác giả.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là một số hợp lệ.',
            'price.min' => 'Giá bán không được âm.',
            'original_price.numeric' => 'Giá gốc phải là một số hợp lệ.',
            'original_price.min' => 'Giá gốc không được âm.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock.min' => 'Số lượng tồn kho không được âm.',
            'status.in' => 'Trạng thái sách không hợp lệ.',
            'sku.unique' => 'Mã sách này đã tồn tại. Hãy chọn mã khác hoặc nhập mã mới.',
            'image.file' => 'Tệp tải lên không phải là ảnh hợp lệ. Vui lòng chọn ảnh JPG, JPEG, PNG hoặc WEBP.',
            'image.image' => 'Tệp tải lên không phải là ảnh hợp lệ. Vui lòng chọn ảnh JPG, JPEG, PNG hoặc WEBP.',
            'image.mimes' => 'Tệp tải lên không phải là ảnh hợp lệ. Vui lòng chọn ảnh JPG, JPEG, PNG hoặc WEBP.',
            'image.max' => 'Ảnh bìa vượt quá 5 MB. Vui lòng chọn ảnh có dung lượng tối đa 5 MB.',
            'image.uploaded' => 'Ảnh chưa tải lên được. Kiểm tra giới hạn upload_max_filesize và post_max_size trong PHP/Laragon.',
        ]);
        if ($request->hasFile('image') && !$request->file('image')->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['image' => 'Tải ảnh thất bại. Kiểm tra dung lượng ảnh và cấu hình PHP upload_max_filesize/post_max_size.']);
        }
        return $data;
    }

    private function resolveCategory(Request $request, ?int $categoryId): int
    {
        $name = trim((string) $request->input('category_name', ''));
        if ($name === '') return (int) $categoryId;

        $existing = Category::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        if ($existing) return $existing->id;

        $base = Str::slug($name) ?: 'danh-muc';
        $slug = $base;
        $index = 2;
        while (Category::where('slug', $slug)->exists()) $slug = $base.'-'.$index++;

        return Category::create([
            'name' => $name,
            'slug' => $slug,
            'description' => 'Danh mục được tạo từ biểu mẫu thêm sách.',
            'is_active' => true,
        ])->id;
    }

    private function uniqueSlug(string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title) ?: 'sach';
        $slug = $base;
        $index = 2;
        while (Book::where('slug', $slug)->when($ignore, fn ($query) => $query->where('id', '!=', $ignore))->exists()) {
            $slug = $base.'-'.$index++;
        }
        return $slug;
    }

    public function destroy(Book $book)
    {
        if ($book->image_path && !str_starts_with($book->image_path, 'images/')) {
            Storage::disk('public')->delete($book->image_path);
        }
        $title = $book->title;
        $book->delete();
        return back()->with('success', 'Đã xóa sách “' . $title . '” thành công.');
    }
}
