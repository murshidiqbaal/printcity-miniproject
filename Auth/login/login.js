import anime from 'animejs/lib/anime.es.js';

anime({
  targets: 'h1 span',
  translateY: ['-2rem', '0rem'],
  delay: anime.stagger(100),
  duration: 600,
  loop: true,
  direction: 'alternate',
  easing: 'easeInOutSine'
});
