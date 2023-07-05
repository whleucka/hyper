// Keypad functions
function bs(e) {
  var input = document.getElementById('two_fa');
  if (input.value.length > 0) {
    input.value = input.value.slice(0, -1);
  }
}
function key(e) {
  var input = document.getElementById('two_fa');
  if (input.value.length < 6) {
    input.value = input.value + e.target.value;
  }
}

document.addEventListener("DOMContentLoaded", function(event) { 
  // is-invalid form validation errors
  form_elements = this.querySelectorAll('.form-control');
  form_elements.forEach((el) => {
    const name = el.name;
    if (name in form_errors) {
      el.classList.add("is-invalid");
    }
  });
});

