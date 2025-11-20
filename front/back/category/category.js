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
                if (tab.textContent.trim() === 'Categories List' && tabId === 'category-list') {
                    tab.classList.add('active');
                } else if (tab.textContent.trim() === 'Add New Category' && tabId === 'add-category') {
                    tab.classList.add('active');
                } else if (tab.textContent.trim() === 'Edit Category' && tabId === 'edit-category') {
                    tab.classList.add('active');
                }
            });
        }

        // Edit category function
        function editCategory(name) {
            // Show the edit tab (which is hidden by default)
            document.getElementById('edit-tab').style.display = 'flex';
            // Show the edit form
            showTab('edit-category');
            
            // In a real application, we would load the category data from an API
            // For this example, we'll just set the form values based on the name
            document.getElementById('editCategoryName').value = name;
            document.getElementById('editCategorySlug').value = name.toLowerCase().replace(/\s+/g, '-');
            document.getElementById('editCategoryDescription').value = `${name} products and items`;
        }

        // Delete category function
        function deleteCategory(name) {
            if (confirm(`Are you sure you want to delete the "${name}" category?`)) {
                alert(`Category "${name}" has been deleted successfully!`);
                // In a real application, we would make an API call to delete the category
            }
        }

        // Icon selection
        document.querySelectorAll('.icon-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.icon-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update the hidden input value
                const formId = this.closest('.form-container').id === 'add-category' ? 'add' : 'edit';
                document.getElementById(`${formId}CategoryIcon`).value = this.getAttribute('data-icon');
            });
        });

        // Color selection
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.color-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update the hidden input value
                const formId = this.closest('.form-container').id === 'add-category' ? 'add' : 'edit';
                document.getElementById(`${formId}CategoryColor`).value = this.getAttribute('data-color');
            });
        });

        // Form submission handlers
        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Category added successfully!');
            showTab('category-list');
        });

        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Category updated successfully!');
            showTab('category-list');
        });

        // Generate slug from category name
        document.getElementById('categoryName').addEventListener('blur', function() {
            const slug = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            document.getElementById('categorySlug').value = slug;
        });