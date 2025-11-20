document.addEventListener('DOMContentLoaded', function() {
            // View options functionality
            const viewOptions = document.querySelectorAll('.view-option');
            const resultsGrid = document.querySelector('.results-grid');
            
            viewOptions.forEach(option => {
                option.addEventListener('click', function() {
                    viewOptions.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    if (this.querySelector('.fa-list')) {
                        resultsGrid.style.gridTemplateColumns = '1fr';
                    } else {
                        resultsGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                    }
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            const searchButton = document.querySelector('.search-button');
            const resultCards = document.querySelectorAll('.result-card');
            const resultsCount = document.querySelector('.results-count');
            
            searchButton.addEventListener('click', performSearch);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
            
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleCount = 0;
                
                if (searchTerm.trim() === '') {
                    // If search is empty, show all results
                    resultCards.forEach(card => {
                        card.style.display = 'block';
                        visibleCount++;
                    });
                } else {
                    // Filter results based on search term
                    resultCards.forEach(card => {
                        const title = card.querySelector('.card-title').textContent.toLowerCase();
                        const description = card.querySelector('.card-description').textContent.toLowerCase();
                        
                        if (title.includes(searchTerm) || description.includes(searchTerm)) {
                            card.style.display = 'block';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
                
                // Update results count
                resultsCount.textContent = `About ${visibleCount.toLocaleString()} results (0.${Math.floor(Math.random() * 50) + 10} seconds)`;
            }
            
            // Pagination functionality
            const paginationButtons = document.querySelectorAll('.pagination-button');
            
            paginationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.querySelector('.fa-chevron-left')) {
                        // Previous page
                        const activePage = document.querySelector('.pagination-button.active');
                        if (activePage.textContent !== '1') {
                            paginationButtons.forEach(btn => btn.classList.remove('active'));
                            const prevPage = parseInt(activePage.textContent) - 1;
                            paginationButtons[prevPage].classList.add('active');
                        }
                    } else if (this.querySelector('.fa-chevron-right')) {
                        // Next page
                        const activePage = document.querySelector('.pagination-button.active');
                        if (activePage.textContent !== '5') {
                            paginationButtons.forEach(btn => btn.classList.remove('active'));
                            const nextPage = parseInt(activePage.textContent) + 1;
                            paginationButtons[nextPage].classList.add('active');
                        }
                    } else {
                        // Numbered page
                        paginationButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });
        });