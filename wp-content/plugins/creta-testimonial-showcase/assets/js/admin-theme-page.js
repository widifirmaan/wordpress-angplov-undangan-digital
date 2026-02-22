document.addEventListener('DOMContentLoaded', function () {

  const searchInput = document.getElementById('theme-search');
  const filterSelect = document.getElementById('theme-filter');
  const themeGrid = document.getElementById('theme-grid');
  const readMoreBtn = document.getElementById('theme-readmore');

  let paginationStack = [''];
  const pageSize = 12;
  let isLoading = false;
  let hasNextPage = true;
  let collectionHandle = '';
  let searchTerm = '';

  const showLoader = () => {
    const loader = document.getElementById('theme-loader');
    if (loader) loader.style.display = 'block';
    // themeGrid.style.display = 'none';
  };

  const hideLoader = () => {
    const loader = document.getElementById('theme-loader');
    if (loader) loader.style.display = 'none';
    if ( themeGrid != null ) {
      themeGrid.style.display = 'flex'; // or 'block'
    }
  };

  const getProducts = (append = false) => {
    if (isLoading || !hasNextPage) return;
    isLoading = true;

    const afterCursor = paginationStack[paginationStack.length - 1];

    showLoader();

    fetch(ajaxurl + '?action=get_elemento_products', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        collectionHandle,
        productHandle: searchTerm,
        paginationParams: {
          first: pageSize,
          afterCursor: afterCursor,
          beforeCursor: '',
          reverse: true
        }
      })
    })
    .then(res => res.json())
    .then(json => {
      const parsed = typeof json === 'string' ? JSON.parse(json) : json;
      const products = parsed.data.products;
      const pageInfo = parsed.data.pageInfo;

      hasNextPage = pageInfo.hasNextPage;
      paginationStack.push(pageInfo.endCursor);

      renderProducts(products, append);
      if ( readMoreBtn != null ) {
        if (!hasNextPage) {
          readMoreBtn.style.display = 'none';
        } else {
          readMoreBtn.style.display = 'inline-block';
        }
      }
    })
    .finally(() => {
      hideLoader();
      isLoading = false;
    });
  };

  const renderProducts = async (products, append = false) => {

    let html = '';
    await products.map(({ node }) => {

        if ( node.hasOwnProperty("inCollection") && node.inCollection == false || node.handle == 'wp-theme-bundle-copy' ) {        
        return;
        }

        html += `<div class="col-md-4">
        <div class="theme-wrapper">
          <div class="theme-card text-center p-3">
            <a href="${node.onlineStoreUrl}" target="_blank">
              <img class="img-fluid" src="${node.images.edges[0].node.src}" alt="${node.title}">
            </a>
          </div>
          <div class="pt-3 card-body">
            <h6 class="theme-title mb-3">${node.title}</h6>
            <div class="d-flex justify-content-center gap-2">`;

        if (node.metafield?.value != undefined) {
            html += `<a href="${node.metafield?.value || '#'}" class="btn btn-sm btn-primary rounded-pill px-3" target="_blank">Live Demo</a>`;
        }
        
        html += `<a href="${node.onlineStoreUrl || '#'}" class="btn btn-sm btn-success rounded-pill px-3" target="_blank">Buy Now</a>
                </div>
            </div>
            </div>
        </div>`;
    });

    if ( themeGrid != null ) {

      if (append) {
        themeGrid.innerHTML += html;
      } else {
  
        if (html == '') {
          html = '<h4 class="card-title">No Result Found</h4>';
        }
  
        themeGrid.innerHTML = html;
      }
    }
    
  };

  const getCollections = () => {
    fetch(ajaxurl + '?action=get_elemento_collections')
      .then(res => res.json())
      .then(json => {
        const parsed = typeof json === 'string' ? JSON.parse(json) : json;
        const items = parsed.data;

        items.sort((a, b) => a.title.localeCompare(b.title));

        let html = '';
        items.forEach(item => {
          if (item.handle === 'free-wordpress-themes') return;
          html += `<li><input type="radio" name="theme_cat" value="${item.handle}" hidden>${item.title}</li>`;
        });

        if ( filterSelect != null ) {
          filterSelect.innerHTML = html;
        }
      });
  };

  if ( readMoreBtn != null ) {
    // Event: Read More button
    readMoreBtn.addEventListener('click', () => {
      getProducts(true); // append = true
    });
  }

  if ( searchInput != null ) {
    
    // Event: search
    searchInput.addEventListener('input', () => {
      searchTerm = searchInput.value;
      paginationStack = [''];
      hasNextPage = true;
      getProducts(false); // append = false (fresh render)
    });
  }

  if ( filterSelect != null ) {
    // Click on <li> triggers input selection
    filterSelect.addEventListener('click', (e) => {
      const li = e.target.closest('li');
      if (!li) return;
      const input = li.querySelector('input[type="radio"]');
      if (input) {
        input.checked = true;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  
    // Event: filter radio change
    filterSelect.addEventListener('change', (e) => {
      if (e.target.name === 'theme_cat') {
        collectionHandle = e.target.value;
        paginationStack = [''];
        hasNextPage = true;
        getProducts(false); // append = false
      }
    });
  }


  // Init
  searchTerm = '';
  getProducts(false);
  getCollections();

});
