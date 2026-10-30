document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.querySelector('#sidebarToggle');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', (event) => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    const questionType = document.querySelector('#jenis_soal');
    const choices = document.querySelector('#questionChoices');

    if (questionType && choices) {
        const updateChoices = () => {
            choices.hidden = questionType.value !== 'Pilihan Ganda';
        };
        questionType.addEventListener('change', updateChoices);
        updateChoices();
    }
});
