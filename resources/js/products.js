
function fetchProducts(showAll = false, page = 1) {
    let query = document.getElementById('search').value;
    let max = document.getElementById('max_price').value;
    let min = document.getElementById('min_price').value;
     let sort = document.getElementById('sort').value;
     let params = new URLSearchParams();
     params.append('page', page);
    if (query) params.append('query', query);
    if (min) params.append('min_price', min);
    if (max) params.append('max_price', max);
     if (sort) params.append('sort', sort);
     if (showAll) params.append('all', '1');

    fetch(`/search-products?${params.toString()}`)
        .then(res => res.text())
        .then(data => {
            document.getElementById('product-table').innerHTML = data;
        });
}
const searchInput = document.getElementById('search');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        fetchProducts(showAll);
    });
}
const minPrice = document.getElementById('min_price');
if (minPrice) {
    minPrice.addEventListener('input', function () {
        fetchProducts(showAll);
    });
}

const maxPrice = document.getElementById('max_price');
if (maxPrice) {
    maxPrice.addEventListener('input', function () {
        fetchProducts(showAll);
    });
}

const sort = document.getElementById('sort');
if (sort) {
    sort.addEventListener('change', function () {
        fetchProducts(showAll);
    });
}
let currentPage = 1;
let showAll = false;
const seeall = document.getElementById('see-all-btn');
if(seeall){
seeall.addEventListener('click', function () {
    showAll = !showAll; // toggle true/false

    fetchProducts(showAll);

    // Optional: change button text
    this.innerText = showAll ? 'Less' : 'All';
});}
//edit-btn
document.addEventListener('click', function(e) {

    const btn = e.target.closest('.edit-btn');

    if (btn) {

        const id = btn.dataset.id;

        fetch(`/edit-item/${id}`)
        .then(res => res.json())
        .then(data => {
console.log(data);
            // fill form fields
            document.getElementById("form-section").style.display = "block";
            document.querySelector('[name="name"]').value = data.name;
            document.querySelector('[name="variants"]').value = data.variants;
            document.querySelector('[name="category"]').value = data.category;
            document.querySelector('[name="price"]').value = data.price;
            document.querySelector('[name="status"]').value = data.status;
            document.querySelector('#preview').src = data.image;
            preview.classList.remove('hidden');
            removeImage.classList.remove('hidden');

            // store id somewhere (important for update)
            document.getElementById('item-form').dataset.id = id;
        });
    }
});
//DELETE-btn
document.addEventListener('click', function(e) {

    const btn = e.target.closest('.delete-btn');

    if (btn) {

        const id = btn.dataset.id;

        Swal.fire({
            title: 'Delete?',
            icon: 'warning',
            showCancelButton: true
        }).then(result => {

            if (result.isConfirmed) {

                fetch(`/delete-item/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire('Deleted!', '', 'success');
                    btn.closest('tr')?.remove();
                    //  fetchProducts(showAll, currentPage);
                });
            }
        });
    }
});
//ADD BUTTON
const addBtn = document.getElementById('add-btn');

if (addBtn) {
    addBtn.addEventListener('click', function () {
        document.getElementById("form-section").style.display = "block";
        document.getElementById('item-form').reset();

        preview.src = "";
        preview.classList.add('hidden');
        removeBtn.classList.add('hidden');
    });
}
const closeBtn = document.getElementById('close');

if (closeBtn) {
    closeBtn.addEventListener('click', function () {
        document.getElementById("form-section").style.display = "none";

        if (preview) {
            preview.src = "";
            preview.classList.add('hidden');
        }

        if (removeBtn) {
            removeBtn.classList.add('hidden');
        }
    });
}

const itemForm = document.getElementById('item-form');

if (itemForm) {
    itemForm.addEventListener('submit', function (e) {
        e.preventDefault();
        handleFormSubmit(this);
    });
}

function handleFormSubmit(form) {
    const formData = new FormData(form);

     const id = form.dataset.id;

    let url = '/add-item';
    let method = 'POST';

    // if id exists → update
    if (id) {
        url = `/update-item/${id}`;
        method = 'POST'; // or PUT
    }
    // Example: send via AJAX
    fetch(url, {
    method: method,
    body: formData
})
.then(res => {
    console.log(res); // raw response

    if (!res.ok) {
        throw new Error("Server error");
    }

    return res.json(); // convert body
})
.then(data => {
   // console.log(data); // actual data
        // ✅ Sweet Alert Success
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Product added successfully',
            timer: 2000,
            showConfirmButton: false
        });

        // ✅ Clear form
        form.reset();
        loadProducts();
        fetchProducts(showAll, currentPage);
       
preview.src = "";
preview.classList.add('hidden');
removeBtn.classList.add('hidden');
        // ✅ Optional: clear file input manually (extra safe)
        form.querySelector('input[type="file"]').value = "";
})
.catch(err => {
    console.error(err); // error
});
}

//PAGINATION
document.addEventListener('click', function (e) {
    let link = e.target.closest('a');

    if (link && link.closest('nav')) {
        e.preventDefault();

        let url = new URL(link.href);
        let page = url.searchParams.get('page') || 1;

        currentPage = page;

        fetchProducts(showAll, currentPage);
    }
});

const input = document.getElementById('imageInput');
const preview = document.getElementById('preview');
const removeBtn = document.getElementById('removeImage');

if (removeBtn && preview && input) {
    removeBtn.addEventListener('click', function () {
        preview.src = '';
        preview.classList.add('hidden');

        input.value = '';
        removeBtn.classList.add('hidden');
    });
}

if (input && preview && removeBtn) {
    input.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Select a valid image');
            input.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            removeBtn.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    });
}

function loadProducts() {
    fetch('/products-list')
        .then(res => res.text())
        .then(html => {
             document.getElementById('product-table').innerHTML = html;
        });
}

let selectedProduct = null;
let selectedPrice = 0;

document.querySelectorAll('.buy-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        selectedProduct = this.dataset.id;
        selectedPrice = this.dataset.price;

        document.getElementById('buy-modal').classList.remove('hidden');
    });
});
const close_pay = document.getElementById('close-modal');
if(close_pay){
document.getElementById('close-modal').onclick = () => {
    document.getElementById('buy-modal').classList.add('hidden');
};}
const paynow = document.getElementById('pay-now');
if(paynow){
document.getElementById('pay-now').addEventListener('click', function () {

    let qty = document.getElementById('qty').value;

    fetch('/create-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: selectedProduct,
            quantity: qty
        })
    })
    .then(res => res.json())
    .then(data => {
        openRazorpay(data);
    });
});}
function openRazorpay(data) {

    var options = {
        key: data.key,
        amount: data.amount,
        currency: "INR",
        name: "My App",
        order_id: data.razorpay_order_id,
        method: {
        upi: true
    },

        handler: function (response) {
            console.log(response);
            fetch('/payment-success', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    payment_id: response.razorpay_payment_id,
                   order_id: response.razorpay_order_id,
                   signature: response.razorpay_signature
                })
            }).then(() => {
                //console.log('hello'.response);
               // window.location.reload();
            });
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
}