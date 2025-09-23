let hasAnimated = false; // prevent reversing
let shownames = false;
window.addEventListener("scroll", () => {
    if (hasAnimated) return; // stop running after first animation

    const scrollPos = window.scrollY;
    const triggerPoint = window.innerHeight * 0.1;
    const triggerNames = window.innerHeight * 0.6;

    if (scrollPos >= triggerPoint) {
        const leftCards = document.querySelectorAll(".left-content .off-card");
        const rightCards = document.querySelectorAll(".right-content .off-card");

        function animateCards(cards, direction) {
            cards.forEach((card, index) => {
                const spreadX = index * 220 * direction;
                card.style.transform = `translateX(${spreadX}px)`;
                card.style.opacity = 1;
            });
        }

        animateCards(leftCards, -1);
        animateCards(rightCards, 1);

        hasAnimated = true; // lock it so it doesn't reverse
    }

     if (hasAnimated && !showNames && scrollPos >= triggerNames) {
        document.querySelectorAll(".product-name").forEach(name => {
            name.style.opacity = 1;
        });
        showNames = true;
    }
});
