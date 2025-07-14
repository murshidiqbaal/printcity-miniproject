const canvas = new fabric.Canvas('canvas', {
  preserveObjectStacking: true,
});

let imageObj = null;

// Drag and Drop Support
const dropArea = document.getElementById('drop-area');

dropArea.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropArea.classList.add("highlight");
});

dropArea.addEventListener("dragleave", () => {
  dropArea.classList.remove("highlight");
});

dropArea.addEventListener("drop", (e) => {
  e.preventDefault();
  dropArea.classList.remove("highlight");
  const file = e.dataTransfer.files[0];
  loadImage(file);
});

document.getElementById("fileElem").addEventListener("change", function (e) {
  loadImage(e.target.files[0]);
});

function loadImage(file) {
  const reader = new FileReader();
  reader.onload = function (f) {
    fabric.Image.fromURL(f.target.result, function (img) {
      canvas.clear();
      imageObj = img;
      img.scaleToWidth(400);
      img.set({ selectable: true });
      canvas.centerObject(img);
      canvas.add(img);
      canvas.setActiveObject(img);
    });
  };
  reader.readAsDataURL(file);
}
function applyFrame(fileName) {
  const svgPath = `Admin/Templates/imgs/${fileName}`;
  fabric.loadSVGFromURL(svgPath, function (objects, options) {
    const frame = fabric.util.groupSVGElements(objects, options);
    frame.scaleToWidth(500);
    frame.selectable = false;
    frame.evented = false;
    canvas.add(frame);
    canvas.sendToBack(frame);
    canvas.renderAll();
  });
}

// Dynamically load frame buttons
window.addEventListener('DOMContentLoaded', () => {
  fetch('frames.php')
    .then(res => res.json())
    .then(frames => {
      const container = document.getElementById('frame-buttons');
      frames.forEach(frame => {
        const btn = document.createElement('button');
        btn.textContent = frame.replace('.svg', '');
        btn.onclick = () => applyFrame(frame);
        container.appendChild(btn);
      });
    })
    .catch(err => console.error('Error loading frames:', err));
});


