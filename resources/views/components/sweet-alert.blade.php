<!-- 1. Load the Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 2. Logic -->
<script>
    document.addEventListener('livewire:initialized', () => {
        
        // Helper: Check if Dark Mode is active
        const isDarkMode = () => {
            return document.documentElement.classList.contains('dark') || 
                   (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        };

        // Helper: Get Base Config with Dynamic Colors
        const getSwalConfig = () => {
            const dark = isDarkMode();
            return {
                background: dark ? '#1f2937' : '#ffffff', // gray-800 vs white
                color: dark ? '#f3f4f6' : '#1f2937',      // gray-100 vs gray-800
                confirmButtonColor: '#4f46e5',            // indigo-600
                cancelButtonColor: dark ? '#4b5563' : '#9ca3af', // gray-600 vs gray-400
            };
        };

        Livewire.on('swal:success', (data) => {
            Swal.fire({
                ...getSwalConfig(),
                title: 'Success!',
                text: data[0].message,
                icon: 'success',
            });
        });

        Livewire.on('swal:error', (data) => {
            Swal.fire({
                ...getSwalConfig(),
                title: 'Error!',
                text: data[0].message,
                icon: 'error',
                confirmButtonColor: '#ef4444' // Red override
            });
        });

        Livewire.on('swal:confirm', (data) => {
            Swal.fire({
                ...getSwalConfig(),
                title: data[0].title,
                text: data[0].text,
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#ef4444', // Red for cancel/danger
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch(data[0].method); 
                }
            });
        });
    });
</script>