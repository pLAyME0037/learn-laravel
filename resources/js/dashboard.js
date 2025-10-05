// Sidebar functionality
window.sidebarToggle = function () {
   fetch('/toggle-sidebar', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({})
   })
      .catch(error => console.error('Error:', error));
}

// Theme functionality
window.themeController = {
   setTheme: function (mode) {
      // Apply theme immediately for better UX
      this.applyTheme(mode);

      // Save to backend
      fetch('/theme/set', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
         },
         body: JSON.stringify({ theme: mode })
      })
         .catch(error => console.error('Error saving theme:', error));
   },

   applyTheme: function (mode) {
      if (mode === 'dark') {
         document.documentElement.classList.add('dark');
         localStorage.theme = 'dark';
      } else if (mode === 'light') {
         document.documentElement.classList.remove('dark');
         localStorage.theme = 'light';
      } else {
         localStorage.removeItem('theme');
         if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
         } else {
            document.documentElement.classList.remove('dark');
         }
      }
   }
};

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function () {
   const savedTheme = localStorage.theme;
   if (savedTheme) {
      window.themeController.applyTheme(savedTheme);
   }
});
