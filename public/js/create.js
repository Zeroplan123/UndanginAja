// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Function to update template card styling
    function updateTemplateSelection() {
        // Remove selected class from all cards
        document.querySelectorAll('.template-card').forEach(function(card) {
            card.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-300');
            card.classList.add('border-gray-200');
        });
        
        // Add selected class to checked card
        const checkedRadio = document.querySelector('input[name="template_id"]:checked');
        if (checkedRadio) {
            const card = checkedRadio.nextElementSibling.querySelector('.template-card');
            if (card) {
                card.classList.remove('border-gray-200');
                card.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-300');
            }
        }
    }
    
    // Add change event listeners to all radio buttons
    document.querySelectorAll('input[name="template_id"]').forEach(function(radio) {
        radio.addEventListener('change', updateTemplateSelection);
    });
    
    // Initialize selected state on page load
    updateTemplateSelection();
});