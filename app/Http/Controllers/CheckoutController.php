<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private const SHIPPING_FEE = 20000;

    public function show(Request $request)
    {
        $purchaseMode = $request->query('mode') === 'buy-now' ? 'buy-now' : 'cart';
        $cart = $purchaseMode === 'buy-now'
            ? session('buy_now_cart', [])
            : session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }
        $books = Book::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total = 0;
        foreach ($cart as $id => $row) {
            if (isset($books[$id])) {
                $total += (float) $books[$id]->price * (int) $row['quantity'];
            }
        }
        $shippingFee = self::SHIPPING_FEE;
        $grandTotal = $total + $shippingFee;
        return view('checkout.show', compact('cart', 'books', 'total', 'shippingFee', 'grandTotal', 'purchaseMode'));
    }

    public function place(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:150',
            'email' => 'required|email|max:180',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:1000',
            'payment_method' => 'required|in:cod,bank',
            'note' => 'nullable|string|max:1000',
        ]);
        $purchaseMode = $request->input('purchase_mode') === 'buy-now' ? 'buy-now' : 'cart';
        $cart = $purchaseMode === 'buy-now'
            ? session('buy_now_cart', [])
            : session('cart', []);

        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $order = null;
        try {
            DB::transaction(function () use ($cart, $data, &$order) {
            $ids = array_keys($cart);
            $books = Book::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');
            $subtotal = 0;

            foreach ($cart as $id => $row) {
                if (!isset($books[$id]) || $books[$id]->status !== 'active' || $books[$id]->stock < (int) $row['quantity']) {
                    throw new \RuntimeException('Sách đã hết hàng hoặc số lượng tồn kho không đủ.');
                }
                $subtotal += (float) $books[$id]->price * (int) $row['quantity'];
            }

            $shippingFee = self::SHIPPING_FEE;
            $grandTotal = $subtotal + $shippingFee;

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_code' => 'BN' . now()->format('ymdHis') . random_int(10, 99),
                'customer_name' => $data['customer_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'subtotal' => $subtotal,
                'total' => $grandTotal,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($cart as $id => $row) {
                $book = $books[$id];
                $qty = (int) $row['quantity'];
                $order->items()->create([
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'sku' => $book->sku,
                    'quantity' => $qty,
                    'unit_price' => $book->price,
                    'line_total' => (float) $book->price * $qty,
                ]);
                $book->decrement('stock', $qty);
            }
            });
        } catch (\RuntimeException $exception) {
            return redirect()->route('checkout.show', $purchaseMode === 'buy-now' ? ['mode' => 'buy-now'] : [])
                ->with('error', $exception->getMessage());
        }

        session()->forget($purchaseMode === 'buy-now' ? 'buy_now_cart' : 'cart');

        return redirect()->route('orders.thankyou', $order)->with('success', 'Đặt hàng thành công!');
    }

    public function thankyou(Order $order)
    {
        if (auth()->check() && auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->load('items');
        $bankQrUrl = null;
        if ($order->payment_method === 'bank') {
            $bankQrUrl = 'https://img.vietqr.io/image/MB-0944323840-compact2.png?amount=' . (int) round((float) $order->total)
                . '&addInfo=' . rawurlencode('BOOKNEST ' . $order->order_code)
                . '&accountName=' . rawurlencode('DAO VAN PHU');
        }
        return view('checkout.thankyou', compact('order', 'bankQrUrl'));
    }

    public function mine()
    {
        abort_unless(auth()->check(), 403);
        $orders = Order::with('items')->where('user_id', auth()->id())->latest()->paginate(10);
        return view('orders.mine', compact('orders'));
    }
}
