jQuery(document).ready(function($){
    $('.plm-btn-wishlist').click(function(){
        let book = $(this).data('book');
        let button = $(this);
        $.post(plm_ajax.ajax_url, {
            action:'plm_toggle_wishlist',
            book_id:book
        }, function(response){
            if(response === 'added') {
                button.text('✓ In Wishlist');
            } else {
                button.text('♡ Wishlist');
            }
        });
    });
});
// Favorite toggle
$('.plm-btn-favorite').click(function(){
    let book = $(this).data('book');
    let button = $(this);
    $.post(plm_ajax.ajax_url, {
        action:'plm_toggle_favorite',
        book_id:book
    }, function(response){
        if(response === 'added') button.text('❤️ Favorited');
        else button.text('❤️ Favorite');
    });
});

// Like toggle
$('.plm-btn-like').click(function(){
    let book = $(this).data('book');
    let button = $(this);
    $.post(plm_ajax.ajax_url, {
        action:'plm_toggle_like',
        book_id:book
    }, function(response){
        if(response === 'added') button.text('👍 Liked');
        else button.text('👍 Like');
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const actionButtons = document.querySelectorAll(".plm-action-btn");

    actionButtons.forEach(button => {
        button.addEventListener("click", function () {
            const bookCard = this.closest(".plm-book-card");
            const bookId = bookCard.getAttribute("data-book-id");
            const actionType = this.getAttribute("data-action");

            // Disable button briefly to prevent spam
            this.classList.add("plm-loading");
            this.disabled = true;

            fetch(plm_ajax.ajax_url, {
                method: "POST",
                credentials: "same-origin",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: `plm_toggle_${actionType}`,
                    book_id: bookId,
                    nonce: plm_ajax.nonce
                })
            })
                .then(response => response.json())
                .then(data => {
                    this.classList.remove("plm-loading");
                    this.disabled = false;

                    if (data.success) {
                        // Toggle button active state
                        this.classList.toggle("active");

                        // Pop confirmation toast
                        if (typeof showToast === "function") {
                            if (data.data === "added") {
                                showToast(`✅ Added to ${actionType}`, "success");
                            } else {
                                showToast(`❌ Removed from ${actionType}`, "info");
                            }
                        }
                    } else if (data.data === "login_required") {
                        showToast("⚠️ Please log in to use this feature", "warning");
                    } else {
                        showToast("❗ Error, please try again", "error");
                    }
                })
                .catch(() => {
                    this.classList.remove("plm-loading");
                    this.disabled = false;
                    showToast("🚫 Connection error", "error");
                });
        });
    });
});
