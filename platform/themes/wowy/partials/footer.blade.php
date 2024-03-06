{!! dynamic_sidebar('top_footer_sidebar') !!}
<footer class="main">
    <section class="section-padding-60">
        <div class="container">
            <div class="row">
                {!! dynamic_sidebar('footer_sidebar') !!}
            </div>
        </div>
    </section>
    <div class="container pb-20 wow fadeIn animated">
        <div class="row">
            <div class="col-12 mb-20">
                <div class="footer-bottom"></div>
            </div>
            <div class="col-lg-6">
                <p class="float-md-left font-sm text-muted mb-0">{{ theme_option('copyright') }}</p>
            </div>
            <div class="col-lg-3"><a href="/cookie-policy">Politica Sui Cookie E Sulla Privacy</a></div>
            <div class="col-lg-3">
                <p class="text-lg-end text-start font-sm text-muted mb-0">
                    {{ __('All rights reserved.') }}

                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Quick view -->
<div class="modal fade custom-modal" id="quick-view-modal" tabindex="-1" aria-labelledby="quick-view-modal-label"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="half-circle-spinner loading-spinner">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div class="quick-view-content"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

@if (is_plugin_active('ecommerce'))
    <script>
        window.currencies = {!! json_encode(get_currencies_json()) !!};
    </script>
@endif

{!! Theme::footer() !!}

<script>
    window.trans = {
        "Views": "{{ __('Views') }}",
        "Read more": "{{ __('Read more') }}",
        "days": "{{ __('days') }}",
        "hours": "{{ __('hours') }}",
        "mins": "{{ __('mins') }}",
        "sec": "{{ __('sec') }}",
        "No reviews!": "{{ __('No reviews!') }}"
    };
</script>

{!! Theme::place('footer') !!}

@if (session()->has('success_msg') ||
        session()->has('error_msg') ||
        (isset($errors) && $errors->count() > 0) ||
        isset($error_msg))
    <script type="text/javascript">
        window.onload = function() {
            @if (session()->has('success_msg'))
                window.showAlert('alert-success', '{{ session('success_msg') }}');
            @endif

            @if (session()->has('error_msg'))
                window.showAlert('alert-danger', '{{ session('error_msg') }}');
            @endif

            @if (isset($error_msg))
                window.showAlert('alert-danger', '{{ $error_msg }}');
            @endif

            @if (isset($errors))
                @foreach ($errors->all() as $error)
                    window.showAlert('alert-danger', '{!! BaseHelper::clean($error) !!}');
                @endforeach
            @endif
        };
    </script>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
    // $("#owl-demo").owlCarousel({
    //
    //     autoPlay: 3000, //Set AutoPlay to 3 seconds
    //
    //     items : 4,
    //     itemsDesktop : [1199,3],
    //     itemsDesktopSmall : [979,3]
    //
    // });
    // $('.brands-carousel').owlCarousel({
    //     autoPlay: 3000, //Set AutoPlay to 3 seconds
    //         items : 4,
    //         itemsDesktop : [1199,3],
    //         itemsDesktopSmall : [979,3]
    // })
    $('.featured-brands-carousel').owlCarousel({
        autoplay: true,
        autoplayTimeout: 2000,
        autoplayHoverPause: true,
        stagePadding: 50,
        /*the little visible images at the end of the carousel*/
        loop: true,
        rtl: false,
        lazyLoad: true,
        autoHeight: true,
        margin: -50,
        nav: false,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            800: {
                items: 3
            },
            1000: {
                items: 4
            },
            1200: {
                items: 4
            }
        }
    })
</script>

<script>
    $(".form--auth--btn").click(function(e) {
        e.preventDefault();
        let captchaInput = $("#captcha-login").val();

        axios.post('/captcha-validator/login', {
                captcha: captchaInput
            })
            .then(response => {
                if (response.data.valid) {
                    $('.captcha-error').html('');
                    $(".form--auth").submit();

                }
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    $('.captcha-error').html('La somma inserita non è corretta');
                    refreshLoginForm();
                }
            });

    });


    $("#contact-form-btn").click(function(e) {
        e.preventDefault();

        let captchaInput = $("#captcha-contact").val();

        axios.post('/captcha-validator/contact-form', {
                captcha: captchaInput
            })
            .then(response => {
                if (response.data.valid) {
                    $('.captcha-error').html('');

                }
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    $('.captcha-error').html('La somma inserita non è corretta');
                    refreshContactForm();
                }
            });
    });

    function refreshContactForm() {
        axios.get('/refresh-captcha/contact-form')
            .then(response => {
                // Update the CAPTCHA image source with the new data URI
                $('.captcha-value img').attr('src', response.data.dataUri);
            })
            .catch(error => {
                console.error('Failed to refresh CAPTCHA', error);
            });
    }


    $('.email-controll-registration').on('keyup', function() {
        var email = $(this).val().trim();

        // Only proceed if the email is not empty
        if (email) {
            // Send a request to your endpoint
            axios.post('checkEmailAlreadyexists', {
                    email: email
                })
                .then(function(response) {
                    // Handle the response here
                    // For example, if email exists, show a message or change input style
                    if (response.data.exists) {
                        // Email exists - handle accordingly
                        $('.email-controll-registration').addClass('red-border');
                        $('#realtime-email-error').css('display', 'block');
                        console.log(response);
                        $('#realtime-email-error').html(response.data.msg);

                    } else {
                        // Email does not exist - handle accordingly
                        $('.email-controll-registration').removeClass('red-border');
                        $('#realtime-email-error').css('display', 'none');
                    }
                })
                .catch(function(error) {
                    // Handle errors here, if any
                    console.error('Error checking email:', error);
                });
        }
    });


    $(".register--btn--submit").click(function(e) {
        e.preventDefault();
        let allValid = true;

        // Loop through each text input field in the form
        $("#registration-form input[type='text']").each(function() {
            if ($(this).val().trim() === "") {
                allValid = false;
                $(this).css('border', '1px solid red');
            } else {
                $(this).css('border', '');
            }
        });
        $("#registration-form input[type='email']").each(function() {
            if ($(this).val().trim() === "") {
                allValid = false;
                $(this).css('border', '1px solid red');
            } else {
                $(this).css('border', '');
            }
        });
        $("#registration-form input[type='password']").each(function() {
            if ($(this).val().trim() === "") {
                allValid = false;
                $(this).css('border', '1px solid red');
            } else {
                $(this).css('border', '');
            }
        });
        if ($('#realtime-email-error').css('display') === 'block') {
            allValid = false;
        }
        let isCheckboxChecked = false;

        $("#registration-form input[type='checkbox']").each(function() {
            if ($(this).is(':checked')) {
                isCheckboxChecked = true;
                return false; // Break the loop as we found a checked checkbox
            }
        });

        if (isCheckboxChecked) {
            $('.custome-checkbox').css('border', ''); // Reset border if a checkbox is checked
        } else {
            $('.custome-checkbox').css('border', '1px solid red'); // Set red
        }

        // CAPTCHA validation
        let captchaInput = $("#captcha-register").val();
        axios.post('/captcha-validator/register', {
                captcha: captchaInput
            })
            .then(response => {
                if (response.data.valid && allValid && isCheckboxChecked) {
                    // If CAPTCHA and all other validations are passed, submit the form
                    $('.form--auth').submit();
                } else if (response.data.valid && !allValid) {

                } else {
                    // Handle CAPTCHA validation failure
                    $('.captcha-error').html('La somma non è corretta');
                }
            })
            .catch(error => {
                if (error.response && error.response.status === 422) {
                    // Handle specific error here for CAPTCHA
                    $('.captcha-error').html('La somma non è corretta!');
                    refreshRegisterFormCaptcha();
                }
            });

    });

    function refreshRegisterFormCaptcha() {
        axios.get('/refresh-captcha/register')
            .then(response => {
                // Update the CAPTCHA image source with the new data URI
                $('.captcha-value img').attr('src', response.data.dataUri);
            })
            .catch(error => {
                console.error('Failed to refresh CAPTCHA', error);
            });
    }


    $(document).ready(function() {


        const $passwordField = $("#txt-password");
        const $togglePasswordButton = $("#toggle-password");
        if (window.location.href.includes('register')) {
            $togglePasswordButton.on("click", function() {
                // Check if the URL contains 'was'

                // Execute the toggle functionality only if the URL contains 'was'
                if ($passwordField.attr("type") === "password") {
                    $passwordField.attr("type", "text");
                    $togglePasswordButton.removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    $passwordField.attr("type", "password");
                    $togglePasswordButton.removeClass("fa-eye-slash").addClass("fa-eye");
                }

            });
        }


        const $passwordField1 = $("#txt-password1");
        const $togglePasswordButton1 = $("#toggle-password");
        if (window.location.href.includes('login')) {
            $togglePasswordButton.on("click", function() {
                // Check if the URL contains 'was'

                // Execute the toggle functionality only if the URL contains 'was'
                if ($passwordField1.attr("type") === "password") {
                    $passwordField1.attr("type", "text");
                    $togglePasswordButton1.removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    $passwordField1.attr("type", "password");
                    $togglePasswordButton1.removeClass("fa-eye-slash").addClass("fa-eye");
                }

            });
        }



        const $password_confirmation_Field = $("#txt-password-confirmation");
        const $togglePassword_confirmation_Button = $("#toggle-password-confirmation");

        $togglePassword_confirmation_Button.on("click", function() {
            if ($password_confirmation_Field.attr("type") === "password") {
                $password_confirmation_Field.attr("type", "text");
                $togglePassword_confirmation_Button.removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                $password_confirmation_Field.attr("type", "password");
                $togglePassword_confirmation_Button.removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });



        //for the change password

        const $password_old1_Field = $("#old_password");
        const $togglePassword_old1_Button = $("#toggle-old-password");

        $togglePassword_old1_Button.on("click", function() {
            if ($password_old1_Field.attr("type") === "password") {
                $password_old1_Field.attr("type", "text");
                $togglePassword_old1_Button.removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                $password_old1_Field.attr("type", "password");
                $togglePassword_old1_Button.removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });


        const $password1_Field = $("#password");
        const $togglePassword1_Button = $("#toggle-password");

        $togglePassword1_Button.on("click", function() {
            if ($password1_Field.attr("type") === "password") {
                $password1_Field.attr("type", "text");
                $togglePassword1_Button.removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                $password1_Field.attr("type", "password");
                $togglePassword1_Button.removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });


        const $password_confirmation1_Field = $("#password_confirmation");
        const $togglePassword_confirmation1_Button = $("#toggle-password-confirmation");

        $togglePassword_confirmation1_Button.on("click", function() {
            if ($password_confirmation1_Field.attr("type") === "password") {
                $password_confirmation1_Field.attr("type", "text");
                $togglePassword_confirmation1_Button.removeClass("fa-eye").addClass("fa-eye-slash");
            } else {
                $password_confirmation1_Field.attr("type", "password");
                $togglePassword_confirmation1_Button.removeClass("fa-eye-slash").addClass("fa-eye");
            }
        });

    });


    $(document).on("keyup", "input[name='password']", function(e) {
        if (!window.location.pathname.includes("/login")) {
            console.log('ow');

            e.preventDefault();
            var password = $(this).val();
            var validationResult = validatePassword(password);

            // Remove any existing error spans with class 'password-error'
            $(this).next('#password-error').remove();
            $(this).closest('div').find('#password-error').remove();

            if (validationResult == "La password è forte.") {
                $(this).removeClass('is-invalid');
                $(this).addClass('is-valid');
                $(this).after('<span id="password-error" class="valid-feedback" style="display:block">' +
                    validationResult + '</span>');
            } else {
                $(this).removeClass('is-valid');
                $(this).addClass('is-invalid');
                $(this).after('<span id="password-error" class="invalid-feedback" style="display:block">' +
                    validationResult + '</span>');

            }
        }
    })


    $(document).on("blur", "input[name='password']", function(e) {
        e.preventDefault();
        if (window.location.href.includes('register')) {
            var password = $(this).val();
            var validationResult = validatePassword(password);

            $(this).next('#password-error').remove();
            $(this).closest('div').find('#password-error').remove();

            // Remove any existing error spans with class 'password-error'

            if (validationResult == "La password è forte.") {
                $(this).removeClass('is-invalid');
                $(this).addClass('is-valid');
                $(this).after('<span id="password-error" class="valid-feedback" style="display:block">' +
                    validationResult + '</span>');

            } else {
                $(this).removeClass('is-valid');
                $(this).addClass('is-invalid');
                $(this).after('<span id="password-error" class="invalid-feedback" style="display:block">' +
                    validationResult + '</span>');

            }
        }

    })


    $(document).on("click", ".form-check-input", async function() {
        await $(this).closest('#products-filter-ajax').submit();


    });

    // Debounce function to limit the rate of function execution
    function debounce(func, delay) {
        let debounceTimer;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Adjusted event handler with delegation for dynamically loaded elements
    $('body').on('keyup', '#search-consumabili', debounce(function() {
        // Ensure this element's closest form or the specific AJAX container is correctly targeted
        // If `#products-filter-ajax` is the form ID, you might want to use .closest('form') to ensure you're submitting the correct form
        $(this).closest('form')
            .submit(); // Adjusted to use 'form' assuming `#products-filter-ajax` is a form
    }, 800));

    $(document).on('click', '.category-check', function() {
        // Clear any existing timeout to prevent multiple triggers
        if (window.categoryTimeout) {
            clearTimeout(window.categoryTimeout);
        }

        // Set a new timeout
        window.categoryTimeout = setTimeout(function() {
            updateFilters('category');
        }, 1200); // Delay of 500 milliseconds
    });

    $(document).on('click', '.brands-check', function() {
        // Clear any existing timeout to prevent multiple triggers
        if (window.brandTimeout) {
            clearTimeout(window.brandTimeout);
        }

        // Set a new timeout
        window.brandTimeout = setTimeout(function() {
            updateFilters('brand');
        }, 1200); // Delay of 500 milliseconds
    });

    function checkCheckboxesBasedOnURL() {
        // Get the full URL
        setTimeout(function() {
            const url = window.location.href;
            // Create a URLSearchParams object
            const urlParams = new URLSearchParams(window.location.search);
            // For categories
            const categories = urlParams.getAll('categories[]');
            categories.forEach(function(categoryId) {
                console.log('category', categoryId)

                var checkbox = $('#category-filter-' + categoryId);
                // Toggle the checked property
                checkbox.attr('checked', !checkbox.attr('checked'));
            });
            const brands = urlParams.getAll('brands[]');
            brands.forEach(function(brandId) {
                console.log('brand', brandId)
                var checkbox = $('#brand-filter-' + brandId);
                // Toggle the checked property
                checkbox.attr('checked', !checkbox.attr('checked'));
            });
        }, 500)

    }

    // Call the function on page load

    function updateFilters(type) {
        var categoryIds = $('.category-check:checked').map(function() {
            return $(this).val(); // Assuming the value of the checkbox contains the ID
        }).get();

        // Collect all checked brand IDs
        var brandIds = $('.brands-check:checked').map(function() {
            return $(this).val(); // Assuming the value of the checkbox contains the ID
        }).get();

        $.ajax({
            url: '/updateFilter',
            type: 'POST',
            data: {
                type: type,
                brandIds: brandIds,
                categoryIds: categoryIds,
                _token: '{{ csrf_token() }}'
            },
            success: async function(response) {
                await $('#products-filter-ajax').html(response.html);
                checkCheckboxesBasedOnURL();
            }
        });

    }


    $(document).ready(function() {
        // Handle hover event
        $('.product-cart-wrap').hover(
            function() {
                var href = $(this).find('.hidden-product-id').val();
                axios.post('/saveInteraction', {
                        href: href,
                        action: 'hover'
                    })
                    .then(function(response) {
                        console.log(response.data);
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            },
            function() {
                // Code for "mouseleave" event goes here
            }
        );

        $('.product-cart-wrap .button-add-to-cart').click(function() {
            var href = $(this).find('.hidden-product-id').val();
            axios.post('/saveInteraction', {
                    href: href,
                    action: 'basket'
                })
                .then(function(response) {
                    console.log(response.data);
                })
                .catch(function(error) {
                    console.log(error);
                });
        });

    });





    function adjustCardHeights() {
        const cards = document.querySelectorAll('.product-content-wrap h2');
        const maxHeight = Math.max(...Array.from(cards).map(card => card.offsetHeight));
        cards.forEach(card => card.style.height = `${maxHeight}px`);
    }

    function adjustCardHeights1() {
        const cards = document.querySelectorAll('.cart-related-wrap');
        const maxHeight = Math.max(...Array.from(cards).map(card => card.offsetHeight));
        cards.forEach(card => card.style.height = `${maxHeight}px`);
    }
    if (window.location.pathname.includes('/products')) {
        // Setup MutationObserver to watch for changes in the container
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.type === 'childList') {
                    // Assuming changes in children imply new content was added
                    adjustCardHeights1();
                }
            });
        });

        // Start observing
        const config = {
            childList: true,
            subtree: true
        };
        observer.observe(document.querySelector('.related-listing'), config);

    }
    // Initial call for page load
    adjustCardHeights1();


    if (window.location.pathname.includes('/products')) {
        console.log('products')
        // Setup MutationObserver to watch for changes in the container
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.type === 'childList') {
                    // Assuming changes in children imply new content was added
                    adjustCardHeights();
                }
            });
        });

        // Start observing
        const config = {
            childList: true,
            subtree: true
        };
        observer.observe(document.querySelector('.products-listing'), config);

    }
    // Initial call for page load
    adjustCardHeights();




    function validatePassword(password) {
        // At least 8 characters long
        if (password.length < 6) {
            return "password deve contenere almeno 6 caratteri.";
        }

        // Contains at least one uppercase letter
        if (!/[A-Z]/.test(password)) {
            return "La password deve contenere almeno una lettera maiuscola.";
        }

        // Contains at least one lowercase letter
        if (!/[a-z]/.test(password)) {
            return "La password deve contenere almeno una lettera minuscola.";
        }

        // Contains at least one number
        if (!/[0-9]/.test(password)) {
            return "La password deve contenere almeno un numero.";
        }

        // Contains at least one special character
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            return "La password deve contenere almeno un carattere speciale.";
        }

        // If all conditions are met, the password is strong
        return "La password è forte.";
    }

    $('.discounted-carousel').owlCarousel({
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        stagePadding: 0,
        /*the little visible images at the end of the carousel*/
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
                items: 2
            },
            800: {
                items: 3
            },
            1000: {
                items: 4
            },
            1200: {
                items: 4
            }
        }
    })



    // Select all input fields in the form
</script>



<div id="scrollUp"><i class="fal fa-long-arrow-up"></i></div>
</body>

</html>
