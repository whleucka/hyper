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
          if (error) {
            const li = document.createElement("li");
            li.classList.add('list-group-item', 'text-danger');
            const node = document.createTextNode(error); 
            li.appendChild(node);
            div.appendChild(li);
          }
        });

        error_container.appendChild(div);
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", (event) => { 
  handleFormValidation();
});

