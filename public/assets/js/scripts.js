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

// Form validation
const handleFormValidation = () => {
  form_elements = document.querySelectorAll('.form-control');
  form_elements.forEach((el) => {
    const name = el.name;
    if (name in form_errors) {
      // Add is-invalid to the form-control
      el.classList.add("is-invalid");
      // Add the validation errors to container
      const error_container = document.getElementById(`${name}-errors`);
      if (error_container) {
        const div = document.createElement('div');
        div.classList.add('invalid-feedback', 'text-start');
        div.style.display = "block";

        form_errors[name].forEach((error) => {
          const li = document.createElement("li");
          li.classList.add('list-group-item', 'text-danger');
          const node = document.createTextNode(error); 
          li.appendChild(node);
          div.appendChild(li);
        });

        error_container.appendChild(div);
      }
    }
  });
}

// Keypad animation
const handleKeypadAnimation = () => {
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
}

document.addEventListener("DOMContentLoaded", (event) => { 
  handleFormValidation();
});

