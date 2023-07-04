document.addEventListener("DOMContentLoaded", function(event) { 
  form_elements = this.querySelectorAll('.form-control');
  form_elements.forEach((el) => {
    const name = el.name;
    if (name in form_errors) {
      el.classList.add("is-invalid");
    }
  });
});

