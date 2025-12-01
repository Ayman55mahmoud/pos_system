@extends('layout.master')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">Edit Order #{{ $order->order_number }}</h2>

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
       

        <!-- Order Info -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Order Info</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Customer Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $order->phone) }}">
                    </div>

                    <div class="col-md-4">
                        <label>Order Type</label>
                        <select name="order_type" class="form-control">
                            <option value="dine_in" {{ old('order_type', $order->order_type) == 'dine_in' ? 'selected' : '' }}>Dine In</option>
                            <option value="takeaway" {{ old('order_type', $order->order_type) == 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                            <option value="delivery" {{ old('order_type', $order->order_type) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Address (Delivery)</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address', $order->address) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Items -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Current Items</div>
            <div class="card-body" id="oldItemsContainer">
                @foreach ($order->items as $item)
                    <div class="row mb-3 border p-3 rounded item-row" data-id="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $item->id }}][item_id]" value="{{ $item->id }}">

                        <div class="col-md-4">
                            <label>Product</label>
                            <select name="items[{{ $item->id }}][product_id]" class="form-control">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ (old("items.{$item->id}.product_id", $item->product_id) == $product->id) ? 'selected' : '' }}>
                                        {{ $product->name }} - {{ $product->price }} EGP
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Quantity</label>
                            <input type="number" name="items[{{ $item->id }}][quantity]" class="form-control" value="{{ old("items.{$item->id}.quantity", $item->quantity) }}" min="1">
                        </div>

                        <div class="col-md-3">
                            <label>Subtotal</label>
                            <input type="text" class="form-control" value="{{ $item->sup_total }}" readonly>
                            <input type="hidden" name="items[{{ $item->id }}][sup_total]" value="{{ $item->sup_total }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger delete-old-item" data-id="{{ $item->id }}">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Add New Items -->
        <div class="card mb-4">
            <div class="card-header bg-warning">Add New Items</div>
            <div class="card-body">
                <div id="newItemsContainer"></div>
                <button type="button" class="btn btn-secondary" id="addNewItemBtn">+ Add Product</button>
            </div>
        </div>

        <input type="hidden" name="deleted_items" id="deletedItemsInput">

        <button class="btn btn-primary btn-lg btn-block">Save Changes</button>
    </form>
</div>

{{-- ======= pass products to JS as JSON ======= --}}
<script>
    const PRODUCTS = @json($products->map(fn($p) => ['id'=>$p->id, 'name'=>$p->name, 'price'=>$p->price]));
</script>

{{-- ======= JS Logic ======= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('addNewItemBtn');
    const newItemsContainer = document.getElementById('newItemsContainer');
    const deletedItemsInput = document.getElementById('deletedItemsInput');
    let deletedItems = [];

    // safety
    if (!Array.isArray(PRODUCTS)) {
        console.error('PRODUCTS not defined or not an array');
        return;
    }
    if (!addBtn) console.warn('#addNewItemBtn not found');
    if (!newItemsContainer) console.warn('#newItemsContainer not found');

    // helper escape
    function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

    function buildProductOptions(){
        return PRODUCTS.map(p => `<option value="${p.id}">${escapeHtml(p.name)} - ${p.price} EGP</option>`).join('');
    }

    // delegation for old-item delete and new-item remove
    document.addEventListener('click', function(e){
        const t = e.target;

        if(t && t.classList && t.classList.contains('delete-old-item')){
            const id = t.dataset.id;
            if(id){
                deletedItems.push(id);
                deletedItemsInput.value = deletedItems.join(',');
            }
            const row = t.closest('.item-row');
            if(row) row.remove();
        }

        if(t && t.classList && t.classList.contains('remove-new-item')){
            const row = t.closest('.new-item-row');
            if(row) row.remove();
        }
    });

    // add new row
    addBtn && addBtn.addEventListener('click', function(ev){
        ev.preventDefault();
        const options = buildProductOptions();
        const html = `
            <div class="row mb-3 border p-3 rounded new-item-row">
                <div class="col-md-4">
                    <label>Product</label>
                    <select name="new_items[][product_id]" class="form-control">
                        ${options}
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Quantity</label>
                    <input type="number" name="new_items[][quantity]" class="form-control" value="1" min="1">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-new-item">X</button>
                </div>
            </div>
        `;
        newItemsContainer.insertAdjacentHTML('beforeend', html);
    });

    // debug helpers
    window.__orderFormDebug = {
        PRODUCTS,
        getNewRowsCount: () => newItemsContainer ? newItemsContainer.querySelectorAll('.new-item-row').length : 0,
        getDeleted: () => deletedItems.slice()
    };
});
</script>

@endsection
