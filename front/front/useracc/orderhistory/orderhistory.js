// Sample past orders data
    const orders = [
      { id: 'ORD12345', date: '2025-09-01', status: 'Delivered', total: 71.98 },
      { id: 'ORD12346', date: '2025-09-12', status: 'Processing', total: 29.99 },
      { id: 'ORD12347', date: '2025-08-25', status: 'Shipped', total: 125.50 },
      { id: 'ORD12348', date: '2025-08-15', status: 'Cancelled', total: 45.75 }
    ];

    const ordersBody = document.getElementById('orders-body');

    function renderOrders() {
      ordersBody.innerHTML = '';
      
      if (orders.length === 0) {
        ordersBody.innerHTML = `
          <tr>
            <td colspan="5">
              <div class="empty-state">
                <p>You haven't placed any orders yet.</p>
                <a href="index.html" class="reorder">Start Shopping</a>
              </div>
            </td>
          </tr>
        `;
        return;
      }
      
      orders.forEach(order => {
        const tr = document.createElement('tr');
        
        // Format date for better readability
        const formattedDate = new Date(order.date).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
        
        // Determine status class
        let statusClass = '';
        switch(order.status.toLowerCase()) {
          case 'delivered':
            statusClass = 'status-delivered';
            break;
          case 'processing':
            statusClass = 'status-processing';
            break;
          case 'shipped':
            statusClass = 'status-shipped';
            break;
          case 'cancelled':
            statusClass = 'status-cancelled';
            break;
        }
        
        tr.innerHTML = `
          <td data-label="Order #">${order.id}</td>
          <td data-label="Date">${formattedDate}</td>
          <td data-label="Status"><span class="status-badge ${statusClass}">${order.status}</span></td>
          <td data-label="Total">$${order.total.toFixed(2)}</td>
          <td data-label="Actions">
            <button class='reorder'>Reorder</button>
            <a href="#" class="view-details">View Details</a>
          </td>
        `;
        ordersBody.appendChild(tr);
      });

      // Add event listeners to reorder buttons
      document.querySelectorAll('.reorder').forEach((btn, i) => {
        btn.addEventListener('click', () => {
          alert('Reordering ' + orders[i].id);
        });
      });
      
      // Add event listeners to view details links
      document.querySelectorAll('.view-details').forEach((link, i) => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          alert('Viewing details for ' + orders[i].id);
        });
      });
    }

    renderOrders();