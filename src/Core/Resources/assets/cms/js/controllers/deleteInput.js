function deleteInput(event) {
  const btn = event.currentTarget;
  const parent = btn.parentElement;
  const input = parent.querySelector("input");
  input.value = "";
  parent.classList.remove("has-content");
}

function inputChange(event) {
  const input = event.currentTarget;
  const parent = input.parentElement;
  if (parent.classList.contains("has-content")) {
    if (input.value.length < 1) {
      parent.classList.remove("has-content");
    }
  } else {
    parent.classList.add("has-content");
  }
}

function init() {
  const btns = document.querySelectorAll("[data-remove-input]");
  if (btns) {
    btns.forEach((el) => {
      el.parentElement
        .querySelector("input")
        .addEventListener("input", inputChange);
      el.addEventListener("click", deleteInput);
    });
  }
}

export default init;
