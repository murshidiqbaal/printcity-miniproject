document.addEventListener("DOMContentLoaded", () => {
  const templateSelector = document.getElementById("template-selector");
  const textInput = document.getElementById("custom-text");
  const imageUpload = document.getElementById("image-upload");
  const textPreview = document.getElementById("text-preview");
  const templatePreview = document.getElementById("template-preview");
  const uploadedImage = document.getElementById("uploaded-image");

  templateSelector.addEventListener("change", () => {
    const selected = templateSelector.value;
    templatePreview.src = `assets/${selected}.jpg`;
  });

  textInput.addEventListener("input", () => {
    textPreview.textContent = textInput.value;
  });

  imageUpload.addEventListener("change", (e) => {
    const file = e.target.files[0];
    const reader = new FileReader();
    reader.onload = () => {
      uploadedImage.src = reader.result;
      uploadedImage.style.display = 'block';
    };
    reader.readAsDataURL(file);
  });
});
