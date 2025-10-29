/**
 * Bookshive Recommendations JS
 * Author: E. Durant
 * Description: Dynamically fetches and displays book recommendations.
 */

(function ($) {
  'use strict';

  const $container = $('#bookshive-recommendations .recommendations-container');

  if ($container.length) {
    const userId = $container.data('user');

    const data = {
      action: 'bookshive_get_recommendations',
      nonce: bookshiveAjax.nonce,
      user_id: userId
    };

    // Display loading indicator
    $container.html('<p class="loading">📖 Fetching your recommendations...</p>');

    $.post(bookshiveAjax.ajax_url, data, function (response) {
      if (response.success && response.data.html) {
        $container.hide().html(response.data.html).fadeIn(400);
      } else {
        $container.html('<p class="empty">No recommendations found at the moment. Try adding more books!</p>');
      }
    });
  }

  /**
   * Optional: Add interaction for users to save recommended books
   */
  $(document).on('click', '.bookshive-book-card .save-recommendation', function () {
    const bookId = $(this).closest('.bookshive-book-card').data('book-id');
    const data = {
      action: 'bookshive_add_book_to_library',
      nonce: bookshiveAjax.nonce,
      book_id: bookId
    };

    const $btn = $(this);
    $btn.prop('disabled', true).text('Adding...');

    $.post(bookshiveAjax.ajax_url, data, function (response) {
      if (response.success) {
        $btn.text('Added ✅');
      } else {
        $btn.text('Error ❌');
      }
    });
  });

})(jQuery);
