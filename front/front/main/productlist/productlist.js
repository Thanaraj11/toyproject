const PRODUCTS = [
      { id: 1, name: 'Robo Racer', category: 'Robots', price: 29.99, img: '' },
      { id: 2, name: 'Puzzle Castle', category: 'Puzzles', price: 15.50, img: '' },
      { id: 3, name: 'Soft Teddy', category: 'Plush', price: 12.00, img: '' },
      { id: 4, name: 'Build-a-Ship', category: 'Construction', price: 34.75, img: '' },
      { id: 5, name: 'Racing Track', category: 'Vehicles', price: 22.00, img: '' },
      { id: 6, name: 'Magic Blocks', category: 'Education', price: 18.90, img: '' },
      { id: 7, name: 'Mini Drone', category: 'Robots', price: 59.00, img: '' },
      { id: 8, name: 'Mega Puzzle 5000', category: 'Puzzles', price: 45.00, img: '' },
      { id: 9, name: 'Fluffy Bunny', category: 'Plush', price: 10.00, img: '' },
      { id: 10, name: 'Lego Tank', category: 'Construction', price: 70.00, img: '' }
    ];

    const productTemplate = document.getElementById('product-template');
    const container = document.getElementById('products-container');
    const categorySelect = document.getElementById('filter-category');
    const minPriceInput = document.getElementById('filter-min-price');
    const maxPriceInput = document.getElementById('filter-max-price');
    const sortOptions = document.getElementById('sort-options');
    const viewOptions = document.getElementById('view-options');
    const pagination = document.getElementById('pagination');

    const PAGE_SIZE = 4;
    let currentPage = 1;
    let filteredProducts = PRODUCTS.slice();

    function initCategories() {
      const categories = Array.from(new Set(PRODUCTS.map(p => p.category)));
      categories.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        categorySelect.appendChild(opt);
      });
    }

    function createProductCard(p) {
      const clone = productTemplate.content.cloneNode(true);
      const img = clone.querySelector('.image');
      img.alt = p.name;
      img.src = p.img || 'data:image/svg+xml;utf8,' + encodeURIComponent(`<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"150\" height=\"150\"><rect width=\"100%\" height=\"100%\" fill=\"#f1f2f6\"/><text x=\"50%\" y=\"50%\" dominant-baseline=\"middle\" text-anchor=\"middle\" font-size=\"12\" font-family=\"sans-serif\" fill=\"%23636e72\">${p.name}</text></svg>`);
      clone.querySelector('.name').textContent = p.name;
      clone.querySelector('.category').textContent = p.category;
      clone.querySelector('.price').textContent = '$' + p.price.toFixed(2);
      
      // Add to cart functionality
      const addButton = clone.querySelector('button');
      addButton.addEventListener('click', () => {
        alert(`${p.name} added to cart!`);
      });
      
      return clone;
    }

    function renderProducts() {
      container.innerHTML = '';
      const start = (currentPage - 1) * PAGE_SIZE;
      const end = start + PAGE_SIZE;
      filteredProducts.slice(start, end).forEach(p => container.appendChild(createProductCard(p)));

      container.className = viewOptions.value === 'grid' ? 'grid-view' : 'list-view';
      renderPagination();
    }

    function renderPagination() {
      pagination.innerHTML = '';
      const totalPages = Math.ceil(filteredProducts.length / PAGE_SIZE);
      for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === currentPage) btn.disabled = true;
        btn.addEventListener('click', () => {
          currentPage = i;
          renderProducts();
        });
        pagination.appendChild(btn);
      }
    }

    function applyFilters() {
      let list = PRODUCTS.slice();
      const cat = categorySelect.value;
      if (cat !== 'all') {
        list = list.filter(p => p.category === cat);
      }
      const minPrice = parseFloat(minPriceInput.value);
      const maxPrice = parseFloat(maxPriceInput.value);
      if (!isNaN(minPrice)) list = list.filter(p => p.price >= minPrice);
      if (!isNaN(maxPrice)) list = list.filter(p => p.price <= maxPrice);

      switch (sortOptions.value) {
        case 'price-asc':
          list.sort((a, b) => a.price - b.price);
          break;
        case 'price-desc':
          list.sort((a, b) => b.price - a.price);
          break;
        case 'name':
        default:
          list.sort((a, b) => a.name.localeCompare(b.name));
      }

      filteredProducts = list;
      currentPage = 1;
      renderProducts();
    }

    document.getElementById('filter-form').addEventListener('submit', ev => {
      ev.preventDefault();
      applyFilters();
    });

    sortOptions.addEventListener('change', applyFilters);
    viewOptions.addEventListener('change', renderProducts);

    initCategories();
    applyFilters();