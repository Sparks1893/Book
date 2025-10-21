/**
 * Bookhive Frontend Script
 * Handles display toggles, filters, animations, and recommendations
 */

document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;

  // ===== View Toggle =====
  const gridBtn = document.querySelector(".view-toggle .grid-view");
  const listBtn = document.querySelector(".view-toggle .list-view");
  const libraryGrid = document.querySelector(".library-grid");
  const libraryList = document.querySelector(".library-list");

  if (gridBtn && listBtn) {
    gridBtn.addEventListener("click", () => {
      gridBtn.classList.add("active");
      listBtn.classList.remove("active");
      libraryGrid.style.display = "grid";
      libraryList.style.display = "none";
    });

    listBtn.addEventListener("click", () => {
      listBtn.classList.add("active");
      gridBtn.classList.remove("active");
      libraryGrid.style.display = "none";
      libraryList.style.display = "flex";
    });
  }

  // ===== Filtering =====
  const filterInputs = document.querySelectorAll(
    ".library-filters select, .library-filters input"
  );

  if (filterInputs.length) {
    filterInputs.forEach((input) => {
      input.addEventListener("input", () => applyFilters());
    });
  }

  function applyFilters() {
    const search = document
      .querySelector("#search-input")
      ?.value.toLowerCase()
      .trim();
    const genre = document.querySelector("#filter-genre")?.value;
    const rating = document.querySelector("#filter-rating")?.value;
    const spice = document.querySelector("#filter-spice")?.value;
    const author = document.querySelector("#filter-author")?.value;

    const books = document.querySelectorAll(".book-card");

    books.forEach((book) => {
      const title = book
        .querySelector(".book-card-title")
        ?.textContent.toLowerCase();
      const bookAuthor = book
        .querySelector(".book-card-author")
        ?.textContent.toLowerCase();
      const bookGenre = book.getAttribute("data-genre");
      const bookRating = parseFloat(book.getAttribute("data-rating") || 0);
      const bookSpice = parseFloat(book.getAttribute("data-spice") || 0);

      let visible = true;

      if (search && !title.includes(search) && !bookAuthor.includes(search)) {
        visible = false;
      }
      if (genre && genre !== "all" && bookGenre !== genre) {
        visible = false;
      }
      if (rating && bookRating < parseFloat(rating)) {
        visible = false;
      }
      if (spice && bookSpice < parseFloat(spice)) {
        visible = false;
      }
      if (author && !bookAuthor.includes(author.toLowerCase())) {
        visible = false;
      }

      book.style.display = visible ? "" : "none";
      book.style.opacity = visible ? "1" : "0";
      book.style.transition = "opacity 0.3s ease";
    });
  }

  // ===== Recommendation Animations =====
  const recommendationCards = document.querySelectorAll(
    ".recommendation-card"
  );
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    },
    { threshold: 0.15 }
  );

  recommendationCards.forEach((card) => observer.observe(card));

  // ===== Series Tracker =====
  const seriesBooks = document.querySelectorAll(".series-book");
  seriesBooks.forEach((book) => {
    if (book.classList.contains("series-book-missing")) {
      const alert = document.createElement("div");
      alert.textContent = "Missing from your collection!";
      alert.style.color = "var(--gold)";
      alert.style.fontSize = "0.8rem";
      alert.style.marginTop = "0.4rem";
      book.appendChild(alert);

      book.addEventListener("click", () => {
        const links = book.parentElement.querySelector(".series-purchase-links");
        if (links) {
          links.scrollIntoView({ behavior: "smooth" });
          links.style.boxShadow = "0 0 15px var(--glow)";
          setTimeout(() => (links.style.boxShadow = "none"), 2000);
        }
      });
    }
  });

  // ===== Smooth Page Animations =====
  const fadeInElements = document.querySelectorAll(
    ".book-card, .series-book, .recommendation-card"
  );
  fadeInElements.forEach((el) => {
    el.style.opacity = 0;
    el.style.transform = "translateY(15px)";
    setTimeout(() => {
      el.style.transition = "all 0.6s ease";
      el.style.opacity = 1;
      el.style.transform = "translateY(0)";
    }, Math.random() * 500);
  });
});
