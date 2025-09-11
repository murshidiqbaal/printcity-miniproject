

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

        // Additional animation for elements on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const frames = document.querySelectorAll('.frame');
            
            // Add random rotation to frames for more natural look
            frames.forEach(frame => {
                const randomRotate = (Math.random() * 6) - 3;
                frame.style.transform = `rotate(${randomRotate}deg)`;
            });
            
            // Add interactive animation to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
 