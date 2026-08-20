$(document).ready(function () {

    function updateCartBadge(count) {
        var el = document.getElementById('cart_counter');

        if (el && count !== undefined && count !== null) {
            el.textContent = count;
        }
    }

    function saveCartCount(count) {
        localStorage.setItem('cart_count', count);
    }

    function clearCartCount() {
        localStorage.removeItem('cart_count');
        updateCartBadge(0);
    }

    function loadCartCountFromLocalStorage() {
        var count = localStorage.getItem('cart_count');

        if (count !== null) {
            updateCartBadge(count);
        }
    }

    function syncCartCountWithSession() {
        var route = $('meta[name="cart-count-url"]').attr("content");

        if (!route) return;

        $.ajax({
            url: route,
            type: "GET",
            cache: false,
            data: { _: Date.now() },
            success: function (res) {
                var count = parseInt(res.cart_count, 10) || 0;

                updateCartBadge(count);

                if (count > 0) {
                    saveCartCount(count);
                } else {
                    localStorage.removeItem('cart_count');
                }
            }
        });
    }

    // أول ما تفتح الصفحة: اعرض آخر رقم محفوظ بسرعة
    loadCartCountFromLocalStorage();

    // بعدها تأكد من الرقم الحقيقي الموجود في session
    syncCartCountWithSession();

    // عند الرجوع بزر Back
    $(window).on("pageshow", function () {
        loadCartCountFromLocalStorage();
        syncCartCountWithSession();
    });

    function addProductToCart(route, qty) {
        if (!route) {
            toastr.error("Could not add to cart. Please try again.");
            return;
        }

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: route,
            type: "post",
            data: { qty: qty || 1 },
            success: function (res) {
                toastr.success(res.status);

                var count = parseInt(res.cart_count, 10) || 0;

                updateCartBadge(count);
                saveCartCount(count);
            },
            error: function (xhr) {
                toastr.error("Could not add to cart. Please try again.");
                console.error(xhr.status, xhr.responseText);
            },
        });
    }

    $(".add-to-cart").on("click", function (event) {
        event.preventDefault();
        addProductToCart($(this).data("route"), 1);
    });

    $(document).on("click", "#btn", function () {
        var qty = parseInt($("#qty").val(), 10) || 1;
        addProductToCart($("#route").val(), qty);
    });

});