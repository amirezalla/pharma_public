$(document).ready((function () {
    var e = function (e) {
        $(".contact-error-message").html(e).show()
    },
        s = function (s) {
            var t = "";
            $.each(s, (function (e, s) {
                "" !== t && (t += "<br />"), t += s
            })), e(t)
        };
    $(document).on("click", ".contact-form button[type=submit]", (function (t) {
        var o = this;



        t.preventDefault(), t.stopPropagation(), $(this).addClass("button-loading"), $(".contact-success-message").html("").hide(), $(".field-error-message").html("").hide();
        var privacyCheckbox = document.getElementById('privacyPolicy');
        var errorMessage = document.getElementById('errorMessage');





        if (!privacyCheckbox.checked) {
            errorMessage.style.display = 'block';
            t.preventDefault(); $(this).removeClass("button-loading");

        } else {
            var subjectSelect = $("#subject");
            if (subjectSelect.val() == '0') {
                $("#subjectError").html("Per favore, seleziona un argomento."); // Inserting error text into the subjectError element
                $("#subjectError").css('display', 'block');
                t.preventDefault(); $(this).removeClass("button-loading");

            } else {
                $("#subjectError").html("");
                $.ajax({
                    type: "POST",
                    cache: !1,
                    url: $(this).closest("form").prop("action"),
                    data: new FormData($(this).closest("form")[0]),
                    contentType: !1,
                    processData: !1,
                    success: function (s) {
                        var t;
                        s.error ? e(s.message) : ($(o).closest("form").find("input[type=text]").val(""), $(o).closest("form").find("input[type=email]").val(""), $(o).closest("form").find("textarea").val(""), t = s.message, $(".contact-success-message").html(t).show()), $(o).removeClass("button-loading"), "undefined" != typeof refreshRecaptcha && refreshRecaptcha()
                    },
                    error: function (t) {
                        var r;
                        "undefined" != typeof refreshRecaptcha && refreshRecaptcha(), $(o).removeClass("button-loading");

                        if (t.responseJSON && t.responseJSON.errors) {
                            // Clear all previous error messages first
                            $('.field-error-message').html('').hide();

                            var errors = t.responseJSON.errors;
                            for (var field in errors) {
                                if (errors.hasOwnProperty(field) && errors[field].length) {
                                    // Display the first error message for each field
                                    $('#' + field + 'Error').html(errors[field][0]).show();
                                }
                            }
                        } else {
                            // Your existing error handling code
                            void 0 !== r.responseJSON.message ? e(r.responseJSON.message) : $.each(r.responseJSON, function (s, t) {
                                $.each(t, function (s, t) {
                                    e(t);
                                });
                            });
                        }
                    }

                })
                errorMessage.style.display = 'none';
                // This will clear any previous error messages when the form is valid
            }

        }

    }))
}));