$(document.body).ready(function () {
  // Routes mapping hash to html template inside views/
  const routes = {
    '#dashboard': 'views/dashboard.html',
    '#recipes': 'views/recipes.html',
    '#meal-plans': 'views/meal-plans.html',
    '#external-api': 'views/external-api.html',
    '#profile': 'views/profile.html',
    '#login': 'views/login.html',
    '#register': 'views/register.html'
  };

  function loadView() {
    let hash = window.location.hash || '#dashboard';
    let viewUrl = routes[hash] || 'views/dashboard.html';

    // Update active state in nav links
    $('.navbar-nav .nav-link').removeClass('active');
    $(`.navbar-nav .nav-link[href="${hash}"]`).addClass('active');

    // Fetch view file content dynamically
    $.get(viewUrl, function (data) {
      $('#app-view').html(data);
    }).fail(function () {
      $('#app-view').html('<div class="alert alert-danger">Error: View could not be loaded.</div>');
    });
  }

  // Handle hash changes and initial page load
  $(window).on('hashchange', loadView);
  loadView();
});