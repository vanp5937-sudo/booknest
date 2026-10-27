<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $ids = array_keys($cart);
        $books = Book::whereIn('id', $ids)->get()->keyBy('id');
        $total = 0;
        foreach ($cart as $id => $row) {
            if (isset($books[$id])) {
                $total += (float) $books[$id]->price * (int) $row['quantity'];
            }
        }
        return view('cart.index', compact('cart', 'books', 'total'));
    }

    public function add(Request $request, Book $book)
    {
        $data = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:20',
        ], [
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng mua tối thiểu là 1 quyển.',
            'quantity.max' => 'Mỗi lần mua tối đa 20 quyển.',
        ]);

        if ($book->status !== 'active' || $book->stock < 1) {
            return $this->cartAddError($request, 'Sách hiện đã hết hàng.', 422);
        }

        $cart = session('cart', []);
        $qty = (int) ($cart[$book->id]['quantity'] ?? 0) + (int) ($data['quantity'] ?? 1);

        if ($qty > $book->stock) {
            return $this->cartAddError($request, 'Số lượng tồn kho không đủ cho sách “'.$book->title.'”. Hiện còn '.$book->stock.' quyển.', 422);
        }

        $cart[$book->id] = ['quantity' => $qty];
        session(['cart' => $cart]);

        $count = array_sum(array_map(fn($row) => (int) ($row['quantity'] ?? 0), $cart));

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Đã thêm sách vào giỏ hàng.',
                'cartCount' => $count,
                'bookId' => $book->id,
                'quantity' => $qty,
            ]);
        }

        return back()->with('success', 'Đã thêm sách vào giỏ hàng.');
    }

    private function cartAddError(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message], $status);
        }
        return back()->with('error', $message);
    }

    public function buyNow(Request $request, Book $book)
    {
        $data = $request->validate(['quantity' => 'nullable|integer|min:1|max:20'], [
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng mua tối thiểu là 1 quyển.',
            'quantity.max' => 'Mỗi lần mua tối đa 20 quyển.',
        ]);
        if ($book->status !== 'active' || $book->stock < 1) {
            return back()->with('error', 'Sách hiện đã hết hàng.');
        }
        $qty = (int) ($data['quantity'] ?? 1);
        if ($qty > $book->stock) {
            return back()->with('error', 'Số lượng tồn kho không đủ cho sách “'.$book->title.'”. Hiện còn '.$book->stock.' quyển.');
        }
        session(['buy_now_cart' => [$book->id => ['quantity' => $qty]]]);

        return redirect()->route('checkout.show', ['mode' => 'buy-now']);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:0|max:100',
        ], [
            'quantities.required' => 'Không tìm thấy số lượng sách cần cập nhật.',
            'quantities.*.integer' => 'Số lượng phải là số nguyên.',
            'quantities.*.min' => 'Số lượng không được nhỏ hơn 0.',
            'quantities.*.max' => 'Số lượng cập nhật tối đa là 100 quyển.',
        ]);

        $cart = session('cart', []);
        $updatedCart = $cart;

        foreach ($data['quantities'] as $id => $quantity) {
            $quantity = (int) $quantity;

            if ($quantity === 0) {
                unset($updatedCart[$id]);
                continue;
            }

            $book = Book::find($id);

            if (!$book) {
                return back()->with('error', 'Một sản phẩm trong giỏ hàng không còn tồn tại. Vui lòng tải lại giỏ hàng.');
            }

            if ($book->status !== 'active') {
                return back()->with('error', 'Sách “' . $book->title . '” hiện không còn được bán.');
            }

            if ($quantity > (int) $book->stock) {
                return back()->with('error', 'Số lượng tồn kho không đủ cho sách “' . $book->title . '”. Hiện còn ' . $book->stock . ' quyển, bạn đang chọn ' . $quantity . ' quyển.');
            }

            $updatedCart[$id] = ['quantity' => $quantity];
        }

        session(['cart' => $updatedCart]);

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function remove(Book $book)
    {
        $cart = session('cart', []);
        unset($cart[$book->id]);
        session(['cart' => $cart]);
        return redirect()->route('cart.index')->with('success', 'Đã xóa sách khỏi giỏ hàng.');
    }
}
