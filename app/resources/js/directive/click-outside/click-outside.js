// src/directive/clickOutside.js
export default {
  bind(el, binding, vnode) {
    el.clickOutsideEvent = function(event) {
      // Check if the click was outside the element and its children
      if (!(el === event.target || el.contains(event.target))) {
        // Call the provided method
        if (typeof binding.value === 'function') {
          binding.value(event);
        } else if (typeof binding.expression === 'string') {
          vnode.context[binding.expression](event);
        }
      }
    };
    // Add event listener
    document.body.addEventListener('click', el.clickOutsideEvent);
  },
  unbind(el) {
    // Remove event listener
    document.body.removeEventListener('click', el.clickOutsideEvent);
    delete el.clickOutsideEvent;
  },
};
