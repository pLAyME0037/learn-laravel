import Alpine from 'alpinejs';

Alpine.data('profileImage', () => ({
    uploading: false,

    handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image must be less than 5MB.');
            return;
        }

        this.uploading = true;

        // Preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            this.$refs.image.src = e.target.result;
            this.uploading = false;
        };
        reader.readAsDataURL(file);

        // You can also automatically submit the form or make an AJAX request here
        // this.uploadImage(file);
    },

    uploadImage(file) {
        // Example AJAX upload
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        // If userId is critical, it should be passed via data attributes on the x-data element
        // and accessed as this.$el.dataset.userId.

        fetch('/profile/update', { // Assuming 'profile.update' route is '/profile/update'
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.uploading = false;
                if (data.success) {
                    // Update image source with new URL
                    this.$refs.image.src = data.avatar_url + '?t=' + new Date().getTime();
                } else {
                    alert('Failed to upload image: ' + data.message);
                }
            })
            .catch(error => {
                this.uploading = false;
                console.error('Error:', error);
                alert('Error uploading image');
            });
    }
}));
