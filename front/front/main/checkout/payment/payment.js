const paymentForm = document.getElementById('payment-form');
    const cardDetailsSection = document.getElementById('card-details');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment-method"]');

    // Show/Hide card details based on selected method
    paymentMethodRadios.forEach(radio => {
      radio.addEventListener('change', () => {
        if (radio.value === 'credit' && radio.checked) {
          cardDetailsSection.style.display = 'block';
        } else {
          cardDetailsSection.style.display = 'none';
        }
      });
    });

    // Format card number input
    const cardNumberInput = document.querySelector('input[name="cardnumber"]');
    cardNumberInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 16) value = value.slice(0, 16);
      
      // Add spaces for better readability
      if (value.length > 12) {
        value = value.replace(/(\d{4})(\d{4})(\d{4})(\d{0,4})/, '$1 $2 $3 $4');
      } else if (value.length > 8) {
        value = value.replace(/(\d{4})(\d{4})(\d{0,4})/, '$1 $2 $3');
      } else if (value.length > 4) {
        value = value.replace(/(\d{4})(\d{0,4})/, '$1 $2');
      }
      
      e.target.value = value.trim();
    });

    // Format expiry date input
    const expiryInput = document.querySelector('input[name="expiry"]');
    expiryInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 4) value = value.slice(0, 4);
      
      if (value.length > 2) {
        value = value.replace(/(\d{2})(\d{0,2})/, '$1/$2');
      }
      
      e.target.value = value;
    });

    paymentForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(paymentForm);
      const paymentInfo = {
        method: formData.get('payment-method'),
        cardname: formData.get('cardname'),
        cardnumber: formData.get('cardnumber').replace(/\s/g, ''),
        expiry: formData.get('expiry'),
        cvv: formData.get('cvv')
      };
      
      // Basic validation
      if (paymentInfo.method === 'credit') {
        if (!paymentInfo.cardname) {
          alert('Please enter cardholder name');
          return;
        }
        if (paymentInfo.cardnumber.replace(/\D/g, '').length !== 16) {
          alert('Please enter a valid 16-digit card number');
          return;
        }
        if (!paymentInfo.expiry || !paymentInfo.expiry.includes('/')) {
          alert('Please enter a valid expiry date in MM/YY format');
          return;
        }
        if (!paymentInfo.cvv || paymentInfo.cvv.length < 3) {
          alert('Please enter a valid CVV');
          return;
        }
      }
      
      console.log('Payment info:', paymentInfo);
      alert('Order placed successfully!');
      window.location.href = 'order-confirmation.html';
    });