// Sample cart data (normally fetched from localStorage or server)
    let cart = [
      { id: 1, name: 'Robo Racer', price: 29.99, qty: 2 },
      { id: 2, name: 'Soft Teddy', price: 12.00, qty: 1 }
    ];

    const cartBody = document.getElementById('cart-body');
    const rowTemplate = document.getElementById('cart-row-template');
    const subtotalEl = document.getElementById('subtotal');

    function updateCart() {
      cartBody.innerHTML = '';
      let subtotal = 0;

      cart.forEach((item, index) => {
        const row = rowTemplate.content.cloneNode(true);
        const nameCell = row.querySelector('.cart-name');
        const priceCell = row.querySelector('.cart-price');
        const qtyInput = row.querySelector('.cart-qty');
        const totalCell = row.querySelector('.cart-total');
        const btnDec = row.querySelector('.qty-decrease');
        const btnInc = row.querySelector('.qty-increase');
        const btnRemove = row.querySelector('.remove');

        nameCell.textContent = item.name;
        priceCell.textContent = '$' + item.price.toFixed(2);
        qtyInput.value = item.qty;
        totalCell.textContent = '$' + (item.price * item.qty).toFixed(2);

        subtotal += item.price * item.qty;

        btnDec.addEventListener('click', () => {
          if (item.qty > 1) item.qty--;
          updateCart();
        });
        btnInc.addEventListener('click', () => {
          item.qty++;
          updateCart();
        });
        qtyInput.addEventListener('change', () => {
          const val = parseInt(qtyInput.value);
          if (!isNaN(val) && val > 0) {
            item.qty = val;
          } else {
            item.qty = 1;
          }
          updateCart();
        });
        btnRemove.addEventListener('click', () => {
          cart.splice(index, 1);
          updateCart();
        });

        cartBody.appendChild(row);
      });

      subtotalEl.textContent = 'Subtotal: $' + subtotal.toFixed(2);
    }

    document.getElementById('checkout').addEventListener('click', () => {
      if (cart.length === 0) {
        alert('Your cart is empty!');
      } else {
        alert('Proceeding to checkout with ' + cart.length + ' items.');
      }
    });

    updateCart();