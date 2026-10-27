<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('name')->paginate(12);
        return view('admin.categories.index', compact('categories'));
    }
    public function create()
    {
        return view('admin.categories.form', ['category' => new Category]);
    }
    public function store(Request $r)
    {
        $d = $r->validate(['name' => 'required|string|max:150', 'slug' => 'nullable|string|max:180|unique:categories,slug', 'description' => 'nullable|string', 'is_active' => 'nullable|boolean']);
        $d['slug'] = Str::slug(($d['slug'] ?? null) ?: $d['name']);
        if ($d['slug'] === '') $d['slug'] = 'danh-muc';
        $base = $d['slug'];
        $suffix = 2;
        while (Category::where('slug', $d['slug'])->exists()) $d['slug'] = $base . '-' . $suffix++;
        $d['is_active'] = $r->boolean('is_active');
        Category::create($d);
        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục “' . $d['name'] . '” thành công.');
    }
    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }
    public function update(Request $r, Category $category)
    {
        $d = $r->validate(['name' => 'required|string|max:150', 'slug' => 'nullable|string|max:180|unique:categories,slug,' . $category->id, 'description' => 'nullable|string', 'is_active' => 'nullable|boolean']);
        $d['slug'] = Str::slug(($d['slug'] ?? null) ?: $d['name']);
        if ($d['slug'] === '') $d['slug'] = 'danh-muc';
        $base = $d['slug'];
        $suffix = 2;
        while (Category::where('slug', $d['slug'])->where('id', '!=', $category->id)->exists()) $d['slug'] = $base . '-' . $suffix++;
        $d['is_active'] = $r->boolean('is_active');
        $category->update($d);
        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục “' . $d['name'] . '” thành công.');
    }
    public function destroy(Category $category)
    {
        if ($category->books()->exists()) return back()->with('error', 'Danh mục còn sách, không thể xóa.');
        $name = $category->name;
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục “' . $name . '” thành công.');
    }
}
