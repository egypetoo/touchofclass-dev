<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\StoreCartQuoteRequest;
use App\Models\Product;
use App\Services\SessionCart;
use App\Services\SiteLayoutDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private SessionCart $cart,
        private SiteLayoutDataService $layout,
    ) {}

    public function index(): View
    {
        $lines = $this->cart->lines();

        return view('site.cart', [
            'lines' => $lines,
            'units' => (int) $lines->sum('qty'),
        ]);
    }

    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        $this->cart->add(
            $request->integer('product_id'),
            $request->integer('qty', 1),
        );

        return redirect()->route('cart');
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $this->cart->update($product->id, $data['qty']);

        return response()->json(['count' => $this->cart->count()]);
    }

    public function destroy(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->cart->remove($product->id);

        if ($request->expectsJson()) {
            return response()->json(['count' => $this->cart->count()]);
        }

        return redirect()->route('cart');
    }

    public function quote(StoreCartQuoteRequest $request): RedirectResponse
    {
        $submitted = $request->validated('items') ?? [];
        $next = [];

        foreach ($this->cart->items() as $id => $qty) {
            $row = $submitted[$id] ?? $submitted[(string) $id] ?? null;

            if (! is_array($row)) {
                continue;
            }

            $next[$id] = min(99, max(1, (int) ($row['qty'] ?? $qty)));
        }

        $this->cart->replace($next);
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart')->withErrors([
                'cart' => 'السلة فاضية',
            ]);
        }

        $city = StoreCartQuoteRequest::CITIES[$request->string('city')->toString()] ?? '';
        $message = "طلب عرض سعر\n";
        $message .= 'الاسم: '.$request->string('name')->toString()."\n";
        $message .= 'الموبايل: '.$request->string('phone')->toString()."\n";
        $message .= 'المحافظة: '.$city."\n";
        $message .= "المنتجات:\n";

        foreach ($lines as $line) {
            $product = $line['product'];
            $message .= '- '.$product->getTranslation('name', 'ar').' ('.$product->code.') × '.$line['qty']."\n";
        }

        $notes = trim($request->string('notes')->toString());

        if ($notes !== '') {
            $message .= 'ملاحظات: '.$notes;
        }

        $base = $this->layout->siteSetting()?->whatsappUrl() ?? 'https://wa.me/201001234567';

        return redirect()->away($base.'?text='.rawurlencode($message));
    }
}
