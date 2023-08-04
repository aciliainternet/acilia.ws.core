function dropDrag(event) {
  const dropContainer = event;
  const fileInput = dropContainer.querySelector("[data-drop-drag-image]");

  dropContainer.addEventListener("dragover", (e) => {
    // prevent default to allow drop
    e.preventDefault();
  }, false)

  dropContainer.addEventListener("dragenter", () => {
    dropContainer.classList.add("drag-active");
  })

  dropContainer.addEventListener("dragleave", () => {
    dropContainer.classList.remove("drag-active");
  })

  dropContainer.addEventListener("drop", (e) => {
    e.preventDefault();
    dropContainer.classList.remove("drag-active");
    fileInput.files = e.dataTransfer.files;
  })
}

function init() {
  const dropDragContainer = document.querySelectorAll('[data-drop-drag]');
  if (dropDragContainer) {
    dropDragContainer.forEach((el) => {
      dropDrag(el);
    });
  }
}

export default init;
