<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Support\BookSearch;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function products(Request $request)
    {

        $term = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('category', ''));
        $sort = (string) $request->query('sort', 'newest');
        $allowedSorts = ['newest', 'price_asc', 'price_desc', 'name'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'newest';
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $booksQuery = Book::with('category')->where('status', 'active');

        if ($term !== '') {

            BookSearch::apply($booksQuery, $term);
        }

        if ($categorySlug !== '') {
            $booksQuery->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug)->where('is_active', true);
            });
        }

        switch ($sort) {
            case 'price_asc':
                $booksQuery->orderBy('price')->orderBy('title');
                break;
            case 'price_desc':
                $booksQuery->orderByDesc('price')->orderBy('title');
                break;
            case 'name':
                $booksQuery->orderBy('title');
                break;
            default:
                $booksQuery->latest('updated_at')->latest('id');
                break;
        }

        $books = $booksQuery->paginate(10)->withQueryString();

        return view('pages.products', compact('categories', 'books'));
    }

    public function blog()
    {
        $articles = [
            ['category' => 'Thói quen đọc', 'title' => 'Bắt đầu thói quen đọc sách từ 15 phút mỗi ngày', 'excerpt' => 'Một khoảng thời gian ngắn nhưng đều đặn có thể giúp việc đọc trở thành phần tự nhiên trong lịch trình của bạn.', 'icon' => 'bi-book-half', 'image' => 'images/blog/thoi_quen_doc.png', 'date' => 'Cẩm nang đọc sách'],
            ['category' => 'Chọn sách', 'title' => 'Cách chọn cuốn sách phù hợp với mục tiêu của bạn', 'excerpt' => 'Từ học tập, phát triển bản thân đến giải trí, hãy bắt đầu bằng điều bạn muốn khám phá lúc này.', 'icon' => 'bi-compass', 'image' => 'images/blog/chon_sach.png', 'date' => 'Gợi ý từ BookNest'],
            ['category' => 'Góc học tập', 'title' => 'Sắp xếp góc đọc nhỏ để dễ tập trung hơn', 'excerpt' => 'Ánh sáng vừa đủ, chỗ ngồi thoải mái và một cuốn sách đang đọc sẽ giúp bạn dễ bắt đầu hơn.', 'icon' => 'bi-house-heart', 'image' => 'images/blog/goc_hoc_tap.png', 'date' => 'Cảm hứng mỗi ngày'],
            ['category' => 'Sách và cuộc sống', 'title' => 'Ghi chú sau khi đọc: giữ lại điều thật sự hữu ích', 'excerpt' => 'Thử viết lại một ý tưởng đáng nhớ và một việc nhỏ bạn muốn áp dụng sau mỗi lần đọc.', 'icon' => 'bi-journal-text', 'image' => 'images/blog/sach_va_cuoc_song.png', 'date' => 'Mẹo đọc hiệu quả'],
        ];
        return view('pages.blog', compact('articles'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email chưa đúng định dạng.',
            'message.required' => 'Vui lòng nhập nội dung lời nhắn.',
            'message.min' => 'Nội dung cần ít nhất 10 ký tự.',
        ]);
        return back()->with('success', 'BookNest đã nhận được lời nhắn của bạn. Cảm ơn bạn đã liên hệ!');
    }
}
