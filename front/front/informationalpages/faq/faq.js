    const faqSearch = document.getElementById('faq-search');
    const faqItems = document.querySelectorAll('.faq-item');
    const noResults = document.querySelector('.no-results');
    
    // Toggle FAQ items
    faqItems.forEach(item => {
      const question = item.querySelector('.faq-question');
      question.addEventListener('click', () => {
        item.classList.toggle('active');
      });
    });
    
    // Search functionality
    faqSearch.addEventListener('input', function() {
      const query = this.value.toLowerCase();
      let visibleCount = 0;
      
      faqItems.forEach(item => {
        const question = item.querySelector('.faq-question').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
        
        if (question.includes(query) || answer.includes(query)) {
          item.style.display = '';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });
      
      // Show no results message if no matches
      if (visibleCount === 0 && query !== '') {
        noResults.style.display = 'block';
      } else {
        noResults.style.display = 'none';
      }
    });