/**
 * Bookshive Series Checker JS
 * Author: E. Durant
 * Description: Handles user interactions for the series completion checker.
 */

(function ($) {
  'use strict';

  const $checker = $('#bookshive-series-checker');
  const $button = $('#check-series-btn');
  const $results = $('#series-results');
  const $loading = $results.find('.loading');

  // Only initialize if element is present
  if (!$checker.length) return;

  $button.on('click', function () {
    $button.prop('disabled', true).text('Checking...');
    $loading.removeClass('hidden').text('🔍 Scanning your library...');

    const data = {
      action: 'bookshive_check_series',
      nonce: bookshiveAjax.nonce
    };

    $.post(bookshiveAjax.ajax_url, data, function (response) {
      $button.prop('disabled', false).text('Check My Series');
      $loading.addClass('hidden');

      if (response.success && response.data.html) {
        $results.hide().html(response.data.html).fadeIn(400);
      } else {
        $results.hide().html(
          `<p class="bookshive-empty">📚 ${response.data || 'All your series look complete!'}</p>`
        ).fadeIn(400);
      }
    });
  });

  /**
   * Add simple animations to store links
   */
  $(document).on('mouseenter', '.store-link', function () {
    $(this).css({
      transform: 'scale(1.05)',
      background: '#c79c60',
      color: '#fff'
    });
  }).on('mouseleave', '.store-link', function () {
    $(this).css({
      transform: 'scale(1)',
      background: '',
      color: ''
    });
  });

})(jQuery);
