const colors = ["#FF6B6B", "#6BCB77", "#4D96FF", "#FFD93D", "#C780FA"];

window.onload = function () {
  rotateCards();
  applyColors();
};

function rotateCards() {
  let cards = document.querySelectorAll(".card");
  let angle = 0;
  cards.forEach((card, index) => {
    if(card.classList.contains("away")) {
         card.style.transform = "translateY(-120px) rotate(-48deg)";

    }else{
    card.style.transform = `rotate(${angle}deg)`;
    angle -= 10;
    card.style.zIndex = cards.length - index; // Ensure the last card is on top
    } 
});
}

function applyColors() {
  let cards = document.querySelectorAll(".card");
  cards.forEach((card, index) => {
    card.style.background = colors[index % colors.length];
  });
}

let stackArea = document.querySelector(".stack-area");
window.addEventListener("scroll", () => {
let distance=window.innerHeight/2;
let topVal = stackArea.getBoundingClientRect().top;
let index = -1*(topVal / distance + 1);
index=Math.floor(index);
let cards = document.querySelectorAll(".card");

for (i = 0; i <cards.length; i++) {
if(i <= index){
    cards[i].classList.add("away");
}else{
    cards[i].classList.remove("away");
 }}
 rotateCards();
});