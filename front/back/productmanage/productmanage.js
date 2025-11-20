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
                if (tab.textContent.trim() === document.getElementById(tabId).querySelector('.form-title').textContent) {
                    tab.classList.add('active');
                }
            });
            
            // Special case for product list tab
            if (tabId === 'product-list') {
                document.querySelectorAll('.tab')[1].classList.add('active');
            }
        }

        // Form submission handlers
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Product added successfully!');
            showTab('product-list');
        });

        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Product updated successfully!');
            showTab('product-list');
        });

        // Delete product handler
        document.querySelectorAll('.action-delete').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this product?')) {
                    alert('Product deleted successfully!');
                }
            });
        });