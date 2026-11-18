// directives/click-outside/index.js
import Vue from 'vue';
import clickOutside from './click-outside.js';

// Register directive globally
Vue.directive('click-outside', clickOutside);

export default clickOutside;
