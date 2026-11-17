import { createApp } from 'vue';
import { gsap } from 'gsap';
import './unai-ui.js';

const start = () => window.initUnaiUI({ createApp }, gsap);
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', start, { once: true });
} else {
  start();
}
