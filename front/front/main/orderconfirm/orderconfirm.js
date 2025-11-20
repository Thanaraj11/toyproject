// Sample order data
    const orderNumber = 'ORD' + Math.floor(Math.random()*1000000);
    const estimatedDelivery = new Date();
    estimatedDelivery.setDate(estimatedDelivery.getDate() + 7); // 7 days from now

    const cart = [
      { name: 'Robo Racer', price: 29.99, qty: 2 },
      { name: 'Soft Teddy', price: 12.00, qty: 1 }
    ];
    const shippingCost = 5.00;

    document.getElementById('order-number').textContent = orderNumber;
    document.getElementById('delivery-date').textContent = estimatedDelivery.toDateString();

    // Render purchase summary
    const summaryBody = document.getElementById('summary-body');
    let subtotal = 0;
    cart.forEach(item => {
      const tr = document.createElement('tr');
      const total = item.price * item.qty;
      subtotal += total;
      tr.innerHTML = `<td>${item.name}</td><td>${item.qty}</td><td>$${item.price.toFixed(2)}</td><td>$${total.toFixed(2)}</td>`;
      summaryBody.appendChild(tr);
    });

    document.getElementById('subtotal').textContent = 'Subtotal: $' + subtotal.toFixed(2);
    document.getElementById('shipping').textContent = 'Shipping: $' + shippingCost.toFixed(2);
    document.getElementById('order-total').textContent = 'Order Total: $' + (subtotal + shippingCost).toFixed(2);