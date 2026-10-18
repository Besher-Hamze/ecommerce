$(document).ready(function () {
    function csrf() {
        return $('meta[name="csrf-token"]').attr("content");
    }

    function updateCartBadge(count) {
        var el = document.getElementById("cart_counter");
        if (el && count !== undefined && count !== null) {
            el.textContent = count;
        }
    }

    function syncCartCountWithSession() {
        var route = $('meta[name="cart-count-url"]').attr("content");
        if (!route) return;
        $.get(route, { _: Date.now() }, function (res) {
            updateCartBadge(parseInt(res.cart_count, 10) || 0);
        });
    }

    function refreshMiniCart() {
        var route = $('meta[name="cart-mini-url"]').attr("content");
        if (!route) return;
        $.get(route, function (res) {
            updateCartBadge(res.cart_count);
            $("#miniCartBody").html(res.html);
        });
    }

    function applyTotals(totals) {
        if (!totals) return;
        $("#cart-subtotal").text("$" + Number(totals.subtotal).toFixed(2));
        $("#cart-shipping").text(totals.free ? "Free" : "$" + Number(totals.shipping).toFixed(2));
        $("#cart-total").text("$" + Number(totals.total).toFixed(2));
        if (typeof window.updateShippingBar === "function") {
            window.updateShippingBar(totals);
        }
    }

    function addProductToCart(route, qty, source, context) {
        if (!route) {
            toastr.error("Could not add to cart. Please try again.");
            return;
        }
        $.ajaxSetup({ headers: { "X-CSRF-TOKEN": csrf() } });
        $.post(
            route,
            { qty: qty || 1, source: source || null, context: context || null },
            function (res) {
                toastr.success(res.status);
                updateCartBadge(parseInt(res.cart_count, 10) || 0);
                refreshMiniCart();
            }
        ).fail(function () {
            toastr.error("Could not add to cart. Please try again.");
        });
    }

    function patchCart(id, quantity, $row) {
        var route = $row.find("[data-route]").first().data("route");
        $.ajaxSetup({ headers: { "X-CSRF-TOKEN": csrf() } });
        $.ajax({
            url: route,
            type: "POST",
            data: { _method: "PATCH", id: id, quantity: quantity },
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            success: function (res) {
                updateCartBadge(res.cart_count);
                $row.find(".line-total").text("$" + Number(res.line_total).toFixed(2));
                applyTotals(res.totals);
            },
        });
    }

    syncCartCountWithSession();
    $(window).on("pageshow", syncCartCountWithSession);
    $("#miniCartOffcanvas").on("show.bs.offcanvas", refreshMiniCart);

    $(document).on("click", ".add-to-cart", function (event) {
        event.preventDefault();
        var $btn = $(this);
        addProductToCart($btn.data("route"), 1, $btn.data("source"), $btn.data("context"));
    });

    $(document).on("click", "#btn", function () {
        addProductToCart(
            $("#route").val(),
            parseInt($("#qty").val(), 10) || 1,
            $("#rec_source").val(),
            $("#rec_context").val()
        );
    });

    $(document).on("click", ".update-cart_increment, .update-cart_decrement", function (e) {
        e.preventDefault();
        var $row = $(this).closest("tr");
        var $input = $row.find(".quantity_2");
        var qty = parseInt($input.val(), 10) || 1;
        qty = $(this).hasClass("update-cart_increment") ? qty + 1 : Math.max(1, qty - 1);
        $input.val(qty);
        patchCart($row.data("id"), qty, $row);
    });

    $(document).on("change", ".quantity_2", function () {
        var $row = $(this).closest("tr");
        patchCart($row.data("id"), Math.max(1, parseInt($(this).val(), 10) || 1), $row);
    });

    $(document).on("click", ".remove-from-cart", function (e) {
        e.preventDefault();
        var $row = $(this).closest("tr");
        $.ajaxSetup({ headers: { "X-CSRF-TOKEN": csrf() } });
        $.ajax({
            url: $(this).data("route"),
            type: "POST",
            data: { _method: "DELETE", id: $row.data("id") },
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            success: function (res) {
                toastr.success(res.status);
                updateCartBadge(res.cart_count);
                $row.fadeOut(200, function () { $(this).remove(); });
                applyTotals(res.totals);
            },
        });
    });

    $(document).on("click", ".add-bundle", function (e) {
        e.preventDefault();
        var $btn = $(this);
        $.ajaxSetup({ headers: { "X-CSRF-TOKEN": csrf() } });
        $.post(
            $btn.data("route"),
            { ids: $btn.data("ids"), context: $btn.data("context") },
            function (res) {
                toastr.success(res.status);
                updateCartBadge(res.cart_count);
                refreshMiniCart();
            }
        ).fail(function () {
            toastr.error("Could not add bundle.");
        });
    });
});
