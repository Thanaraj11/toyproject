// Tab navigation
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Show selected tab
            document.getElementById(tabId).style.display = 'block';
            
            // Update tab active state
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Find and activate the corresponding tab button
            document.querySelectorAll('.tab').forEach(tab => {
                if (tab.textContent.trim() === 'Discount Codes' && tabId === 'discount-codes') {
                    tab.classList.add('active');
                } else if (tab.textContent.trim() === 'Special Offers' && tabId === 'special-offers') {
                    tab.classList.add('active');
                } else if (tab.textContent.trim() === 'Create Promotion' && tabId === 'create-promotion') {
                    tab.classList.add('active');
                }
            });
        }

        // Toggle switch functionality
        document.getElementById('promotionStatus').addEventListener('change', function() {
            document.getElementById('statusText').textContent = this.checked ? 'Active' : 'Inactive';
        });

        // Copy code functionality
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert(`Code "${code}" copied to clipboard!`);
            });
        }

        // Delete code functionality
        function deleteCode(code) {
            if (confirm(`Are you sure you want to delete the discount code "${code}"?`)) {
                alert(`Discount code "${code}" has been deleted successfully!`);
            }
        }

        // Edit discount code
        function editDiscountCode(code) {
            alert(`Edit functionality for code "${code}" would open here.`);
            // In a real application, this would open an edit form with pre-filled data
        }

        // Form submission
        document.getElementById('createPromotionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Promotion created successfully!');
            showTab('discount-codes');
        });

        // Filter functionality
        document.getElementById('status-filter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.promotions-table tbody tr');
            
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = 'table-row';
                } else {
                    const rowStatus = row.querySelector('.status').classList[1];
                    if (rowStatus.includes(status)) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });

        // Pagination
        document.querySelectorAll('.page-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.page-item').forEach(i => {
                    i.classList.remove('active');
                });
                if (!this.querySelector('i')) {
                    this.classList.add('active');
                }
            });
        });

        // Auto-generate promo code if empty
        document.getElementById('promoCode').addEventListener('blur', function() {
            if (!this.value) {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let code = '';
                for (let i = 0; i < 8; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.value = code;
            }
        });