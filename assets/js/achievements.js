/**
 * Bookshive Achievement Notifications
 * Author: E. Durant
 * Description: Small celebratory animation for newly unlocked badges.
 */

(function ($) {
  'use strict';

  const Achievements = {
    init() {
      $(document).on('bookshive_badge_unlocked', this.showPopup);
    },

    showPopup(e, data) {
      const { title, icon } = data;
      const popup = $(`
        <div class="bookshive-badge-popup">
          <span class="icon">${icon}</span>
          <strong>${title}</strong>
          <p>Achievement Unlocked!</p>
        </div>
      `).hide().appendTo('body').fadeIn(200);

      setTimeout(() => {
        popup.fadeOut(500, () => popup.remove());
      }, 3000);
    },
  };

  $(document).ready(() => Achievements.init());
})(jQuery);
