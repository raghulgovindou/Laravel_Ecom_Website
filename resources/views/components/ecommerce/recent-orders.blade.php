@props(['products' => []])

@php
//Print_r($products);
    // Helper function for status classes
    $getStatusClasses = function($status) {
        $baseClasses = 'rounded-full px-2 py-0.5 text-theme-xs font-medium';
        
        return match($status) {
            'Delivered' => $baseClasses . ' bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            'Pending' => $baseClasses . ' bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400',
            'Canceled' => $baseClasses . ' bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            default => $baseClasses . ' bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400',
        };
    };
@endphp

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div id="table-section">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Orders</h4>
        </div>

        <div class="flex items-center gap-3">
           <select id="sort" class="w-32 inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-theme-xs">
                <option value="">Sort</option>
                <option value="price_asc">Price Low → High</option>
                <option value="price_desc">Price High → Low</option>
            </select>


            <button id="see-all-btn"
                class="px-3 py-1.5 text-sm rounded-md border border-gray-300 bg-white">
                All
                </button>
                  @auth
    @if(auth()->user()->role === 'admin')
        <button id="add-btn" class="px-3 py-1.5 text-sm rounded-md border border-gray-300 bg-white">
            Add
        </button>
    @endif
@endauth
                        <div class="flex gap-2">
                <input type="number" id="min_price" placeholder="Min price"
                class="w-1/2 rounded-lg border px-3 py-2">

                <input type="number" id="max_price" placeholder="Max price"
                class="w-1/2 rounded-lg border px-3 py-2">
                </div>
                        </div>
                    </div>
       <div class="p-4">
        <input 
            type="text" 
            id="search"
            placeholder="Search orders..." 
            class="form-input w-full rounded-lg border-gray-300"
        >
    </div>
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="min-w-full" id="product-table">
            <thead>
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <th class="py-3 text-left">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Products</p>
                    </th>
                    <th class="py-3 text-left">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Category</p>
                    </th>
                    <th class="py-3 text-left">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Price</p>
                    </th>
                    <th class="py-3 text-left">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
                    </th>
                </tr>
            </thead>
            <tbody>
               @include('partials.product-table')
            </tbody>
        </table>
        <div id="buy-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-5 rounded">
        <h3>Enter Quantity</h3>

        <input type="number" id="qty" value="1" min="1" class="border p-2">

        <button id="pay-now" class="bg-blue-600 text-white px-4 py-2 mt-3">Pay</button>
        <button id="close-modal" class="ml-2">Cancel</button>
    </div>
</div>
</div>
        <div id="form-section" class="hidden">
            <label><b>Add Products : </b></label>
        <form  id="item-form" enctype="multipart/form-data" class="space-y-3 max-w-md">
    @csrf

    <!-- Name -->
    <input type="text" name="name" placeholder="Name"
        class="w-full px-3 py-1.5 text-sm border rounded-md focus:ring-1 focus:ring-blue-500">

    <!-- Variants -->
    <input type="text" name="variants" placeholder="Variants"
        class="w-full px-3 py-1.5 text-sm border rounded-md">

    <!-- Category -->
    <input type="text" name="category" placeholder="Category"
        class="w-full px-3 py-1.5 text-sm border rounded-md">

    <!-- Price -->
    <input type="number" name="price" placeholder="Price"
        class="w-full px-3 py-1.5 text-sm border rounded-md">

    <!-- Image -->
    <input type="file" name="image" id="imageInput"
    class="w-full text-sm border rounded-md p-1" accept="image/*">

<div class="relative inline-block">
    <img id="preview"
         class="mt-2 hidden rounded-md"
         width="120">

    <button type="button"
            id="removeImage"
            class="hidden absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 text-sm">
        ×
    </button>
</div>

    <!-- Status -->
    <select name="status"
        class="w-full px-3 py-1.5 text-sm border rounded-md">
        <option value=" ">choose</option>
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
    </select>

    <!-- Buttons -->
    <div class="flex gap-2">
        <button type="submit"
            class="px-3 py-1.5 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600">
            Save
        </button>

        <button type="button" id="close"
            class="px-3 py-1.5 text-sm border rounded-md hover:bg-gray-100">
            Cancel
        </button>
    </div>
</form></div>
    </div>
</div>