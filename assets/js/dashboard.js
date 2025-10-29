/**
 * Bookshive Dashboard Script
 * Handles: Reading analytics, charts, achievements display
 * Author: E. Durant
 */

(function ($) {
  'use strict';

  const BookshiveDashboard = {
    init() {
      this.renderCharts();
      this.animateProgressBars();
      this.loadAchievements();
      this.refreshStats();
    },

    /* =====================================================
       CHARTS SECTION
       ===================================================== */
    renderCharts() {
      const genreCanvas = document.getElementById('genreChart');
      const timelineCanvas = document.getElementById('timelineChart');

      if (genreCanvas) {
        const ctx1 = genreCanvas.getContext('2d');
        new Chart(ctx1, {
          type: 'doughnut',
          data: {
            labels: bookshiveDashboardData.genre_labels,
            datasets: [{
              data: bookshiveDashboardData.genre_counts,
              backgroundColor: [
                '#684ac2',
                '#9f7aea',
                '#d6bcfa',
                '#805ad5',
                '#553c9a',
                '#b794f4'
              ],
              borderWidth: 1,
            }],
          },
          options: {
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: '#333',
                  padding: 16,
                },
              },
            },
          },
        });
      }

      if (timelineCanvas) {
        const ctx2 = timelineCanvas.getContext('2d');
        new Chart(ctx2, {
          type: 'line',
          data: {
            labels: bookshiveDashboardData.timeline_labels,
            datasets: [{
              label: 'Books Read',
              data: bookshiveDashboardData.timeline_data,
              borderColor: '#684ac2',
              backgroundColor: 'rgba(104,74,194,0.1)',
              fill: true,
              tension: 0.3,
            }],
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  color: '#3d2d77',
                },
                grid: {
                  color: '#eee',
                },
              },
              x: {
                ticks: { color: '#555' },
                grid: { display: false },
              },
            },
            plugins: {
              legend: { display: false },
            },
          },
        });
      }
    },

    /* =====================================================
       PROGRESS BAR ANIMATION
       ===================================================== */
    animateProgressBars() {
      $('.progress-bar').each(function () {
        const $bar = $(this);
        const target = parseInt($bar.data('value')) || 0;
        $bar.css('width', '0%');
        setTimeout(() => {
          $bar.css('width', target + '%');
        }, 300);
      });
    },

    /* =====================================================
       ACHIEVEMENT DISPLAY
       ===================================================== */
    loadAchievements() {
      if (!bookshiveDashboardData.achievements) return;

      const container = $('.badge-collection');
      if (!container.length) return;

      bookshiveDashboardData.achievements.forEach((ach) => {
        const badge = $(`
          <div class="badge-item" title="${ach.description}">
            <img src="${ach.icon}" alt="${ach.title}">
            <span>${ach.title}</span>
          </div>
        `);
        container.append(badge);
      });
    },

    /* =====================================================
       STATS REFRESH (AJAX)
       ===================================================== */
    refreshStats() {
      if (!$('#stats-today').length) return;

      $.ajax({
        url: bookshiveDashboardData.ajax_url,
        method: 'POST',
        data: { action: 'bookshive_get_dashboard_stats' },
        success: (response) => {
          if (response.success && response.data) {
            $('#stats-today').text(response.data.today_books);
            $('#stats-month').text(response.data.month_books);
            $('#stats-total').text(response.data.total_books);
            $('#goal-progress .progress-bar').css(
              'width',
              response.data.goal_percent + '%'
            );
          }
        },
      });
    },
  };

  $(document).ready(() => BookshiveDashboard.init());
})(jQuery);
