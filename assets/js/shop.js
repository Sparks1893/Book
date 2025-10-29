/**
 * Bookshive Shop Interactivity
 * Author: E. Durant
 * Description: Adds animations, wishlist handling, and modal previews for the Bookshive shop.
 */

(function ($) {
  'use strict';

  const Shop = {
    init: function () {
      this.bindUI();
    },

    bindUI: function () {
      // Hover card animation (already has CSS, but add touch support)
      $('.bookshive-product-card')
        .on('touchstart', function () {
          $(this).addClass('hover');
        })
        .on('touchend', function () {
          $(this).removeClass('hover');
        });

      // Wishlist button click
      $(document).on('click', '.wishlist-btn', this.handleWishlist);

      // Optional Quick View Modal
      $(document).on('click', '.quick-view-btn', this.handleQuickView);
    },

    /**
     * Handles adding or removing a book from wishlist
     */
    handleWishlist: function (e) {
      e.preventDefault();
      const $btn = $(this);
      const bookId = $btn.closest('.bookshive-product-card').data('book-id');

      $btn.prop('disabled', true).text('Saving...');

      $.post(bookshiveAjax.ajax_url, {
        action: 'bookshive_toggle_wishlist',
        nonce: bookshiveAjax.nonce,
        book_id: bookId,
      })
        .done((response) => {
          if (response.success) {
            const newState = response.data.added ? 'Added 💖' : 'Removed 💔';
            $btn.text(newState).toggleClass('active', response.data.added);
          } else {
            $btn.text('Error ❌');
          }
        })
        .fail(() => {
          $btn.text('Error ❌');
        })
        .always(() => {
          setTimeout(() => {
            $btn.prop('disabled', false).text('Wishlist');
          }, 2000);
        });
    },

    /**
     * Opens a modal for quick book preview
     */
    handleQuickView: function (e) {
      e.preventDefault();
      const bookId = $(this).closest('.bookshive-product-card').data('book-id');

      const $modal = $('#bookshive-quickview-modal');
      $modal.addClass('open').find('.content').html('<p>Loading book details...</p>');

      $.post(bookshiveAjax.ajax_url, {
        action: 'bookshive_get_book_preview',
        nonce: bookshiveAjax.nonce,
        book_id: bookId,
      })
        .done((response) => {
          if (response.success) {
            $modal.find('.content').html(response.data.html);
          } else {
            $modal.find('.content').html('<p>Unable to load details.</p>');
          }
        });
    },
  };

  $(document).ready(() => Shop.init());

  /**
   * Simple modal close handler
   */
  $(document).on('click', '#bookshive-quickview-modal .close', function () {
    $('#bookshive-quickview-modal').removeClass('open');
  });

})(jQuery);
