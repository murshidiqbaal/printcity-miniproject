window.addEventListener('scroll', () => {
  const cards = document.querySelectorAll('.stack-card');
  const triggerHeight = window.innerHeight / 1.2;

  cards.forEach((card, i) => {
    const top = card.getBoundingClientRect().top;
    if (top < triggerHeight) {
      card.style.transform = `translateY(-${i * 20}px) rotate(-${i * 5}deg)`;
    } else {
      card.style.transform = `translateY(0px) rotate(0deg)`;
    }
  });
});
