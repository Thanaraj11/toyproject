// Sample wishlist data with more details
    let wishlist = [
      { id: 1, name: 'Robo Racer', price: 29.99, category: 'Toys', image: '🚗' },
      { id: 2, name: 'Soft Teddy Bear', price: 12.00, category: 'Toys', image: '🧸' },
      { id: 3, name: 'Wireless Headphones', price: 89.99, category: 'Electronics', image: '🎧' },
      { id: 4, name: 'Stainless Steel Water Bottle', price: 24.95, category: 'Home', image: '💧' }
    ];

    const wishlistBody = document.getElementById('wishlist-body');
    const emptyWishlistMessage = document.getElementById('empty-wishlist-message');
    const notification = document.getElementById('notification');

    function showNotification(message, isSuccess = true) {
      notification.textContent = message;
      notification.style.background = isSuccess ? '#2ecc71' : '#e74c3c';
      notification.style.display = 'block';
      
      setTimeout(() => {
        notification.style.display = 'none';
      }, 3000);
    }

    function renderWishlist() {
      wishlistBody.innerHTML = '';
      
      if (wishlist.length === 0) {
        emptyWishlistMessage.style.display = 'block';
        document.querySelector('table').style.display = 'none';
        return;
      }
      
      emptyWishlistMessage.style.display = 'none';
      document.querySelector('table').style.display = 'table';
      
      wishlist.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>
            <div class="product-cell">
              <div class="product-image">${item.image}</div>
              <div>
                <div class="product-name">${item.name}</div>
                <div class="product-category">${item.category}</div>
              </div>
            </div>
          </td>
          <td class="price">$${item.price.toFixed(2)}</td>
          <td>
            <div class="actions">
              <button class="move-cart" data-index="${index}">
                <i class="fas fa-cart-plus"></i> Move to Cart
              </button>
              <button class="remove-item" data-index="${index}">
                <i class="fas fa-trash"></i> Remove
              </button>
            </div>
          </td>
        `;
        wishlistBody.appendChild(tr);
      });

      // Add event listeners to the new buttons
      document.querySelectorAll('.move-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const index = e.target.closest('.move-cart').dataset.index;
          const item = wishlist[index];
          showNotification(`${item.name} moved to cart!`);
          // In a real app, you would add to cart here
        });
      });

      document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const index = e.target.closest('.remove-item').dataset.index;
          const item = wishlist[index];
          wishlist.splice(index, 1);
          showNotification(`${item.name} removed from wishlist`, false);
          renderWishlist();
        });
      });
    }

    // Initial render
    renderWishlist();