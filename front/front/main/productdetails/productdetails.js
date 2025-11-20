const product = {
      id: 1,
      name: 'Robo Racer',
      price: 29.99,
      description: 'High-speed robot car with remote control. This amazing toy features LED lights, realistic engine sounds, and can reach speeds up to 10 mph. Perfect for children ages 6 and up.',
      images: [
        'https://via.placeholder.com/300x300?text=Robo+1',
        'https://via.placeholder.com/300x300?text=Robo+2',
        'https://via.placeholder.com/300x300?text=Robo+3'
      ]
    };

    const reviews = [
      { name: 'Alice', rating: 5, comment: 'Amazing toy! My kids love it.' },
      { name: 'Bob', rating: 4, comment: 'Good value for money. The battery life could be better but overall a great product.' }
    ];

    // Populate product info
    document.getElementById('product-title').textContent = product.name;
    document.getElementById('product-price').textContent = '$' + product.price.toFixed(2);
    document.getElementById('product-description').textContent = product.description;

    // Gallery functionality
    let currentImgIndex = 0;
    const mainImg = document.getElementById('main-img');
    const thumbnails = document.getElementById('thumbnails');

    function showImage(index) {
      currentImgIndex = index;
      mainImg.src = product.images[index];
      
      // Update active thumbnail
      const thumbImgs = thumbnails.querySelectorAll('img');
      thumbImgs.forEach((img, i) => {
        if (i === index) {
          img.classList.add('active');
        } else {
          img.classList.remove('active');
        }
      });
    }

    product.images.forEach((src, i) => {
      const img = document.createElement('img');
      img.src = src;
      img.alt = product.name + ' thumbnail ' + (i + 1);
      img.addEventListener('click', () => showImage(i));
      thumbnails.appendChild(img);
    });

    document.getElementById('prev-img').addEventListener('click', () => {
      currentImgIndex = (currentImgIndex - 1 + product.images.length) % product.images.length;
      showImage(currentImgIndex);
    });

    document.getElementById('next-img').addEventListener('click', () => {
      currentImgIndex = (currentImgIndex + 1) % product.images.length;
      showImage(currentImgIndex);
    });

    showImage(0);

    // Add to cart button
    document.getElementById('add-to-cart').addEventListener('click', () => {
      const size = document.getElementById('variant-size').value;
      const color = document.getElementById('variant-color').value;
      alert(`${product.name} (${size}, ${color}) added to cart.`);
    });

    // Render reviews
    const reviewsList = document.getElementById('reviews-list');
    function renderReviews() {
      reviewsList.innerHTML = '';
      reviews.forEach(r => {
        const li = document.createElement('li');
        li.innerHTML = `<span class="reviewer-name">${r.name}</span> 
                        <span class="review-rating">${'★'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</span>
                        <p>${r.comment}</p>`;
        reviewsList.appendChild(li);
      });
    }
    renderReviews();

    // Handle review form
    document.getElementById('review-form').addEventListener('submit', ev => {
      ev.preventDefault();
      const name = document.getElementById('reviewer-name').value;
      const rating = parseInt(document.getElementById('review-rating').value);
      const comment = document.getElementById('review-comment').value;
      reviews.push({ name, rating, comment });
      renderReviews();
      ev.target.reset();
    });