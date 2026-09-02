// ========================================
// UVIA Coffee - Admin JavaScript
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.admin-alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

// ========================================
// File input preview (optional enhancement)
// ========================================

document.addEventListener('change', function(e) {
    const input = e.target;
    if (input.type === 'file' && input.accept.includes('image')) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                // Find preview container
                const container = input.closest('.form-group');
                if (container) {
                    // Remove old preview
                    const oldPreview = container.querySelector('.image-preview');
                    if (oldPreview) oldPreview.remove();
                    
                    // Add new preview
                    const preview = document.createElement('div');
                    preview.className = 'image-preview';
                    preview.style.cssText = `
                        margin-top: 8px;
                        width: 80px;
                        height: 80px;
                        border-radius: 8px;
                        overflow: hidden;
                    `;
                    preview.innerHTML = `<img src="${event.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
                    container.appendChild(preview);
                }
            };
            reader.readAsDataURL(file);
        }
    }
});

console.log('☕ UVIA Coffee Admin Panel loaded');