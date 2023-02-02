<div {{ $attributes->class(['fixed top-1 right-1 z-50 sm:top-10 sm:right-10 font-semibold']) }} x-data="{
    init() {
            if (window.localStorage.getItem('theme') === null) {
                window.localStorage.setItem('theme', 'dark');
            }
            if (window.localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark');
            }
        },
        darkMode() {
            return window.localStorage.getItem('theme') === 'dark' ? 'light mode' : 'dark mode';
        },
        toggleDark() {
            if (window.localStorage.getItem('theme') === 'dark') {
                window.localStorage.setItem('theme', 'light');
                document.body.classList.remove('dark');
                this.darkMode = 'dark mode';
            } else {
                window.localStorage.setItem('theme', 'dark');
                document.body.classList.add('dark');
                this.darkMode = 'light mode';
            }
        }
}">
    <x-button primary @click="toggleDark" x-text="darkMode" />
</div>
