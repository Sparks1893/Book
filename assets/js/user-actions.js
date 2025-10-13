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
