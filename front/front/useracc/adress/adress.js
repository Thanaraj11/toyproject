let addresses = [
      { fullname: 'John Doe', phone: '1234567890', address1: '123 Street', address2: '', city: 'Cityville', postal: '12345', country: 'Countryland' }
    ];

    const addressesUl = document.getElementById('addresses-ul');
    const addressFormSection = document.getElementById('address-form-section');
    const addressForm = document.getElementById('address-form');
    const formTitle = document.getElementById('form-title');
    let editingIndex = null;

    function renderAddresses() {
      addressesUl.innerHTML = '';
      addresses.forEach((addr, i) => {
        const li = document.createElement('li');
        
        const addressInfo = document.createElement('div');
        addressInfo.className = 'address-info';
        
        const nameDiv = document.createElement('div');
        nameDiv.className = 'address-name';
        nameDiv.textContent = addr.fullname;
        addressInfo.appendChild(nameDiv);
        
        const phoneDiv = document.createElement('p');
        phoneDiv.textContent = `Phone: ${addr.phone}`;
        addressInfo.appendChild(phoneDiv);
        
        const addressDiv = document.createElement('p');
        addressDiv.textContent = `Address: ${addr.address1}${addr.address2 ? ', ' + addr.address2 : ''}`;
        addressInfo.appendChild(addressDiv);
        
        const cityDiv = document.createElement('p');
        cityDiv.textContent = `${addr.city}, ${addr.postal}, ${addr.country}`;
        addressInfo.appendChild(cityDiv);
        
        li.appendChild(addressInfo);
        
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'address-actions';
        
        const editBtn = document.createElement('button');
        editBtn.textContent = 'Edit';
        editBtn.className = 'edit-btn';
        editBtn.addEventListener('click', () => editAddress(i));
        actionsDiv.appendChild(editBtn);
        
        li.appendChild(actionsDiv);
        addressesUl.appendChild(li);
      });
    }

    function editAddress(index) {
      editingIndex = index;
      formTitle.textContent = 'Edit Address';
      const addr = addresses[index];
      for (const key in addr) {
        addressForm[key].value = addr[key];
      }
      addressFormSection.style.display = 'block';
      // Scroll to form
      addressFormSection.scrollIntoView({ behavior: 'smooth' });
    }

    document.getElementById('add-address').addEventListener('click', () => {
      editingIndex = null;
      formTitle.textContent = 'Add Address';
      addressForm.reset();
      addressFormSection.style.display = 'block';
      // Scroll to form
      addressFormSection.scrollIntoView({ behavior: 'smooth' });
    });

    document.getElementById('cancel-address').addEventListener('click', () => {
      addressFormSection.style.display = 'none';
    });

    addressForm.addEventListener('submit', e => {
      e.preventDefault();
      const formData = new FormData(addressForm);
      const addr = {};
      for (const [key, value] of formData.entries()) {
        addr[key] = value;
      }
      if (editingIndex !== null) {
        addresses[editingIndex] = addr;
      } else {
        addresses.push(addr);
      }
      addressFormSection.style.display = 'none';
      renderAddresses();
    });

    renderAddresses();