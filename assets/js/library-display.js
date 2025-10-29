/**
 * Bookshive Library Display JS
 * Author: E. Durant
 * Handles user filters, layouts, and reading status updates.
 */

(function($) {
  'use strict';

  const $library = $('#bookshive-library');
  const $container = $('#bookshive-books-container');
  const $filterForm = $('#bookshive-filter-form');

  /**
   * Handle book filtering (genre, author, rating, spice)
   */
  $filterForm.on('submit', function(e) {
    e.preventDefault();

    const data = {
      action: 'bookshive_filter_books',
      nonce: bookshiveAjax.nonce,
      genre: $('#bookshive-filter-genre').val(),
      author: $('#bookshive-filter-author').val(),
      rating: $('#bookshive-filter-rating').val(),
      spice: $('#bookshive-filter-spice').val()
    };

    $container.addClass('loading');
    $.post(bookshiveAjax.ajax_url, data, function(response) {
      $container.removeClass('loading');
      if (response.success) {
        $container.html(response.data.html);
      } else {
        $container.html('<p class="bookshive-empty">No matching books found.</p>');
      }
    });
  });

  /**
   * Handle layout toggle buttons
   */
  $('.bookshive-view-toggle button').on('click', function() {
    $('.bookshive-view-toggle button').removeClass('active');
    $(this).addClass('active');
    const layout = $(this).data('layout');
    $library.attr('data-layout', layout);

    if (layout === 'list') {
      $library.addClass('layout-list').removeClass('layout-grid layout-shelf');
    } else if (layout === 'shelf') {
      $library.addClass('layout-shelf').removeClass('layout-grid layout-list');
    } else {
      $library.addClass('layout-grid').removeClass('layout-list layout-shelf');
    }
  });

  /**
   * Handle status change buttons (Reading, Paused, DNF, Completed)
   */
  $(document).on('click', '.book-action', function() {
    const $btn = $(this);
    const $card = $btn.closest('.bookshive-book-card');
    const bookId = $card.data('book-id');
    const status = $btn.data('status');

    const reason =
      status === 'paused' || status === 'did_not_finish'
        ? prompt('Would you like to add a reason? (optional)', '')
        : '';

    const data = {
      action: 'bookshive_update_reading_status',
      nonce: bookshiveAjax.nonce,
      book_id: bookId,
      status: status,
      reason: reason
    };

    $btn.prop('disabled', true);

    $.post(bookshiveAjax.ajax_url, data, function(response) {
      $btn.prop('disabled', false);

      if (response.success) {
        $card.find('.status')
          .attr('class', `status status-${status}`)
          .text(status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));

        if (reason && (status === 'paused' || status === 'did_not_finish')) {
          $card.find('.status').attr('title', 'Reason: ' + reason);
        }

        $card.addClass('updated');
        setTimeout(() => $card.removeClass('updated'), 1500);
      } else {
        alert(response.data || 'Error updating status.');
      }
    });
  });

  /**
   * Optional visual enhancement: hover glow
   */
  $(document).on('mouseenter', '.bookshive-book-card', function() {
    $(this).css('box-shadow', '0 4px 12px rgba(0,0,0,0.15)');
  }).on('mouseleave', '.bookshive-book-card', function() {
    $(this).css('box-shadow', '');
  });

})(jQuery);
