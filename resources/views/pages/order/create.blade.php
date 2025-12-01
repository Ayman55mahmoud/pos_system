@extends('layout.master')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">Create New Order</h2>

    <form action="{{ url('orderstore') }}" method="POST">
        @csrf

        <!-- ========================== -->
        <!--      Order Info           -->
        <!-- ========================== -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Order Info</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Table Number</label>
                        <input type="number" name="table_number" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Order Type</label>
                        <select name="order_type" class="form-control" id="orderTypeSelect">
                            <option value="dine_in">Dine In</option>
                            <option value="takeaway">Takeaway</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>
                </div>

                <div id="addressBox" class="mb-3" style="display:none;">
                    <label>Address (Delivery Only)</label>
                    <textarea name="address" class="form-control"></textarea>
                </div>
            </div>
        </div>

        <!-- ========================== -->
        <!--       Order Items          -->
        <!-- ========================== -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">Order Items</div>
            <div class="card-body">
                <div id="itemsContainer"></div>
                <button type="button" class="btn btn-secondary mt-3" id="addRowBtn">+ Add Product</button>
            </div>
        </div>

        <button class="btn btn-primary w-100" type="submit">Create Order</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

    const PRODUCTS = @json($products);

    const orderTypeSelect = document.getElementById("orderTypeSelect");
    const addressBox = document.getElementById("addressBox");
    const addRowBtn = document.getElementById("addRowBtn");
    const itemsContainer = document.getElementById("itemsContainer");

    let rowIndex = 0; // لكل صف index مختلف

    // -------------------
    // Address toggle
    // -------------------
    orderTypeSelect.addEventListener("change", function(){
        addressBox.style.display = this.value === "delivery" ? "block" : "none";
    });

    // -------------------
    // Build product <option>s
    // -------------------
    function buildOptions(){
        return PRODUCTS.map(p => 
            `<option value="${p.id}" data-price="${p.price}">${p.name} — ${p.price} EGP</option>`
        ).join("");
    }

    // -------------------
    // Add new product row
    // -------------------
    addRowBtn.addEventListener("click", function(){
        const options = buildOptions();

        const row = `
            <div class="row mb-3 p-3 border rounded item-row">
                <div class="col-md-5">
                    <label>Product</label>
                    <select name="items[${rowIndex}][product_id]" class="form-control productSelect">
                        ${options}
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Quantity</label>
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control qtyInput" value="1" min="1">
                </div>
                <div class="col-md-3">
                    <label>Subtotal</label>
                    <input type="text" class="form-control subtotal" readonly>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger removeRow">X</button>
                </div>
            </div>
        `;

        itemsContainer.insertAdjacentHTML("beforeend", row);

        const newRow = itemsContainer.lastElementChild;
        updateSubtotal(newRow);

        rowIndex++;
    });

    // -------------------
    // Remove row
    // -------------------
    document.addEventListener("click", function(e){
        if(e.target.classList.contains("removeRow")){
            e.target.closest(".item-row").remove();
        }
    });

    // -------------------
    // Update subtotal
    // -------------------
    function updateSubtotal(row){
        const productSelect = row.querySelector(".productSelect");
        const qtyInput = row.querySelector(".qtyInput");
        const subtotalInput = row.querySelector(".subtotal");

        const price = parseFloat(productSelect.selectedOptions[0].dataset.price || 0);
        const qty = parseInt(qtyInput.value) || 0;

        subtotalInput.value = (price * qty).toFixed(2);
    }

    document.addEventListener("input", function(e){
        if(e.target.classList.contains("qtyInput")){
            const row = e.target.closest(".item-row");
            updateSubtotal(row);
        }
    });

    document.addEventListener("change", function(e){
        if(e.target.classList.contains("productSelect")){
            const row = e.target.closest(".item-row");
            updateSubtotal(row);
        }
    });

});
</script>

@endsection
