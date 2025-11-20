// Sample data (replace with server data in real app)
    // const PRODUCTS = [
    //   { id: 1, name: 'Robo Racer', category: 'Robots', price: 29.99, featured: true, img: '', rating: 4.5 },
    //   { id: 2, name: 'Puzzle Castle', category: 'Puzzles', price: 15.50, featured: true, img: '', rating: 4.2 },
    //   { id: 3, name: 'Soft Teddy Bear', category: 'Plush', price: 12.00, featured: false, img: '', rating: 4.8 },
    //   { id: 4, name: 'Build-a-Ship', category: 'Construction', price: 34.75, featured: true, img: '', rating: 4.3 },
    //   { id: 5, name: 'Racing Track Set', category: 'Vehicles', price: 22.00, featured: false, img: '', rating: 4.1 },
    //   { id: 6, name: 'Magic Blocks', category: 'Education', price: 18.90, featured: false, img: '', rating: 4.6 },
    //   { id: 7, name: 'Doll House', category: 'Dolls', price: 42.50, featured: true, img: '', rating: 4.7 },
    //   { id: 8, name: 'Science Kit', category: 'Education', price: 24.99, featured: false, img: '', rating: 4.4 }
    // ];

    // const BANNERS = [
    //   { id: 'b1', title: 'Summer Sale — Up to 40% off!', subtitle: 'Selected toys for a limited time', img: '', cta: 'Shop Now' },
    //   { id: 'b2', title: 'New Arrivals: Robots', subtitle: 'Latest programmable robots with AI features', img: '', cta: 'Explore' },
    //   { id: 'b3', title: 'Free Shipping over $50', subtitle: 'Hurry — limited time offer', img: '', cta: 'Learn More' }
    // ];

    // // --- UI helpers ---
    // const yearEl = document.getElementById('year');
    // yearEl.textContent = new Date().getFullYear();

    // const categoriesList = document.getElementById('categories-list');
    // const featuredGrid = document.getElementById('featured-grid');
    // const productsGrid = document.getElementById('products-grid');
    // const productTemplate = document.getElementById('product-template');
    // const bannersContainer = document.getElementById('banners-container');
    // const cartCount = document.querySelector('.cart-count');

    // let activeCategory = 'All';
    // let activeBannerIndex = 0;
    // let cartItems = [];

    // function uniqueCategories() {
    //   const cats = new Set(PRODUCTS.map(p => p.category));
    //   return ['All', ...Array.from(cats)];
    // }

    // function renderCategories() {
    //   const cats = uniqueCategories();
    //   categoriesList.innerHTML = '';
    //   cats.forEach(cat => {
    //     const li = document.createElement('li');
    //     const btn = document.createElement('button');
    //     btn.type = 'button';
    //     btn.textContent = cat;
    //     btn.dataset.category = cat;
    //     if (cat === activeCategory) {
    //       btn.classList.add('active');
    //     }
    //     btn.addEventListener('click', () => {
    //       activeCategory = cat;
    //       // Update active state
    //       document.querySelectorAll('#categories-list button').forEach(b => b.classList.remove('active'));
    //       btn.classList.add('active');
    //       applyFilters();
    //     });
    //     li.appendChild(btn);
    //     categoriesList.appendChild(li);
    //   });
    // }

    // function createProductCard(product) {
    //   const clone = productTemplate.content.cloneNode(true);
    //   const article = clone.querySelector('.card');
    //   const img = clone.querySelector('.image');
    //   const name = clone.querySelector('.name');
    //   const category = clone.querySelector('.category');
    //   const price = clone.querySelector('.price');
    //   const btn = clone.querySelector('.add-to-cart');
    //   const badge = clone.querySelector('.badge');
    //   const rating = clone.querySelector('.rating');

    //   // Hide badge if product is not featured
    //   if (!product.featured) {
    //     badge.style.display = 'none';
    //   }

    //   img.alt = product.name;
    //   // Provide a simple placeholder when no image URL provided
    //   img.src = product.img || 'data:image/svg+xml;utf8,' + encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#f0f0f0"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="14" fill="#555">${product.name}</text></svg>`);

    //   name.textContent = product.name;
    //   category.textContent = product.category;
    //   price.textContent = '$' + product.price.toFixed(2);

    //   // Set rating
    //   if (rating) {
    //     rating.innerHTML = '';
    //     const fullStars = Math.floor(product.rating);
    //     const hasHalfStar = product.rating % 1 >= 0.5;
        
    //     for (let i = 0; i < 5; i++) {
    //       const star = document.createElement('i');
    //       if (i < fullStars) {
    //         star.className = 'fas fa-star';
    //       } else if (i === fullStars && hasHalfStar) {
    //         star.className = 'fas fa-star-half-alt';
    //       } else {
    //         star.className = 'far fa-star';
    //       }
    //       rating.appendChild(star);
    //     }
    //   }

    //   btn.addEventListener('click', () => {
    //     addToCart(product);
    //   });

    //   return clone;
    // }

    // function addToCart(product) {
    //   cartItems.push(product);
    //   updateCartCount();
    //   // Show confirmation
    //   const confirmation = document.createElement('div');
    //   confirmation.style.position = 'fixed';
    //   confirmation.style.bottom = '20px';
    //   confirmation.style.right = '20px';
    //   confirmation.style.backgroundColor = 'var(--success)';
    //   confirmation.style.color = 'white';
    //   confirmation.style.padding = '1rem';
    //   confirmation.style.borderRadius = '4px';
    //   confirmation.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    //   confirmation.style.zIndex = '1000';
    //   confirmation.textContent = `${product.name} added to cart!`;
      
    //   document.body.appendChild(confirmation);
      
    //   setTimeout(() => {
    //     confirmation.style.opacity = '0';
    //     confirmation.style.transition = 'opacity 0.5s';
    //     setTimeout(() => document.body.removeChild(confirmation), 500);
    //   }, 2000);
    // }

    // function updateCartCount() {
    //   cartCount.textContent = cartItems.length;
    // }

    // function renderFeatured() {
    //   featuredGrid.innerHTML = '';
    //   const featured = PRODUCTS.filter(p => p.featured);
    //   if (!featured.length) {
    //     featuredGrid.textContent = 'No featured products at the moment.';
    //     return;
    //   }
    //   featured.forEach(p => {
    //     featuredGrid.appendChild(createProductCard(p));
    //   });
    // }

    // function renderAllProducts(list) {
    //   productsGrid.innerHTML = '';
    //   if (!list.length) {
    //     productsGrid.innerHTML = '<p class="no-products">No products found. Try a different search or category.</p>';
    //     return;
    //   }
    //   list.forEach(p => productsGrid.appendChild(createProductCard(p)));
    // }

    // function applyFilters(query = '') {
    //   let list = PRODUCTS.slice();
    //   if (activeCategory && activeCategory !== 'All') {
    //     list = list.filter(p => p.category === activeCategory);
    //   }
    //   if (query) {
    //     const q = query.trim().toLowerCase();
    //     list = list.filter(p => 
    //       p.name.toLowerCase().includes(q) || 
    //       p.category.toLowerCase().includes(q)
    //     );
    //   }
    //   renderAllProducts(list);
    // }

    // // --- Search handling ---
    // const searchForm = document.getElementById('search-form');
    // const searchInput = document.getElementById('search-input');
    // searchForm.addEventListener('submit', (ev) => {
    //   ev.preventDefault();
    //   applyFilters(searchInput.value);
    // });

    // searchInput.addEventListener('input', (ev) => {
    //   // live filter while typing
    //   applyFilters(ev.target.value);
    // });

    // // --- Banners ---
    // function renderBanners() {
    //   bannersContainer.innerHTML = '';
    //   BANNERS.forEach((b, i) => {
    //     const banner = document.createElement('section');
    //     banner.className = 'banner';
    //     banner.dataset.index = i;
    //     banner.setAttribute('aria-hidden', i === activeBannerIndex ? 'false' : 'true');
        
    //     const title = document.createElement('h3');
    //     title.textContent = b.title;
        
    //     const subtitle = document.createElement('p');
    //     subtitle.textContent = b.subtitle;
        
    //     const ctaButton = document.createElement('button');
    //     ctaButton.className = 'banner-btn';
    //     ctaButton.textContent = b.cta;
        
    //     banner.appendChild(title);
    //     banner.appendChild(subtitle);
    //     banner.appendChild(ctaButton);
        
    //     if (!b.img) {
    //       // simple SVG placeholder for banner
    //       const svg = document.createElement('div');
    //       svg.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="350"><rect width="100%" height="100%" fill="rgba(0,0,0,0.2)"/></svg>';
    //       banner.appendChild(svg);
    //     }
        
    //     bannersContainer.appendChild(banner);
    //   });
    //   updateBannerVisibility();
    // }

    // function updateBannerVisibility() {
    //   const banners = bannersContainer.querySelectorAll('.banner');
    //   banners.forEach(b => {
    //     const idx = Number(b.dataset.index);
    //     b.style.display = idx === activeBannerIndex ? 'block' : 'none';
    //     b.setAttribute('aria-hidden', idx === activeBannerIndex ? 'false' : 'true');
    //   });
    // }

    // document.getElementById('banner-prev').addEventListener('click', () => {
    //   activeBannerIndex = (activeBannerIndex - 1 + BANNERS.length) % BANNERS.length;
    //   updateBannerVisibility();
    // });
    
    // document.getElementById('banner-next').addEventListener('click', () => {
    //   activeBannerIndex = (activeBannerIndex + 1) % BANNERS.length;
    //   updateBannerVisibility();
    // });

    // // auto-rotate banners every 6 seconds
    // let bannerInterval = setInterval(() => {
    //   activeBannerIndex = (activeBannerIndex + 1) % BANNERS.length;
    //   updateBannerVisibility();
    // }, 6000);

    // // Pause banner rotation when hovering over banners
    // bannersContainer.addEventListener('mouseenter', () => {
    //   clearInterval(bannerInterval);
    // });
    
    // bannersContainer.addEventListener('mouseleave', () => {
    //   bannerInterval = setInterval(() => {
    //     activeBannerIndex = (activeBannerIndex + 1) % BANNERS.length;
    //     updateBannerVisibility();
    //   }, 6000);
    // });

    // // --- Initialize ---
    // function init() {
    //   renderCategories();
    //   renderFeatured();
    //   applyFilters(); // renders all products
    //   renderBanners();
    //   updateCartCount();
    // }

    // init();


    
    // Simple banner slider functionality
    document.addEventListener('DOMContentLoaded', function() {
      const bannersContainer = document.getElementById('banners-container');
      const banners = document.querySelectorAll('.banner');
      const prevBtn = document.getElementById('banner-prev');
      const nextBtn = document.getElementById('banner-next');
      let currentBanner = 0;
      
      function showBanner(index) {
        banners.forEach((banner, i) => {
          banner.style.display = i === index ? 'block' : 'none';
        });
      }
      
      if (banners.length > 0) {
        showBanner(0);
        
        nextBtn.addEventListener('click', function() {
          currentBanner = (currentBanner + 1) % banners.length;
          showBanner(currentBanner);
        });
        
        prevBtn.addEventListener('click', function() {
          currentBanner = (currentBanner - 1 + banners.length) % banners.length;
          showBanner(currentBanner);
        });
      }
    });
  