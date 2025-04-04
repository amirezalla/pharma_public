! function (e) {
    "use strict";
    e.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": e('meta[name="csrf-token"]').attr("content")
        }
    });
    var t = function (e) {
        window.showAlert("alert-danger", e)
    },
        a = function (e) {
            window.showAlert("alert-success", e)
        },
        o = function (a) {
            void 0 !== a.errors && a.errors.length ? s(a.errors) : void 0 !== a.responseJSON ? void 0 !== a.responseJSON.errors ? 422 === a.status && s(a.responseJSON.errors) : void 0 !== a.responseJSON.message ? t(a.responseJSON.message) : e.each(a.responseJSON, (function (a, o) {
                e.each(o, (function (e, a) {
                    t(a)
                }))
            })) : t(a.statusText)
        },
        s = function (a) {
            var o = "";
            e.each(a, (function (e, t) {
                "" !== o && (o += "<br />"), o += t
            })), t(o)
        };
    window.showAlert = function (t, a) {
        if (t && "" !== a) {
            var o = Math.floor(1e3 * Math.random()),
                s = '<div class="alert '.concat(t, ' alert-dismissible" id="').concat(o, '">\n                <span class="btn-close" data-bs-dismiss="alert" aria-label="close"></span>\n                <i class="fas fa-') + ("alert-success" === t ? "check-circle" : "exclamation-circle") + ' message-icon"></i>\n                '.concat(a, "\n            </div>");
            e("#alert-container").append(s).ready((function () {
                window.setTimeout((function () {
                    e("#alert-container #".concat(o)).remove()
                }), 3e3)
            }))
        }
    };
    var n = "rtl" === e("body").prop("dir");
    e(document).ready((function () {
        jQuery().mCustomScrollbar && e(".ps-custom-scrollbar").mCustomScrollbar({
            theme: "dark",
            scrollInertia: 0
        }), window.onBeforeChangeSwatches = function (t) {
            e(".add-to-cart-form .error-message").hide(), e(".add-to-cart-form .success-message").hide(), e(".number-items-available").html("").hide(), t && t.attributes && e(".add-to-cart-form button[type=submit]").prop("disabled", !0).addClass("btn-disabled")
        }, window.onChangeSwatchesSuccess = function (t) {
            if (e(".add-to-cart-form .error-message").hide(), e(".add-to-cart-form .success-message").hide(), t) {
                var a = e(".add-to-cart-form button[type=submit]");
                if (t.error) a.prop("disabled", !0).addClass("btn-disabled"), e(".number-items-available").html('<span class="text-danger">(' + t.message + ")</span>").show(), e(".hidden-product-id").val("");
                else {
                    e(".add-to-cart-form").find(".error-message").hide(), e(".product-price ins span.text-brand").text(t.data.display_sale_price), t.data.sale_price !== t.data.price ? (e(".product-price ins span.old-price").text(t.data.display_price).show(), e(".product-price span.save-price .percentage-off").text(t.data.sale_percentage), e(".product-price span.save-price").show()) : (e(".product-price ins span.old-price").hide(), e(".product-price span.save-price").hide()), e(".sku_wrapper .value").text(t.data.sku), e(".hidden-product-id").val(t.data.id), a.prop("disabled", !1).removeClass("btn-disabled"), t.data.error_message ? (a.prop("disabled", !0).addClass("btn-disabled"), e(".number-items-available").html('<span class="text-danger">(' + t.data.error_message + ")</span>").show()) : t.data.success_message ? e(".number-items-available").html('<span class="text-success">(' + t.data.success_message + ")</span>").show() : e(".number-items-available").html("").hide();
                    var o = t.data.unavailable_attribute_ids || [];
                    e(".attribute-swatch-item").removeClass("pe-none"), e(".product-filter-item option").prop("disabled", !1), o && o.length && o.map((function (t) {
                        var a = e('.attribute-swatch-item[data-id="' + t + '"]');
                        a.length ? (a.addClass("pe-none"), a.find("input").prop("checked", !1)) : (a = e('.product-filter-item option[data-id="' + t + '"]')).length && a.prop("disabled", "disabled").prop("selected", !1)
                    }));
                    var s = e(".product-image-slider");
                    s.slick("unslick");
                    var r = "";
                    t.data.image_with_sizes.origin.forEach((function (e) {
                        r += '<figure class="border-radius-10"><a href="' + e + '"><img src="' + e + '" alt="image"/></a></figure>'
                    })), s.html(r), s.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        rtl: n,
                        arrows: !1,
                        fade: !1,
                        asNavFor: ".slider-nav-thumbnails"
                    });
                    var i = e(".slider-nav-thumbnails");
                    i.slick("unslick");
                    var l = "";
                    t.data.image_with_sizes.thumb.forEach((function (e) {
                        l += '<div class="item"><img src="' + e + '" alt="image"/></div>'
                    })), i.html(l), i.slick({
                        slidesToShow: 5,
                        slidesToScroll: 1,
                        rtl: n,
                        asNavFor: ".product-image-slider",
                        dots: !1,
                        focusOnSelect: !0,
                        prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
                        nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>'
                    }), i.find(".slick-slide").removeClass("slick-active"), i.find(".slick-slide").eq(0).addClass("slick-active"), s.on("beforeChange", (function (e, t, a, o) {
                        var s = o;
                        i.find(".slick-slide").removeClass("slick-active"), i.find(".slick-slide").eq(s).addClass("slick-active")
                    })), s.lightGallery({
                        selector: ".slick-slide:not(.slick-cloned) a",
                        thumbnail: !0,
                        share: !1,
                        fullScreen: !1,
                        autoplay: !1,
                        autoplayControls: !1,
                        actualSize: !1
                    })
                }
            }
        }, e(document).on("click", ".newsletter-form button[type=submit]", (function (s) {
            s.preventDefault(), s.stopPropagation();
            var n = e(this);
            n.addClass("button-loading"), e.ajax({
                type: "POST",
                cache: !1,
                url: n.closest("form").prop("action"),
                data: new FormData(n.closest("form")[0]),
                contentType: !1,
                processData: !1,
                success: function (e) {
                    n.removeClass("button-loading"), "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), e.error ? t(e.message) : (n.closest("form").find("input[type=email]").val(""), a(e.message))
                },
                error: function (e) {
                    "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), n.removeClass("button-loading"), o(e)
                }
            })
        })), e(document).on("change", ".switch-currency", (function () {
            e(this).closest("form").submit()
        })), e(document).on("click", ".js-add-to-wishlist-button", function (t) {
            t.preventDefault();
            var a = e(this);
            var self = this; // Capture the context of 'this'

            a.addClass("button-loading");

            e.ajax({
                url: a.data("url"),
                method: "POST",
                success: function (t) {
                    if (t.error) {
                        a.removeClass("button-loading");
                        window.showAlert("alert-danger", t.message);
                        return false;
                    }

                    window.showAlert("alert-success", t.message);
                    e(".wishlist-count span").text(t.data.count);
                    a.toggleClass("wis_added");
                    a.removeClass("button-loading").removeClass("js-add-to-wishlist-button").addClass("js-remove-from-wishlist-button");

                    // Use 'self' instead of 'this' to refer to the clicked button
                    if (e(self).find(".fa-heart").hasClass('fas')) {
                        e(self).find(".fa-heart").removeClass('fas').addClass('far').css("color", "#005BA1");
                    } else {
                        e(self).find(".fa-heart").removeClass('far').addClass('fas').css("color", "red");
                    }
                },
                error: function (e) {
                    a.removeClass("button-loading");
                    window.showAlert("alert-danger", e.message);
                }
            });
        }), e(document).on("click", ".js-remove-from-wishlist-button", function (t) {
            t.preventDefault();
            var a = e(this);
            var self = this; // Capture the context of 'this'

            a.addClass("button-loading");

            e.ajax({
                url: a.data("url"),
                method: "DELETE",
                success: function (t) {
                    if (t.error) {
                        a.removeClass("button-loading");
                        window.showAlert("alert-danger", t.message);
                        return false;
                    }

                    window.showAlert("alert-success", t.message);
                    e(".wishlist-count span").text(t.data.count);
                    a.removeClass("button-loading");
                    a.closest("tr").remove();
                    a.removeClass("js-remove-from-wishlist-button").addClass("js-add-to-wishlist-button");

                    // Use 'self' instead of 'this' to refer to the clicked button
                    if (e(self).find(".fa-heart").hasClass('fas')) {
                        e(self).find(".fa-heart").removeClass('fas').addClass('far').css("color", "#005BA1");
                    } else {
                        e(self).find(".fa-heart").removeClass('far').addClass('fas').css("color", "red");
                    }
                },
                error: function (e) {
                    a.removeClass("button-loading");
                    window.showAlert("alert-danger", e.message);
                }
            });
        }), e(document).on("click", ".js-add-to-compare-button", (function (t) {
            t.preventDefault();
            var a = e(this);
            a.addClass("button-loading"), e.ajax({
                url: a.data("url"),
                method: "POST",
                success: function (t) {
                    if (t.error) return a.removeClass("button-loading"), window.showAlert("alert-danger", t.message), !1;
                    e(".compare-count span").text(t.data.count), window.showAlert("alert-success", t.message), a.removeClass("button-loading")
                },
                error: function (e) {
                    a.removeClass("button-loading"), window.showAlert("alert-danger", e.message)
                }
            })
        })), e(document).on("click", ".js-remove-from-compare-button", (function (t) {
            t.preventDefault();
            var a = e(this),
                o = a.html();
            a.html(o + "..."), e.ajax({
                url: a.data("url"),
                method: "DELETE",
                success: function (t) {
                    if (t.error) return a.text(o), window.showAlert("alert-danger", t.message), !1;
                    e(".compare-count span").text(t.data.count), e(".table__compare").load(window.location.href + " .table__compare > *", (function () {
                        window.showAlert("alert-success", t.message), a.html(o)
                    }))
                },
                error: function (e) {
                    a.removeClass("button-loading"), window.showAlert("alert-danger", e.message)
                }
            })
        })), e(document).on("click", ".add-to-cart-button", (function (t) {
            t.preventDefault();
            var a = e(this);
            a.prop("disabled", !0).addClass("button-loading"), e.ajax({
                url: a.data("url"),
                method: "POST",
                data: {
                    id: a.data("id")
                },
                dataType: "json",
                success: function (t) {
                    if (a.prop("disabled", !1).removeClass("button-loading").addClass("active"), t.error) return window.showAlert("alert-danger", t.message), void 0 !== t.data.next_url && (window.location.href = t.data.next_url), !1;
                    window.showAlert("alert-success", t.message), void 0 !== t.data.next_url ? window.location.href = t.data.next_url : e.ajax({
                        url: window.siteUrl + "/ajax/cart",
                        method: "GET",
                        success: function (t) {
                            t.error || (e(".cart-dropdown-wrap").html(t.data.html), e(".mini-cart-icon span").text(t.data.count))
                            if (window.location.href.indexOf('cart') > -1) {
                                location.reload();
                            }
                        }

                    })
                },
                error: function (e) {
                    a.prop("disabled", !1).removeClass("button-loading"), window.showAlert("alert-danger", e.message)
                }
            })
        })), e(document).on("click", ".add-to-cart-form button[type=submit]", (function (t) {
            t.preventDefault(), t.stopPropagation();
            var a = e(this);
            if (e(".hidden-product-id").val()) {
                a.prop("disabled", !0).addClass("btn-disabled").addClass("button-loading");
                var s = a.closest("form"),
                    n = s.serializeArray();
                n.push({
                    name: "checkout",
                    value: "checkout" === a.prop("name") ? 1 : 0
                }), e.ajax({
                    type: "POST",
                    url: s.prop("action"),
                    data: e.param(n),
                    success: function (t) {
                        if (a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading"), t.error) return a.removeClass("button-loading"), window.showAlert("alert-danger", t.message), void 0 !== t.data.next_url && (window.location.href = t.data.next_url), !1;
                        window.showAlert("alert-success", t.message), void 0 !== t.data.next_url ? window.location.href = t.data.next_url : e.ajax({
                            url: window.siteUrl + "/ajax/cart",
                            method: "GET",
                            success: function (t) {
                                t.error || (e(".cart-dropdown-wrap").html(t.data.html), e(".mini-cart-icon span").text(t.data.count))
                                if (window.location.href.indexOf('cart') > -1) {
                                    location.reload();
                                }
                            }
                        })
                    },
                    error: function (e) {
                        a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading"), o(e, a.closest("form"))
                    }
                })
            } else a.prop("disabled", !0).addClass("btn-disabled")
        })), e(document).on("click", ".remove-cart-item", (function (t) {
            t.preventDefault();
            var a = e(this);
            a.closest("li").addClass("content-loading"), e.ajax({
                url: a.data("url"),
                method: "GET",
                success: function (t) {
                    if (a.closest("li").removeClass("content-loading"), t.error) return window.showAlert("alert-danger", t.message), !1;
                    e.ajax({
                        url: window.siteUrl + "/ajax/cart",
                        method: "GET",
                        success: function (a) {
                            a.error || (e(".cart-dropdown-wrap").html(a.data.html), e(".mini-cart-icon span").text(a.data.count), window.showAlert("alert-success", t.message))
                        }
                    })
                },
                error: function (e) {
                    a.closest("li").removeClass("content-loading"), window.showAlert("alert-danger", e.message)
                }
            })
        })), e(document).on("click", ".remove-cart-button", (function (t) {

            t.preventDefault();
            var a = e(this);
            a.closest(".table--cart").addClass("content-loading"), e.ajax({
                url: a.data("url"),
                method: "GET",
                success: function (t) {
                    if (t.error) return window.showAlert("alert-danger", t.message), a.closest(".table--cart").removeClass("content-loading"), !1;
                    e(".section--shopping-cart").load(window.location.href + " .section--shopping-cart > *", (function () {
                        a.closest(".table--cart").removeClass("content-loading"), window.showAlert("alert-success", t.message)
                    })), e.ajax({
                        url: window.siteUrl + "/ajax/cart",
                        method: "GET",
                        success: function (t) {
                            t.error || (e(".cart-dropdown-wrap").html(t.data.html), e(".mini-cart-icon span").text(t.data.count))
                        }
                    })
                },
                error: function (e) {
                    a.closest(".table--cart").removeClass("content-loading"), window.showAlert("alert-danger", e.message)
                }
            })
        })), e(document).on("change", ".submit-form-on-change", (function () {
            e(this).closest("form").submit()
        }));
        var s = [],
            r = function (e) {
                for (var t = new ClipboardEvent("").clipboardData || new DataTransfer, a = 0, o = s; a < o.length; a++) {
                    var n = o[a];
                    t.items.add(n)
                }
                e.files = t.files, i(e)
            },
            i = function (t) {
                var a = e(".image-upload__text"),
                    o = e(t).data("max-files"),
                    s = t.files.length;
                o ? (s >= o ? a.closest(".image-upload__uploader-container").addClass("d-none") : a.closest(".image-upload__uploader-container").removeClass("d-none"), a.text(s + "/" + o)) : a.text(s);
                var n = e(".image-viewer__list"),
                    r = e("#review-image-template").html();
                if (n.addClass("is-loading"), n.find(".image-viewer__item").remove(), s) {
                    for (var i = s - 1; i >= 0; i--) n.prepend(r.replace("__id__", i));
                    for (var l = function (e) {
                        var a = new FileReader;
                        a.onload = function (t) {
                            n.find(".image-viewer__item[data-id=" + e + "]").find("img").attr("src", t.target.result)
                        }, a.readAsDataURL(t.files[e])
                    }, c = s - 1; c >= 0; c--) l(c)
                }
                n.removeClass("is-loading")
            };

        function l(t) {
            t.closest(".table--cart").addClass("content-loading"), e.ajax({
                type: "POST",
                cache: !1,
                url: t.closest("form").prop("action"),
                data: new FormData(t.closest("form")[0]),
                contentType: !1,
                processData: !1,
                success: function (a) {
                    if (a.error) return window.showAlert("alert-danger", a.message), t.closest(".table--cart").removeClass("content-loading"), t.closest(".detail-qty").find(".qty-val").text(a.data.count), !1;
                    e(".section--shopping-cart").load(window.location.href + " .section--shopping-cart > *", (function () {
                        t.closest(".table--cart").removeClass("content-loading"), window.showAlert("alert-success", a.message), document.querySelector('.btn-checkout-full').disabled = false
                    })), e.ajax({
                        url: window.siteUrl + "/ajax/cart",
                        method: "GET",
                        success: function (t) {
                            t.error || (e(".cart-dropdown-wrap").html(t.data.html), e(".mini-cart-icon span").text(t.data.count))
                        }
                    })
                },
                error: function (e) {
                    t.closest(".table--cart").removeClass("content-loading"), window.showAlert("alert-danger", e.message)
                }
            })
        }
        e(document).on("change", ".form-review-product input[type=file]", (function (t) {
            t.preventDefault();
            var a = this,
                o = e(a),
                n = o.data("max-size");
            Object.keys(a.files).map((function (e) {
                if (n && a.files[e].size / 1024 > n) {
                    var t = o.data("max-size-message").replace("__attribute__", a.files[e].name).replace("__max__", n);
                    window.showAlert("alert-danger", t)
                } else s.push(a.files[e])
            }));
            var i = s.length,
                l = o.data("max-files");
            l && i > l && s.splice(i - l - 1, i - l), r(a)
        })), e(document).on("click", ".form-review-product .image-viewer__icon-remove", (function (t) {
            t.preventDefault();
            var a = e(t.currentTarget).closest(".image-viewer__item").data("id");
            s.splice(a, 1);
            var o = e(".form-review-product input[type=file]")[0];
            r(o)
        })), sessionStorage.reloadReviewsTab && (e('.nav-tabs li a[href="#Reviews"]').tab("show"), sessionStorage.reloadReviewsTab = !1), e(document).on("click", ".form-review-product button[type=submit]", (function (s) {
            var n = this;
            s.preventDefault(), s.stopPropagation(), e(this).prop("disabled", !0).addClass("btn-disabled").addClass("button-loading");
            var r = e(this).closest("form");
            e.ajax({
                type: "POST",
                cache: !1,
                url: r.prop("action"),
                data: new FormData(r[0]),
                contentType: !1,
                processData: !1,
                success: function (o) {
                    o.error ? t(o.message) : (r.find("select").val(0), r.find("textarea").val(""), a(o.message), setTimeout((function () {
                        sessionStorage.reloadReviewsTab = !0, window.location.reload()
                    }), 1500)), e(n).prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading")
                },
                error: function (t) {
                    e(n).prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading"), o(t)
                }
            })
        })), e(".form-coupon-wrapper .coupon-code").keypress((function (t) {
            if (13 === t.keyCode) return e(".apply-coupon-code").trigger("click"), t.preventDefault(), t.stopPropagation(), !1
        })), e(document).on("click", ".detail-qty .qty-up", (function (t) {

            t.preventDefault(), t.stopPropagation();
            var a = parseInt(e(this).closest(".detail-qty").find(".qty-val").val(), 10);

            a += 1, e(this).closest(".detail-qty").find("input").val(a), e(this).closest(".section--shopping-cart").length && l(e(this))
        })), e(document).on("click", ".detail-qty .qty-down", (function (t) {
            t.preventDefault(), t.stopPropagation();
            var a = parseInt(e(this).closest(".detail-qty").find(".qty-val").val(), 10);
            (a -= 1) > 1 || (a = 1), e(this).closest(".detail-qty").find("input").val(a).trigger("change"),
                a >= 0 && e(this).closest(".section--shopping-cart").length && l(e(this))
        })), e(document).on("change", ".section--shopping-cart .detail-qty .qty-val", (function () {

            l(e(this))
        }))
        $(document).on("click", ".btn-apply-coupon-code", function (t) {
            t.preventDefault();
            var a = $(t.currentTarget);
            var couponCode = a.closest(".form-coupon-wrapper").find(".coupon-code").val();

            a.prop("disabled", !0).addClass("btn-disabled").addClass("button-loading");

            if (couponCode.startsWith('SP')) {
                var shippingAmountValue = $('input[name="shippingAmount"]').val();
                var order_amount_str = $('#total').html();
                const order_amount = parseFloat(order_amount_str.replace(',', '.').replace('€', ''));

                // Send another axios request to an alternative endpoint.
                axios.post('/spc_apply', {
                    coupon_code: couponCode,
                    order_amount: order_amount,
                    shipping_amount: shippingAmountValue
                })
                    .then(response => {
                        // Handle the response
                        if (response.data.success) {
                            window.showAlert("alert-success", response.data.success);
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        } else {
                            window.showAlert("alert-danger", response.data.error);
                        }
                        a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading");
                    })
                    .catch(error => {
                        // Handle the error
                        console.log(error);
                        a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading");
                    });
            } else {
                $.ajax({
                    url: a.data("url"),
                    type: "POST",
                    data: {
                        coupon_code: couponCode
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function (t) {
                        t.error ? (window.showAlert("alert-danger", t.message), a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading")) : $(".section--shopping-cart").load(window.location.href + "?applied_coupon=1 .section--shopping-cart > *", function () {
                            a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading"), window.showAlert("alert-success", t.message)
                        })
                    },
                    error: function (t) {
                        if (undefined !== t.responseJSON) {
                            if ("undefined" !== t.responseJSON.errors) {
                                $.each(t.responseJSON.errors, function (t, a) {
                                    $.each(a, function (e, t) {
                                        window.showAlert("alert-danger", t)
                                    })
                                })
                            } else if (undefined !== t.responseJSON.message) {
                                window.showAlert("alert-danger", t.responseJSON.message)
                            }
                        } else {
                            window.showAlert("alert-danger", t.status.text);
                        }
                        a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading");
                    }
                });
            }
        })
            , e(document).on("click", ".btn-remove-coupon-code", (function (t) {
                t.preventDefault();
                var a = e(t.currentTarget);
                var couponCode = $('input[name="couponCode"]').val();
                o = a.text();
                var spc = couponCode.startsWith('SP-');
                if (!spc) {
                    a.text(a.data("processing-text")), e.ajax({
                        url: a.data("url"),
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": e('meta[name="csrf-token"]').attr("content")
                        },
                        success: function (t) {
                            t.error ? (window.showAlert("alert-danger", t.message), a.text(o)) : e(".section--shopping-cart").load(window.location.href + " .section--shopping-cart > *", (function () {
                                a.text(o)
                            }))
                        },
                        error: function (t) {
                            void 0 !== t.responseJSON ? "undefined" !== t.responseJSON.errors ? e.each(t.responseJSON.errors, (function (t, a) {
                                e.each(a, (function (e, t) {
                                    window.showAlert("alert-danger", t)
                                }))
                            })) : void 0 !== t.responseJSON.message && window.showAlert("alert-danger", t.responseJSON.message) : window.showAlert("alert-danger", t.status.text), a.text(o)
                        }
                    })
                } else {
                    a.text(a.data("processing-text"))
                    axios.post('/spc_remove', {})
                        .then(response => {
                            // Handle the response
                            if (response.data.success) {
                                window.showAlert("alert-success", response.data.success);
                                setTimeout(function () {
                                    location.reload();
                                }, 500);
                            } else {
                                window.showAlert("alert-danger", response.data.error);
                            }
                            a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading");
                        })
                        .catch(error => {
                            // Handle the error
                            console.log(error);
                            a.prop("disabled", !1).removeClass("btn-disabled").removeClass("button-loading");
                        });

                }

            })), e(document).on("click", ".js-remove-from-wishlist-button-wishlist", (function (t) {
                t.preventDefault();
                var a = e(this);
                a.addClass("button-loading"), e.ajax({
                    url: a.data("url"),
                    method: "DELETE",
                    success: function (t) {
                        if (t.error) return a.removeClass("button-loading"), window.showAlert("alert-danger", t.message), !1;
                        window.showAlert("alert-success", t.message), e(".wishlist-count span").text(t.data.count), a.removeClass("button-loading"), a.closest("tr").remove()
                    },
                    error: function (e) {
                        a.removeClass("button-loading"), window.showAlert("alert-danger", e.message)
                    }
                })
            })), e(window).on("load", (function () {
                var t = e("#flash-sale-modal");
                t.length && ! function (e) {
                    for (var t = e + "=", a = document.cookie.split(";"), o = 0; o < a.length; o++) {
                        for (var s = a[o];
                            " " == s.charAt(0);) s = s.substring(1);
                        if (0 == s.indexOf(t)) return s.substring(t.length, s.length)
                    }
                    return ""
                }(t.data("id")) && setTimeout((function () {
                    t.modal("show"),
                        function (e, t, a) {
                            var o = new Date,
                                s = new URL(window.siteUrl);
                            o.setTime(o.getTime() + 24 * a * 60 * 60 * 1e3);
                            var n = "expires=" + o.toUTCString();
                            document.cookie = e + "=" + t + "; " + n + "; path=/; domain=" + s.hostname
                        }(t.data("id"), 1, 1)
                }), 5e3)
            })), e(document).on("click", ".js-quick-view-button", (function (t) {
                t.preventDefault();
                var a = e("#quick-view-modal");
                a.find(".quick-view-content").html(""), a.find(".modal-body").addClass("modal-empty"), a.find(".loading-spinner").show(), a.modal("show"), e.ajax({
                    url: e(t.currentTarget).data("url"),
                    type: "GET",
                    success: function (t) {
                        t.error ? (window.showAlert("alert-danger", t.message), a.modal("hide")) : (a.find(".loading-spinner").hide(), a.find(".modal-body").removeClass("modal-empty"), a.find(".quick-view-content").html(t.data), a.find(".product-image-slider").slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            rtl: n,
                            arrows: !1,
                            fade: !1,
                            asNavFor: ".slider-nav-thumbnails"
                        }), a.find(".slider-nav-thumbnails").slick({
                            slidesToShow: 5,
                            slidesToScroll: 1,
                            rtl: n,
                            asNavFor: ".product-image-slider",
                            dots: !1,
                            focusOnSelect: !0,
                            prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
                            nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>'
                        }), a.find(".slider-nav-thumbnails .slick-slide").removeClass("slick-active"), a.find(".slider-nav-thumbnails .slick-slide").eq(0).addClass("slick-active"), a.find(".product-image-slider").on("beforeChange", (function (e, t, o, s) {
                            var n = s;
                            a.find(".slider-nav-thumbnails .slick-slide").removeClass("slick-active"), a.find(".slider-nav-thumbnails .slick-slide").eq(n).addClass("slick-active")
                        })), a.find(".product-image-slider").lightGallery({
                            selector: ".slick-slide:not(.slick-cloned) a",
                            thumbnail: !0,
                            share: !1,
                            fullScreen: !1,
                            autoplay: !1,
                            autoplayControls: !1,
                            actualSize: !1
                        }), e(".list-filter").each((function () {
                            e(this).find("a").on("click", (function (t) {
                                t.preventDefault(), e(this).parent().siblings().removeClass("active"), e(this).parent().toggleClass("active"), e(this).parents(".attr-detail").find(".current-size").text(e(this).text()), e(this).parents(".attr-detail").find(".current-color").text(e(this).attr("data-color"))
                            }))
                        })))
                    },
                    error: function () {
                        a.modal("hide")
                    }
                })
            }));
        var c = e("#products-filter-ajax"),
            d = e(".products-listing");

        function u(t) {
            c.find("input, select, textarea").each((function (a, o) {
                var s = e(o),
                    n = s.attr("name"),
                    r = t[n] || null;
                "checkbox" === s.attr("type") ? (s.prop("checked", !1), Array.isArray(r) ? s.prop("checked", r.includes(s.val())) : s.prop("checked", !!r)) : s.is("[name=max_price]") ? s.val(r || s.data("max")) : s.is("[name=min_price]") ? s.val(r || s.data("min")) : s.val() != r && s.val(r), s.trigger("change")
            }))
        }

        function p(t) {
            t || (t = c.serializeArray());
            var a = m(t),
                o = !1;
            a && a.length && a.map((function (e) {
                var t;
                t = "[]" == e.name.substring(e.name.length - 2) ? '[name="' + e.name + '"][value="' + e.value + '"]' : '[name="' + e.name + '"]', c.find(t).length && (o = !0)
            })), e(".shop-filter-toogle").length && (o ? e(".shop-filter-toogle").addClass("is-filtering") : e(".shop-filter-toogle").removeClass("is-filtering"))
        }

        function m(e) {
            var t = [];
            return e.forEach((function (e) {
                if (e.value) {
                    if (["min_price", "max_price"].includes(e.name) && c.find("input[name=" + e.name + "]").data(e.name.substring(0, 3)) == parseInt(e.value)) return;
                    t.push(e)
                }
            })), t
        }

        function f(e) {
            for (var t, a = arguments.length > 1 && void 0 !== arguments[1] && arguments[1], o = e || window.location.search.substring(1), s = /([^&=]+)=?([^&]*)/g, n = /\+/g, r = function (e) {
                return decodeURIComponent(e.replace(n, " "))
            }, i = {}; t = s.exec(o);) {
                var l = r(t[1]),
                    c = r(t[2]);
                "[]" == l.substring(l.length - 2) ? (a && (l = l.substring(0, l.length - 2)), (i[l] || (i[l] = [])).push(c)) : i[l] = c
            }
            return i
        }
        e(document).on("click", ".clear_filter.clear_all_filter", (function (e) {
            e.preventDefault(), u([]), c.trigger("submit")
        })), e(document).on("click", ".clear_filter.bf_icons", (function (t) {
            t.preventDefault();
            var a, o = e(t.currentTarget),
                s = o.data("name"),
                n = o.data("value");
            if ("[]" == s.substring(s.length - 2)) "checkbox" === (a = c.find('[name="' + s + '"][value="' + n + '"]')).attr("type") ? a.prop("checked", !1) : a.val(null);
            else switch ((a = c.find('[name="' + s + '"]')).attr("name")) {
                case "min_price":
                    a.val(a.data("min"));
                    break;
                case "max_price":
                    a.val(a.data("max"));
                    break;
                default:
                    a.val(null)
            }
            a && a.trigger("change"), c.trigger("submit")
        })), e(document).on("change", ".product-category-select", (function () {
            e(".product-cat-label").text(e.trim(e(this).find("option:selected").text()))
        })), e(".product-cat-label").text(e.trim(e(".product-category-select option:selected").text())), e(document).on("click", ".show-advanced-filters", (function (t) {
            t.preventDefault(), t.stopPropagation(), e(this).toggleClass("active"), e(".advanced-search-widgets").slideToggle(500)
        })), p(), c.length && (e(document).on("submit", "#products-filter-ajax", (function (a) {
            a.preventDefault();
            var s = e(a.currentTarget),
                n = s.serializeArray(),
                r = m(n),
                i = [];
            d.find("input").map((function (t, a) {
                var o = e(a);
                o.val() && r.push({
                    name: o.attr("name"),
                    value: o.val()
                })
            })), r.map((function (e) {
                i.push(encodeURIComponent(e.name) + "=" + e.value)
            }));
            var l = s.attr("action") + (i && i.length ? "?" + i.join("&") : "");
            r.push({
                name: "s",
                value: 1
            }), e.ajax({
                url: s.attr("action"),
                type: "GET",
                data: r,
                beforeSend: function () {
                    d.find(".list-content-loading").show(), window.closeShopFilterSection && window.closeShopFilterSection(), e("html, body").animate({
                        scrollTop: c.offset().top - e("header").height()
                    }, 500)
                },
                success: function (e) {
                    0 == e.error ? (d.html(e.data), l != window.location.href && window.history.pushState(r, e.message, l), p(n)) : t(e.message || "Opp!")
                },
                error: function (e) {
                    o(e)
                },
                complete: function () {
                    $("#wishlistAction a").click(function () {
                        if ($(this).find(".fa-heart").hasClass('fas')) {
                            $(this).find(".fa-heart").removeClass('fas');
                            $(this).find(".fa-heart").addClass('far');
                            $(this).find(".fa-heart").css("color", "#005BA1");
                        } else {
                            $(this).find(".fa-heart").removeClass('far');
                            $(this).find(".fa-heart").addClass('fas');
                            $(this).find(".fa-heart").css("color", "red");
                        }

                    });
                    $('.discounted-carousel').owlCarousel({
                        autoplay: true,
                        autoplayTimeout: 2000,
                        autoplayHoverPause: true,
                        stagePadding: 0,/*the little visible images at the end of the carousel*/
                        loop: true,
                        rtl: false,
                        lazyLoad: true,
                        autoHeight: true,
                        margin: 10,
                        nav: false,
                        responsive: {
                            0: {
                                items: 1
                            },
                            600: {
                                items: 1
                            },
                            800: {
                                items: 1
                            },
                            1000: {
                                items: 2
                            },
                            1200: {
                                items: 3
                            }
                        }
                    })

                    d.find(".list-content-loading").hide()
                }
            })
        })), window.addEventListener("popstate", (function () {
            var e = window.location.origin + window.location.pathname;
            c.attr("action") == e ? (u(f()), c.trigger("submit")) : history.back()
        }), !1), e(document).on("click", ".products-listing .pagination-page a", (function (t) {
            t.preventDefault();
            var a = e(t.currentTarget).attr("href");
            a.includes(window.location.protocol) || (a = window.location.protocol + a);
            var o = new URL(a).searchParams.get("page");
            d.find("input[name=page]").val(o), c.trigger("submit")
        })), e(document).on("click", ".products_sortby .products_ajaxsortby a", (function (t) {
            t.preventDefault();
            var a = e(t.currentTarget),
                o = a.attr("href"),
                s = a.closest(".products_ajaxsortby");
            if (s.find("a.selected").removeClass("selected"), a.addClass("selected"), o.indexOf("?") >= 0) {
                var n = o.substring(o.indexOf("?") + 1);
                if (n) {
                    var r = f(n);
                    d.find('input[name="' + s.data("name") + '"]').val(r[s.data("name")])
                }
            }
            c.trigger("submit")
        })), e(document).on("change", ".category-filter-input", (function (t) {
            var a = e(t.currentTarget),
                o = a.prop("checked");
            if (e(".category-filter-input[data-parent-id=" + a.attr("data-id") + "]").each((function (t, a) {
                o ? e(a).prop("checked", !0) : e(a).prop("checked", !1)
            })), 0 !== parseInt(a.attr("data-parent-id"))) {
                var s = [],
                    n = e(".category-filter-input[data-parent-id=" + a.attr("data-parent-id") + "]");
                n.each((function (t, a) {
                    e(a).is(":checked") && s.push(e(a).val())
                })), e(".category-filter-input[data-id=" + a.attr("data-parent-id") + "]").prop("checked", s.length === n.length)
            }
        })))
    }))
}(jQuery);
