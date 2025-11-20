// Restock Modal Functions
        function openRestockModal(name, sku, currentStock) {
            document.getElementById('productName').value = name;
            document.getElementById('productSKU').value = sku;
            document.getElementById('currentStock').value = currentStock;
            document.getElementById('restockModal').style.display = 'flex';
        }

        function closeRestockModal() {
            document.getElementById('restockModal').style.display = 'none';
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('restockModal');
            if (event.target === modal) {
                closeRestockModal();
            }
        }

        // Form submission
        document.getElementById('restockForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Restock order has been placed successfully!');
            closeRestockModal();
        });

        // Close alert banner
        document.querySelector('.alert-banner .close').addEventListener('click', function() {
            document.querySelector('.alert-banner').style.display = 'none';
        });

        // Filter functionality
        document.getElementById('stock-filter').addEventListener('change', function() {
            const status = this.value;
            const rows = document.querySelectorAll('.inventory-table tbody tr');
            
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