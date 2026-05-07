<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class OrderController extends Controller
{
    /**
     * 
     * Display a listing of the resource.
     */
    public function createOrder(Request $request)
{
    $product = Product::findOrFail($request->product_id);

    $amount = $product->price * $request->quantity;

    // save order first
    $order = Order::create([
        'user_id' => auth()->id(),
        'product_id' => $product->id,
        'quantity' => $request->quantity,
        'price' => $amount,
        'status' => 'pending'
    ]);

    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    $razorpayOrder = $api->order->create([
        'receipt' => 'order_' . $order->id,
        'amount' => $amount * 100,
        'currency' => 'INR'
    ]);
   $order->update([
    'razorpay_order_id' => $razorpayOrder['id']
]);

    return response()->json([
        'key' => env('RAZORPAY_KEY'),
        'amount' => $amount * 100,
        'razorpay_order_id' => $razorpayOrder['id'],
        'order_id' => $order->id
    ]);
}
   public function paymentSuccess(Request $request)
{
    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    $attributes = [
        'razorpay_order_id' => $request->order_id,
        'razorpay_payment_id' => $request->payment_id,
        'razorpay_signature' => $request->signature
    ];

    try {
        // 🔐 verify signature
        $api->utility->verifyPaymentSignature($attributes);

        // ✅ SUCCESS → payment is real
        $order = Order::where('razorpay_order_id', $request->order_id)->first();

        $order->update([
            'payment_id' => $request->payment_id,
            'status' => 'paid'
        ]);
         // 📱 Get phone number
    $phone = 6385555389; // make sure column exists

    // 📩 Send SMS
    $response = Http::withHeaders([
        'authorization' => config('services.fast2sms.api_key'),
        'accept' => 'application/json',
    ])->post('https://www.fast2sms.com/dev/bulkV2', [
        'route' => 'q', // simple transactional
        'message' => 'Payment successful! Your order is confirmed.',
        'language' => 'english',
        'flash' => 0,
        'numbers' => $phone,
    ]);

    // 🧪 Debug (optional)
     dd($response->body());

        return response()->json(['success' => true]);

    } catch (\Exception $e) {

        // ❌ FAILED → fake or tampered
        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed'
        ], 400);
    }
}
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
