// Sample data (normally from previous checkout steps)
    const cart = [
      { id: 1, name: 'Robo Racer', price: 29.99, qty: 2 },
      { id: 2, name: 'Soft Teddy', price: 12.00, qty: 1 }
    ];
    const shippingInfo = { method: 'Standard', cost: 5.00 };

    const summaryBody = document.getElementById('summary-body');
    const subtotalEl = document.getElementById('summary-subtotal');
    const shippingEl = document.getElementById('shipping-method');
    const totalEl = document.getElementById('order-total');

    function renderSummary() {
      summaryBody.innerHTML = '';
      let subtotal = 0;

      cart.forEach(item => {
        const tr = document.createElement('tr');
        const total = item.price * item.qty;
        subtotal += total;
        
        // For responsive design, add data attributes
        tr.innerHTML = `
          <td data-label="Product">${item.name}</td>
          <td data-label="Quantity">${item.qty}</td>
          <td data-label="Price">$${item.price.toFixed(2)}</td>
          <td data-label="Total">$${total.toFixed(2)}</td>
        `;
        summaryBody.appendChild(tr);
      });

      subtotalEl.textContent = 'Subtotal: $' + subtotal.toFixed(2);
      shippingEl.textContent = 'Shipping: ' + shippingInfo.method + ' ($' + shippingInfo.cost.toFixed(2) + ')';
      totalEl.textContent = 'Order Total: $' + (subtotal + shippingInfo.cost).toFixed(2);
    }

    document.getElementById('confirm-order').addEventListener('click', () => {
      alert('Order confirmed! Thank you for your purchase.');
      window.location.href = 'index.html';
    });

    // Initialize the page
    renderSummary();