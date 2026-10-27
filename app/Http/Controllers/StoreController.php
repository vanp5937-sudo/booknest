<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Support\BookSearch;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $booksQuery = Book::with('category')->where('status', 'active');
        $term = trim((string) $request->query('q', ''));

        if ($term !== '') {
            BookSearch::apply($booksQuery, $term);
        }

        $categorySlug = trim((string) $request->query('category', ''));
        if ($categorySlug !== '') {
            $booksQuery->whereHas('category', fn($query) => $query->where('slug', $categorySlug)->where('is_active', true));
        }

        $sort = (string) $request->query('sort', 'newest');
        if ($sort === 'price_asc') $booksQuery->orderBy('price')->orderBy('title');
        elseif ($sort === 'price_desc') $booksQuery->orderByDesc('price')->orderBy('title');
        elseif ($sort === 'name') $booksQuery->orderBy('title');
        else $booksQuery->latest('updated_at')->latest('id');

        $books = $booksQuery->paginate(10)->withQueryString();
        $featured = Book::with('category')->where('status', 'active')->where('is_featured', true)->take(4)->get();
        return view('store.index', compact('categories', 'books', 'featured'));
    }
    public function show(Book $book)
    {
        abort_unless($book->status === 'active', 404);
        $book->load('category');
        $related = Book::where('category_id', $book->category_id)->where('id', '!=', $book->id)->where('status', 'active')->take(4)->get();
        return view('store.show', compact('book', 'related'));
    }
}
