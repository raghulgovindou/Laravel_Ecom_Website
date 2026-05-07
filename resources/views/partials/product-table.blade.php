@foreach($products as $product)
<tr class="border-t border-gray-100 dark:border-gray-800">
    <td class="py-3 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" />
            </div>
            <div>
                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                    {{ $product->name }}
                </p>
                <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    {{ $product->variants }} Variants
                </span>
            </div>
        </div>
    </td>
    <td class="py-3 whitespace-nowrap">
        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
            {{ $product->category }}
        </p>
    </td>
    <td class="py-3 whitespace-nowrap">
        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
            {{ $product->price }}
        </p>
    </td>
    <td class="py-3 whitespace-nowrap">
        <button  data-id="{{ $product->id }} " class="delete-btn bg-blue-600 text-white px-4 py-2 rounded">
    Delete
</button>
<td class="py-3 whitespace-nowrap">
    @auth
        @if(auth()->user()->role === 'admin')
            <button data-id="{{ $product->id }}" class="edit-btn bg-blue-600 text-white px-4 py-2 rounded">
                Edit
            </button>
        @else
            <button data-id="{{ $product->id }}" data-price="{{ $product->price }}" class="buy-btn bg-blue-600 text-white px-4 py-2 rounded">
                Buy
            </button>
        @endif
    @endauth
</td>
</tr>
@endforeach
<tr>
    <td class="py-3 whitespace-nowrap">
       {{ $products->withPath('/search-products')->links() }}
    </td>
</tr>