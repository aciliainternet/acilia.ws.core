function toggleAccordion(event) {
  const accordion = event.currentTarget;
  const icon = accordion.querySelector("i");
  accordion.nextElementSibling.classList.toggle("is-hidden");
  if (icon) {
    icon.classList.toggle("fa-rotate-180");
  }

  return true;
}

function init() {
  const accordions = document.querySelectorAll("[data-accordion]");
  if (accordions) {
    accordions.forEach((el) => {
      el.addEventListener("click", toggleAccordion);
    });
  }
}

export default init;
