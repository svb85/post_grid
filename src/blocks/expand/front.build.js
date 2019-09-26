"use strict";

if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;

    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);

    return null;
  };
}

function ub_getSiblings(element, criteria) {
  var children = Array.prototype.slice.call(element.parentNode.children).filter(function (child) {
    return child !== element;
  });
  return criteria ? children.filter(criteria) : children;
}

Array.prototype.slice.call(document.getElementsByClassName('ub-expand-toggle-button')).forEach(function (instance) {
  instance.addEventListener('click', function () {
    var blockRoot = instance.closest('.ub-expand');
    blockRoot.querySelector('.ub-expand-partial .ub-expand-toggle-button').classList.toggle('ub-hide');
    blockRoot.querySelector('.ub-expand-full').classList.toggle('ub-hide');
  });
});