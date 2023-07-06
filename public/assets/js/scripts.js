// Keypad functions
const bs = (e) => {
  var input = document.getElementById('two_fa');
  if (input.value.length > 0) {
    input.value = input.value.slice(0, -1);
  }
};
const key = (e) => {
  var input = document.getElementById('two_fa');
  if (input.value.length < 6) {
    input.value = input.value + e.target.value;
  }
};

document.addEventListener("DOMContentLoaded", (event) => { 
  // is-invalid form validation errors
  form_elements = document.querySelectorAll('.form-control');
  form_elements.forEach((el) => {
    const name = el.name;
    if (name in form_errors) {
      el.classList.add("is-invalid");
    }
  });

  // Keypad animation
  const keypad_key = document.querySelectorAll(".keypad > button");
  keypad_key.forEach(key => {
    key.addEventListener("click", (e) => {
      const target = e.currentTarget;
      const classlist = target.classList;
      return new Promise((reject, resolve) => {
        classlist.add("active");
        setTimeout(() => {
          classlist.remove("active");
          resolve();
        }, 100)
      });
    })
  })
});

