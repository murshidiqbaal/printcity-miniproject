

anime({
  targets: 'h1 span',  // Animate each letter in <span>
  translateY: [-20, 0],
  opacity: [0, 1],
  delay: anime.stagger(100), // delay by 100ms for each span
  duration: 1000,
  easing: 'easeOutExpo',
  loop: true,
  direction: 'alternate'
});

 let cards = document.querySelectorAll(".card");

      let stackArea = document.querySelector(".stack-area");

      function rotateCards() {
        let angle = 0;
        cards.forEach((card, index) => {
          if (card.classList.contains("away")) {
            card.style.transform = `translateY(-120vh) rotate(-48deg)`;
          } else {
            card.style.transform = ` rotate(${angle}deg)`;
            angle = angle - 10;
            card.style.zIndex = cards.length - index;
          }
        });
      }

      rotateCards();

      window.addEventListener("scroll", () => {
        let distance = window.innerHeight * 0.5;

        let topVal = stackArea.getBoundingClientRect().top;

        let index = -1 * (topVal / distance + 1);

        index = Math.floor(index);

        for (i = 0; i < cards.length; i++) {
          if (i <= index) {
            cards[i].classList.add("away");
          } else {
            cards[i].classList.remove("away");
          }
        }
        rotateCards();
      });

      document.addEventListener("DOMContentLoaded", () => {
  const typeText = document.getElementById("type-text");
  const message = "Initializing PrintCity...";
  let index = 0;

  const typing = setInterval(() => {
    if (index < message.length) {
      typeText.textContent += message[index];
      index++;
    } else {
      clearInterval(typing);
    }
  }, 100);
});
