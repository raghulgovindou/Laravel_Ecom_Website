<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
public function index()
{
    //$products = Product::all(); // fetch all data
     $products = Product::orderBy('id', 'desc')->paginate(5); // 👈 5 per page
    return view('pages.dashboard.ecommerce', [
        'title' => 'E-commerce Dashboard',
        'products' => $products
    ]);
}
//edit item
public function edit($id)
{
    $product = Product::find($id);

    return response()->json($product);
    }
// delete item
public function deleteItem($id)
{
    // 🔍 Find product
    $product = Product::find($id);

    // ❌ If not found
    if (!$product) {
        return response()->json([
            'status' => 'error',
            'message' => 'Product not found'
        ], 404);
    }

    // 🗑 Delete image (if exists)
    if ($product->image && file_exists(public_path($product->image))) { 
        unlink(public_path($product->image));
    }

    // 🗑 Delete product
    $product->delete();

    // ✅ Return response
    return response()->json([
        'status' => 'success',
        'message' => 'Product deleted successfully'
    ]);
}
// add items
public function additems(Request $request)
{
    // ✅ Validate (optional but recommended)
    $request->validate([
        'name' => 'required',
        'price' => 'required|numeric',
        'image' => 'nullable|image'
    ]);

    // ✅ Handle file upload
    $imageName = null;

    if ($request->hasFile('image')) {
        $image = $request->file('image');

        // generate unique name
       $imageName = time() . '.' . $image->getClientOriginalExtension();

        // store in public/uploads
        $image->move(public_path('images/product'), $imageName);
    }

    // ✅ Save to database
    $product = Product::create([
        'name' => $request->name,
        'variants' => $request->variants,
        'category' => $request->category,
        'price' => $request->price,
        'status' => $request->status,
        'image' => 'images/product/' . $imageName
    ]);

    // ✅ Return JSON response (important for fetch)
    return response()->json([
        'status' => 'success',
        'data' => $product
    ]);
}
 public function update(Request $request,$id)
            {
               // dd($request->all(), $request->file('image'));
                $product = Product::find($id);
                $imageName = $product->image;

    if ($request->hasFile('image')) {
        $image = $request->file('image');

       $fileName = time() . '.' . $image->getClientOriginalExtension();

        $image->move(public_path('images/product/'), $fileName);

        $imageName = 'images/product/' . $fileName;
    }
                $product->update([
                    'name' => $request->name,
                    'variants' => $request->variants,
                    'category' => $request->category,
                    'price' => $request->price,
                    'status' => $request->status,
                     'image' =>  $imageName
                ]);

                return response()->json(['success' => true]);
            }
public function search(Request $request)
    {
        $query = $request->query('query', '');
        $min = $request->query('min_price');
        $max = $request->query('max_price');
        $sort = $request->query('sort');
        $perPage = $request->query('all') ? 1000 : 5;
        $products = Product::when($query, function ($q) use ($query) {
        $q->where('name', 'like', "%$query%");
    })
    ->when($min, function ($q) use ($min) {
        $q->where('price', '>=', $min);
    })
    ->when($max, function ($q) use ($max) {
        $q->where('price', '<=', $max);
    })
    ->when($sort == 'price_asc', function ($q) {
        $q->orderBy('price', 'asc');
    })
    ->when($sort == 'price_desc', function ($q) {
        $q->orderBy('price', 'desc');
    })
    ->when(!$sort, function ($q) {
        $q->orderBy('id', 'desc');
    })
    ->paginate($perPage)
    ->withQueryString();
            
        return view('partials.product-table', compact('products'))->render();
    }
    public function list() {    $products = Product::orderBy('id', 'desc')->paginate(5); return view('partials.product-table', compact('products'))->render(); }
}
