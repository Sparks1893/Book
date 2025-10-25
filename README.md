# Book

/bookhive/
│
├── bookhive.php                          ← Main plugin loader (core)
│
├── /includes/
│   ├── bookhive-dashboard.php            ← Central user dashboard
│   ├── bookhive-recommendations.php      ← Rule-based recommendations
│   ├── bookhive-wishlist.php             ← Wishlist + Notify Me
│   ├── bookhive-series-gap.php           ← Series completion & store links
│   ├── bookhive-community.php            ← Shared libraries & filters
│   ├── bookhive-display.php              ← Grid/List/CoverWall toggle
│   └── bookhive-achievements.php         ← Achievements system
│
├── /templates/
│   ├── dashboard-template.php
│   ├── library-grid.php
│   ├── library-list.php
│   ├── community-feed.php
│   ├── recommendations.php
│   ├── wishlist.php
│   └── release-calendar.php
│
├── /assets/
│   ├── /css/
│   │   ├── bookhive-core.css
│   │   ├── bookhive-dashboard.css
│   │   ├── bookhive-wishlist.css
│   │   ├── bookhive-community.css
│   │   └── bookhive-calendar.css
│   └── /js/
│       ├── bookhive-core.js
│       ├── bookhive-dashboard.js
│       ├── bookhive-wishlist.js
│       ├── bookhive-community.js
│       ├── bookhive-series-gap.js
│       └── bookhive-calendar.js
│
└── /languages/
    └── bookhive-en_US.po
