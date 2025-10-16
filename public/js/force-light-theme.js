// Force light theme for all users - override any other theme settings
(function() {
    'use strict';

    // Remove dark class immediately
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');

    // Force localStorage values
    localStorage.setItem('theme', 'light');
    localStorage.setItem('oneuiDarkMode', 'off');
    localStorage.removeItem('oneuiColorTheme'); // Remove any custom color theme

    // Override OneUI instance if it exists
    if (window.One && window.One.setDarkMode) {
        window.One.setDarkMode('off');
    }

    // Observe for any dark class additions and remove them immediately
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                }
            }
        });
    });

    // Start observing
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });

    // Force light theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');

        // Override OneUI after it's initialized
        setTimeout(function() {
            if (window.One && window.One.setDarkMode) {
                window.One.setDarkMode('off');
            }
        }, 100);
    });

    console.log('Force Light Theme: Activated - All dark modes disabled');
})();