// On document ready...
document.addEventListener("DOMContentLoaded", () => {
});

// Functions
const keypadClick = (e) => {
  
  const code_input = document.getElementById("code");
  const submit = document.getElementById("two-factor-submit");
  const character = e.currentTarget.value;
  const value = code_input.value;
  if (value.length < 6) {
    if (isNaN(character)) {
      if (character === 'bs') {
        code_input.value = value.slice(0, -1);
      } else if (character === 'ent') {
        submit.click();
      }
    } else {
      code_input.value = value + character; 
    }
  }
}
