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
                if (tab.textContent.trim() === 'Customer List' && tabId === 'customer-list') {
                    tab.classList.add('active');
                } else if (tab.textContent.trim() === 'Customer Details' && tabId === 'customer-details') {
                    tab.classList.add('active');
                }
            });
        }

        // Filter functionality
        document.getElementById('status-filter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.customer-table tbody tr');
            
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

        // Delete customer confirmation
        document.querySelectorAll('.action-delete').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this customer?')) {
                    alert('Customer deleted successfully!');
                }
            });
        });