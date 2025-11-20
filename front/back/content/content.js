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
                if (tab.textContent.trim() === 'Page Editor' && tabId === 'page-editor') {
                    tab.classList.add('active');
                } else if (tab.textContent.trim() === 'Banner Management' && tabId === 'banner-management') {
                    tab.classList.add('active');
                }
            });
        }

        // Toggle switch functionality
        document.getElementById('pageStatus').addEventListener('change', function() {
            document.getElementById('pageStatusText').textContent = this.checked ? 'Published' : 'Draft';
        });

        // WYSIWYG Editor functionality
        document.querySelectorAll('.toolbar-btn').forEach(button => {
            button.addEventListener('click', function() {
                const command = this.dataset.command;
                const editor = document.getElementById('pageContent');
                
                if (command === 'createLink' || command === 'insertImage') {
                    let url = prompt('Enter URL:', 'https://');
                    if (url) {
                        document.execCommand(command, false, url);
                    }
                } else {
                    document.execCommand(command, false, null);
                }
                
                editor.focus();
            });
        });

        // Banner modal (simplified)
        function showBannerModal() {
            alert('Banner creation modal would open here. In a real application, this would open a form to create a new banner.');
        }

        // Auto-generate slug from title
        document.getElementById('pageTitle').addEventListener('blur', function() {
            const slug = this.value.toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9-]/g, '');
            document.getElementById('pageSlug').value = slug;
        });

        // Banner toggle switches
        document.querySelectorAll('.banner-card .toggle-switch input').forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const status = this.checked ? 'Active' : 'Inactive';
                const statusElement = this.closest('.banner-card').querySelector('.status');
                
                if (this.checked) {
                    statusElement.textContent = 'Active';
                    statusElement.className = 'status status-active';
                } else {
                    statusElement.textContent = 'Inactive';
                    statusElement.className = 'status status-inactive';
                }
            });
        });

        // Delete banner confirmation
        document.querySelectorAll('.action-delete').forEach(button => {
            button.addEventListener('click', function() {
                const bannerTitle = this.closest('.banner-card').querySelector('.banner-title').textContent;
                if (confirm(`Are you sure you want to delete the "${bannerTitle}" banner?`)) {
                    alert(`Banner "${bannerTitle}" has been deleted successfully!`);
                }
            });
        });