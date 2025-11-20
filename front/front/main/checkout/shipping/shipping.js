document.getElementById('shipping-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const address = {
        fullname: formData.get('fullname'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        address1: formData.get('address1'),
        address2: formData.get('address2'),
        city: formData.get('city'),
        postal: formData.get('postal'),
        country: formData.get('country'),
        shipping: formData.get('shipping')
      };
      console.log("Shipping info:", address);
      alert("Shipping info saved. Proceeding to payment...");
      window.location.href = "checkout-payment.html";
    });