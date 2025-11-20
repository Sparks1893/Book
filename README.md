bookhive/
│
├─ bookhive.php                      ← NEW unified main plugin file
│
├─ includes/                         ← All your existing PHP logic
│   ├─ helpers.php                   ← Bookshive_Helpers
│   ├─ admin-menu.php                ← plm_add_admin_menu
│   ├─ book-post-type.php            ← plm_register_book_post_type
│   ├─ book-taxonomies.php           ← plm_register_book_taxonomies
│   ├─ book-meta.php                 ← plm_add_book_meta_boxes / plm_save_book_meta
│   ├─ user-actions.php              ← plm_create_user_actions_table + AJAX
│   ├─ user-library-pages.php        ← plm_wishlist + any [plm_*] pages
│   ├─ class-library-display.php     ← Bookshive_Library_Display
│   ├─ class-reading-status.php      ← Bookshive_Reading_Status
│   ├─ class-series-checker.php      ← Bookshive_Series_Checker
│   ├─ class-book-recommedations.php ← Bookshive_Book_Recommendations
│   ├─ class-community-feed.php      ← Bookshive_Community_Feed
│   ├─ class-achievements.php        ← Bookshive_Achievements
│   ├─ class-shop-system.php         ← Bookshive_Shop_System
│   ├─ class-shop-shortcodes.php     ← Bookshive_Shop_Shortcodes
│   └─ (optional) author-dashboard.php / Process_csv.php if you use them
│
├─ templates/
│   ├─ user-library-display.php      ← library page HTML + JS hook
│   ├─ partials/
│   │   └─ library-book-card.php     ← card for each book
│   ├─ shop-product-card.php
│   ├─ shop-product-page.php
│   ├─ author-dashboard.php
│   ├─ modal-book-quickview.php
│   └─ (any other templates you want)
│
├─ assets/
│   ├─ css/
│   │   ├─ bookhive-style.css        ← overall look (you already have it)
│   │   ├─ dashboard.css
│   │   ├─ library-display.css
│   │   ├─ shop.css
│   │   ├─ achievements.css
│   │   ├─ toast.css
│   │   └─ community.css
│   └─ js/
│       ├─ bookhive.js               ← general frontend
│       ├─ library-display.js
│       ├─ dashboard.js
│       ├─ recommendations.js
│       ├─ series-checker.js
│       ├─ shop.js
│       ├─ toast.js
│       ├─ user-actions.js
│       └─ script.js                 ← CustomBookInput / CSV UI etc.
│
└─ languages/
    └─ bookhive-en_GB.po             ← existing translation
