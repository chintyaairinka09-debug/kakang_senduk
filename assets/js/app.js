document.querySelectorAll('[data-confirm]').forEach((el) => {
  el.addEventListener('click', (event) => {
    const message = el.getAttribute('data-confirm') || 'Apakah kamu yakin?';
    if (!window.confirm(message)) event.preventDefault();
  });
});
document.querySelectorAll('[data-preview]').forEach((input) => {
  input.addEventListener('change', () => {
    const target = document.querySelector(input.dataset.preview);
    if (target && input.files && input.files[0]) target.src = URL.createObjectURL(input.files[0]);
  });
});
