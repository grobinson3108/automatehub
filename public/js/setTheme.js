// Set theme preference - Always use light theme
(function() {
    // Force light theme for all users
    localStorage.setItem('theme', 'light');
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
})();
