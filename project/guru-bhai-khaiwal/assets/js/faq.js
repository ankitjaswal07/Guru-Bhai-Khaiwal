/**
 * Guru Bhai Khaiwal – FAQ accordion enhancement (native <details>; optional single-open mode + smooth focus).
 */
(function () {
  'use strict';

  var groups = document.querySelectorAll('[data-s20-faq]');
  if (!groups.length) { return; }

  groups.forEach(function (group) {
    var single = group.getAttribute('data-single-open') === '1';
    var items = group.querySelectorAll('details.s20-faq-item');

    items.forEach(function (item) {
      item.addEventListener('toggle', function () {
        if (!item.open || !single) { return; }
        items.forEach(function (other) { if (other !== item && other.open) { other.removeAttribute('open'); } });
      });
    });

    // Keyboard: arrow navigation between questions.
    group.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') { return; }
      var summaries = Array.prototype.slice.call(group.querySelectorAll('summary'));
      var index = summaries.indexOf(document.activeElement);
      if (index === -1) { return; }
      e.preventDefault();
      var nextIndex = e.key === 'ArrowDown' ? (index + 1) % summaries.length : (index - 1 + summaries.length) % summaries.length;
      summaries[nextIndex].focus();
    });
  });
})();
