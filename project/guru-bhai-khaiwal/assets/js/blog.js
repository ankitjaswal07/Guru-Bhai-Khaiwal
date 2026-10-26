/**
 * Guru Bhai Khaiwal – lightweight scroll-snap carousel for the "Latest updates" cards.
 */
(function () {
  'use strict';

  var carousels = document.querySelectorAll('[data-s20-carousel="1"]');
  if (!carousels.length) { return; }

  carousels.forEach(function (root) {
    var track = root.querySelector('.s20-blog-track');
    var prev = root.querySelector('.s20-carousel-prev');
    var next = root.querySelector('.s20-carousel-next');
    var dots = root.querySelector('.s20-carousel-dots');
    var slides = track ? track.querySelectorAll('.s20-blog-slide') : [];
    if (!track || !slides.length) { return; }

    function slideWidth() {
      var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '20') || 20;
      return slides[0].getBoundingClientRect().width + gap;
    }
    function perView() { return Math.max(1, Math.round(track.clientWidth / slideWidth())); }
    function pages() { return Math.max(1, Math.ceil(slides.length / perView())); }
    function currentPage() { return Math.round(track.scrollLeft / (slideWidth() * perView())); }

    function buildDots() {
      if (!dots) { return; }
      dots.innerHTML = '';
      var n = pages();
      for (var i = 0; i < n; i++) {
        var b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('role', 'tab');
        b.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        b.dataset.page = String(i);
        b.addEventListener('click', function () { goTo(parseInt(this.dataset.page, 10)); });
        dots.appendChild(b);
      }
      update();
    }

    function goTo(page) {
      var n = pages();
      page = Math.max(0, Math.min(n - 1, page));
      track.scrollTo({ left: page * slideWidth() * perView(), behavior: 'smooth' });
    }

    function update() {
      var page = currentPage();
      var n = pages();
      if (prev) { prev.disabled = page <= 0; }
      if (next) { next.disabled = page >= n - 1; }
      if (dots) {
        var buttons = dots.querySelectorAll('button');
        buttons.forEach(function (b, i) { b.setAttribute('aria-selected', i === page ? 'true' : 'false'); });
      }
    }

    if (prev) { prev.addEventListener('click', function () { goTo(currentPage() - 1); }); }
    if (next) { next.addEventListener('click', function () { goTo(currentPage() + 1); }); }

    var raf;
    track.addEventListener('scroll', function () { cancelAnimationFrame(raf); raf = requestAnimationFrame(update); }, { passive: true });
    window.addEventListener('resize', function () { cancelAnimationFrame(raf); raf = requestAnimationFrame(buildDots); });

    buildDots();
  });
})();
