/**
 * Handles the front-end display of the user's Bookshive library.
 * Dynamically filters, sorts, and changes layout views via AJAX.
 */

jQuery(document).ready(function ($) {
  const $container = $("#bookshive-library-container");
  const userId = $container.data("user");
  const $content = $("#bookshive-library-content");

  // === Restore preferred layout ===
  let savedView = localStorage.getItem("bookshive_view") || "grid";
  setView(savedView);

  // === Initial load ===
  fetchBooks();

  // === Event Listeners ===
  $("#filter-genre, #filter-author, #filter-rating, #filter-status, #sort-books").on(
    "change",
    fetchBooks
  );

  $(".bookshive-view-toggle .view-btn").on("click", function () {
    const view = $(this).data("view");
    $(".bookshive-view-toggle .view-btn").removeClass("active");
    $(this).addClass("active");
    setView(view);
    localStorage.setItem("bookshive_view", view);
  });

  // === Functions ===
  function fetchBooks() {
    $content.html('<p class="bookshive-loading">📖 Fetching your books...</p>');

    const filters = {
      action: "bookshive_fetch_books",
      nonce: bookshive_ajax.nonce,
      user_id: userId,
      genre: $("#filter-genre").val(),
      author: $("#filter-author").val(),
      rating: $("#filter-rating").val(),
      status: $("#filter-status").val(),
      sort: $("#sort-books").val(),
    };

    $.post(bookshive_ajax.ajax_url, filters, function (response) {
      if (response.success) {
        renderBooks(response.data.books);
      } else {
        $content.html("<p class='bookshive-error'>⚠️ Unable to load books.</p>");
      }
    });
  }

  function renderBooks(books) {
    if (!books.length) {
      $content.html("<p class='bookshive-empty'>No books match your filters.</p>");
      return;
    }

    const currentView = localStorage.getItem("bookshive_view") || "grid";
    $content.removeClass().addClass(`view-${currentView}`);

    let html = "";

    books.forEach((book) => {
      html += `
        <div class="bookshive-book-item" data-status="${book.status}">
          <div class="book-cover">
            ${
              book.thumb
                ? `<img src="${book.thumb}" alt="${book.title} cover">`
                : `<div class="placeholder-cover">📘</div>`
            }
          </div>
          <div class="book-info">
            <h3 class="book-title">${book.title}</h3>
            <p class="book-author">by ${book.author || "Unknown"}</p>
            <p class="book-genre">${book.genre || "—"}</p>
            <p class="book-status">
              <span class="status-tag status-${book.status}">
                ${statusLabel(book.status)}
              </span>
            </p>
            <div class="book-ratings">
              <span class="stars">${"★".repeat(book.rating || 0)}${"☆".repeat(
        5 - (book.rating || 0)
      )}</span>
              <span class="spice">🔥 ${book.spice || 0}</span>
            </div>
          </div>
        </div>`;
    });

    $content.hide().html(html).fadeIn(300);
  }

  function setView(view) {
    $content.removeClass().addClass(`view-${view}`);
    localStorage.setItem("bookshive_view", view);
  }

  function statusLabel(status) {
    switch (status) {
      case "unread":
        return "Unread";
      case "reading":
        return "Reading";
      case "paused":
        return "Paused";
      case "dnf":
        return "Did Not Finish";
      case "finished":
        return "Finished";
      default:
        return "Unknown";
    }
  }
});

