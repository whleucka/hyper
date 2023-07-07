// Keypad backspace
const bs = (e) => {
  var input = document.getElementById('two_fa');
  if (input.value.length > 0) {
    input.value = input.value.slice(0, -1);
  }
};
// Keypad key
const key = (e) => {
  var input = document.getElementById('two_fa');
  if (input.value.length < 6) {
    input.value = input.value + e.target.value;
  }
};

// Keypad animation
const keypad_key = document.querySelectorAll(".keypad > button");
keypad_key.forEach(key => {
  key.addEventListener("click", (e) => {
    const target = e.currentTarget;
    const classlist = target.classList;
    classlist.add("active");
    setTimeout(() => {
      classlist.remove("active");
    }, 100);
  })
});
