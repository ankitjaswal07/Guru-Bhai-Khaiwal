/**
 * Guru Bhai Khaiwal – mobile navigation toggle (vanilla JS, no dependencies).
 */
(function () {
  'use strict';

  var toggle = document.querySelector('.s20-menu-toggle');
  var menu = document.getElementById('s20-menu');
  if (!toggle || !menu) { return; }

  function setOpen(open) {
    menu.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.classList.toggle('s20-menu-open', open);
  }

  toggle.addEventListener('click', function () {
    setOpen(!menu.classList.contains('is-open'));
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && menu.classList.contains('is-open')) {
      setOpen(false);
      toggle.focus();
    }
  });

  document.addEventListener('click', function (e) {
    if (!menu.classList.contains('is-open')) { return; }
    if (menu.contains(e.target) || toggle.contains(e.target)) { return; }
    setOpen(false);
  });

  // Close when resizing back to desktop.
  var mq = window.matchMedia('(min-width: 1025px)');
  var onChange = function (ev) { if (ev.matches) { setOpen(false); } };
  if (mq.addEventListener) { mq.addEventListener('change', onChange); } else if (mq.addListener) { mq.addListener(onChange); }
})();
