/**
 * Bookshive Indie Shop JS
 * Handles: Marketplace filtering + Author Dashboard actions
 * Author: E. Durant
 */

(function ($) {
  'use strict';

  const BookshiveShop = {
    init() {
      this.marketplaceFilters();
      this.authorDashboard();
    },

    /* ===============================
       MARKETPLACE
       =============================== */
    marketplaceFilters() {
      const searchInput = $('#shop-search');
      const genreFilter = $('#shop-genre-filter');

      // Trigger filter on typing or genre change
      searchInput.on('keyup', () => this.filterBooks());
      genreFilter.on('change', () => this.filterBooks());
    },

    filterBooks() {
      const query = $('#shop-search').val().toLowerCase();
      const genre = $('#shop-genre-filter').val();

      $('.book-card').each(function () {
        const title = $(this).find('h4').text().toLowerCase();
        const author = $(this).find('.author').text().toLowerCase();
        const bookGenre = $(this).data('genre') || '';

        const match =
          (title.includes(query) || author.includes(query)) &&
          (!genre || genre === bookGenre);

        $(this).toggle(match);
      });
    },

    /* ===============================
       AUTHOR DASHBOARD
       =============================== */
    authorDashboard() {
      const form = $('#add-indie-book-form');

      if (!form.length) return;

      form.on('submit', (e) => {
        e.preventDefault();
        const data = form.serialize() + '&action=bookshive_add_indie_book';

        $.ajax({
          url: bookshiveAjax.ajax_url,
          method: 'POST',
          data,
          beforeSend: () => this.showNotice('Saving your book...', 'info'),
          success: (response) => {
            if (response.success) {
              this.showNotice('✅ Book added successfully!', 'success');
              form[0].reset();
              this.refreshBookTable(response.data.books);
            } else {
              this.showNotice('❌ Failed to add book: ' + response.data, 'error');
            }
          },
          error: () => this.showNotice('⚠️ Network error, please try again.', 'error'),
        });
      });

      // Delete buttons
      $(document).on('click', '.delete-book', (e) => {
        e.preventDefault();
        const id = $(e.currentTarget).data('id');

        if (!confirm('Are you sure you want to delete this book?')) return;

        $.ajax({
          url: bookshiveAjax.ajax_url,
          method: 'POST',
          data: { action: 'bookshive_delete_indie_book', id },
          success: (response) => {
            if (response.success) {
              this.showNotice('🗑️ Book deleted.', 'success');
              $(`button[data-id="${id}"]`).closest('tr').fadeOut(400, function () {
                $(this).remove();
              });
            } else {
              this.showNotice('❌ Could not delete book.', 'error');
            }
          },
        });
      });
    },

    /* ===============================
       HELPERS
       =============================== */
    refreshBookTable(books) {
      const tbody = $('.author-book-list tbody');
      tbody.empty();

      if (!books.length) {
        tbody.append('<tr><td colspan="4">No books listed yet.</td></tr>');
        return;
      }

      books.forEach((book) => {
        const row = `
          <tr>
            <td>${book.title}</td>
            <td>${book.genre}</td>
            <td>£${book.price}</td>
            <td>
              <button class="button-secondary edit-book" data-id="${book.id}">Edit</button>
              <button class="button delete-book" data-id="${book.id}">Delete</button>
            </td>
          </tr>`;
        tbody.append(row);
      });
    },

    showNotice(message, type = 'info') {
      let color = '#684ac2';
      if (type === 'success') color = '#28a745';
      if (type === 'error') color = '#dc3545';
      if (type === 'info') color = '#007bff';

      const notice = $(`
        <div class="bookshive-notice ${type}" 
             style="
                position:fixed;
                top:20px;right:20px;
                background:${color};
                color:#fff;
                padding:12px 18px;
                border-radius:8px;
                box-shadow:0 2px 10px rgba(0,0,0,0.1);
                z-index:9999;">
          ${message}
        </div>
      `)
        .appendTo('body')
        .hide()
        .fadeIn(300);

      setTimeout(() => {
        notice.fadeOut(400, () => notice.remove());
      }, 3000);
    },
  };

  $(document).ready(() => BookshiveShop.init());
})(jQuery);
