  // Add visual feedback for template selection
        document.querySelectorAll('input[name="template_id"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Remove selected class from all cards
                document.querySelectorAll('.template-card').forEach(function(card) {
                    card.classList.remove('border-blue-500', 'bg-blue-50');
                    card.classList.add('border-gray-200');
                });
                
                // Add selected class to chosen card
                if (this.checked) {
                    const card = this.nextElementSibling.querySelector('.template-card');
                    card.classList.remove('border-gray-200');
                    card.classList.add('border-blue-500', 'bg-blue-50');
                }
            });
        });

        // Initialize selected state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkedRadio = document.querySelector('input[name="template_id"]:checked');
            if (checkedRadio) {
                const card = checkedRadio.nextElementSibling.querySelector('.template-card');
                card.classList.remove('border-gray-200');
                card.classList.add('border-blue-500', 'bg-blue-50');
            }
        });