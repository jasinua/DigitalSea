<?php 
    session_start();
    require_once 'model/dbh.inc.php';
    require_once 'controller/home.inc.php';

    // Get current page from URL, default to 1
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $items_per_page = 18;
    
    // Get wishlist items in one query
    $wishlist_items = isset($_SESSION['user_id']) ? getWishlistItems($_SESSION['user_id']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalSea</title>
    
    <!-- Resource hints -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    
    <!-- Critical CSS inline -->
    <?php include "css/index-css.php" ?>
    
    <!-- Defer non-critical CSS -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></noscript>
    
    <link rel="preload" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"></noscript>
    
    <!-- Defer JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>
    
    <!-- Add lazy loading for images -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize filter functionality
            document.querySelectorAll('.category-header').forEach(function(header) {
                header.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    this.classList.toggle('collapsed');
                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'block';
                    }
                });
            });

            // Initialize filter sections
            document.querySelectorAll('.filter-section h3').forEach(function(header) {
                header.addEventListener('click', function() {
                    const section = this.parentElement;
                    const content = this.nextElementSibling;
                    section.classList.toggle('collapsed');
                    if (content.style.display === 'block') {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'block';
                    }
                });
            });

            // Clear all filters function
            window.clearAllFilters = function() {
                // Redirect to base page without any filters
                window.location.href = 'index.php';
            };

            // Function to update all instances of a product's heart button
            function updateAllProductHearts(productId, isActive) {
                document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"]`).forEach(function(btn) {
                    const icon = btn.querySelector('i');
                    if (isActive) {
                        btn.classList.add('active');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        btn.classList.remove('active');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                });
            }

            // Initialize wishlist functionality
            document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const productId = this.dataset.productId;
                    
                    fetch('controller/add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.trim() === 'not_logged_in') {
                            window.location.href = 'login.php';
                        } else if (result.trim() === 'added') {
                            updateAllProductHearts(productId, true);
                        } else if (result.trim() === 'removed') {
                            updateAllProductHearts(productId, false);
                        } else if (result.trim() === 'error') {
                            console.error('Error updating wishlist');
                            alert('There was an error updating your wishlist. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('There was an error updating your wishlist. Please try again.');
                    });
                });
            });

            // Check initial wishlist status for each product
            document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
                const productId = btn.dataset.productId;
                fetch('controller/check_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === 'true') {
                        updateAllProductHearts(productId, true);
                    }
                })
                .catch(error => {
                    console.error('Error checking wishlist status:', error);
                });
            });

            // Wheel Carousel functionality
            const wheelTrack = document.getElementById('topItems');
            const wheelItems = wheelTrack.querySelectorAll('.wheel-item');
            const wheelPrev = document.getElementById('wheelPrev');
            const wheelNext = document.getElementById('wheelNext');
            // Calculate middle index (for even numbers, pick the left middle)
            let currentWheelIndex = Math.floor(wheelItems.length / 2);
            const visibleWheelItems = 3;

            function updateWheelCarousel() {
                wheelItems.forEach((item, index) => {
                    item.classList.remove('active', 'left', 'right', 'hidden');
                    
                    // Handle edge cases
                    if (currentWheelIndex === 0) {
                        // At the start
                        if (index === 0) {
                            item.classList.add('active');
                        } else if (index === 1) {
                            item.classList.add('right');
                        } else {
                            item.classList.add('hidden');
                        }
                    } else if (currentWheelIndex === wheelItems.length - 1) {
                        // At the end
                        if (index === wheelItems.length - 1) {
                            item.classList.add('active');
                        } else if (index === wheelItems.length - 2) {
                            item.classList.add('left');
                        } else {
                            item.classList.add('hidden');
                        }
                    } else {
                        // Normal case - middle of the carousel
                        const relativePos = (index - currentWheelIndex + wheelItems.length) % wheelItems.length;
                        
                        if (relativePos === 0) {
                            item.classList.add('active');
                        } else if (relativePos === wheelItems.length - 1) {
                            item.classList.add('left');
                        } else if (relativePos === 1) {
                            item.classList.add('right');
                        } else {
                            item.classList.add('hidden');
                        }
                    }
                });

                // Update arrow states
                wheelPrev.style.display = currentWheelIndex === 0 ? 'none' : 'flex';
                wheelNext.style.display = currentWheelIndex >= wheelItems.length - 1 ? 'none' : 'flex';
            }

            wheelPrev.addEventListener('click', () => {
                if (currentWheelIndex > 0) {
                    currentWheelIndex--;
                    updateWheelCarousel();
                }
            });

            wheelNext.addEventListener('click', () => {
                if (currentWheelIndex < wheelItems.length - 1) {
                    currentWheelIndex++;
                    updateWheelCarousel();
                }
            });

            // Initialize wheel carousel
            updateWheelCarousel();

            // New Items Carousel functionality
            const newItems = document.getElementById('newItems');
            const newItemsPrev = document.getElementById('newItemsPrev');
            const newItemsNext = document.getElementById('newItemsNext');
            const itemWidth = 330; // Width of one item
            const itemMargin = 20; // Margin between items
            const scrollAmount = itemWidth + itemMargin; // Total scroll amount for one item
            let visibleNewItems = 4; // Default for larger screens
            
            // Adjust visible items based on screen size
            function updateVisibleItems() {
                if (window.innerWidth <= 400) {
                    visibleNewItems = 2;
                } else if (window.innerWidth <= 768) {
                    visibleNewItems = 3;
                } else {
                    visibleNewItems = 4;
                }
                updateNewItemsArrows();
            }
            
            // Initial update and add resize listener
            updateVisibleItems();
            window.addEventListener('resize', updateVisibleItems);

            function updateNewItemsArrows() {
                newItemsPrev.disabled = currentNewItemsIndex === 0;
                newItemsNext.disabled = currentNewItemsIndex >= newItems.children.length - visibleNewItems;
            }

            let currentNewItemsIndex = 0;

            newItemsPrev.addEventListener('click', () => {
                if (currentNewItemsIndex > 0) {
                    currentNewItemsIndex--;
                    const smallScreen = window.innerWidth <= 400;
                    const scrollSize = smallScreen ? 160 : scrollAmount; // 150px width + 10px gap for small screens
                    
                    newItems.scrollBy({
                        left: -scrollSize,
                        behavior: 'smooth'
                    });
                    updateNewItemsArrows();
                }
            });

            newItemsNext.addEventListener('click', () => {
                if (currentNewItemsIndex < newItems.children.length - visibleNewItems) {
                    currentNewItemsIndex++;
                    const smallScreen = window.innerWidth <= 400;
                    const scrollSize = smallScreen ? 160 : scrollAmount; // 150px width + 10px gap for small screens
                    
                    newItems.scrollBy({
                        left: scrollSize,
                        behavior: 'smooth'
                    });
                    updateNewItemsArrows();
                }
            });

            // Update arrow states on scroll
            newItems.addEventListener('scroll', () => {
                const scrollPosition = newItems.scrollLeft;
                currentNewItemsIndex = Math.round(scrollPosition / scrollAmount);
                updateNewItemsArrows();
            });

            // Initial arrow state
            updateNewItemsArrows();

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    if (document.activeElement === wheelTrack) {
                        wheelPrev.click();
                    } else if (document.activeElement === newItems) {
                        newItemsPrev.click();
                    }
                } else if (e.key === 'ArrowRight') {
                    if (document.activeElement === wheelTrack) {
                        wheelNext.click();
                    } else if (document.activeElement === newItems) {
                        newItemsNext.click();
                    }
                }
            });

            // Make carousels focusable
            wheelTrack.setAttribute('tabindex', '0');
            newItems.setAttribute('tabindex', '0');
        });
    </script>
</head>
<body>
    <div class="page-wrapper">
        <?php include "header/header.php" ?>
        <div id='container'>
            <div id='filters'>
                <button type="button" class="close-filters"><i class="fas fa-times"></i></button>
                <form id='filterForm' action="index.php" method='get'>
                    <div id='filtOpts'>
                        <input type='button' value='Clear Filters' onclick="clearAllFilters()">
                        <input type='submit' value='Apply Filters'>
                    </div>
                    
                    <div class="filter-section">
                        <h3>Categories</h3>
                        <div class="filter-content">
                            <?php 
                            $categories = [
                                'Computers & Laptops' => [
                                    'keywords' => ['computer', 'laptop', 'desktop', 'notebook'],
                                    'subcategories' => [
                                        'Gaming Laptops' => ['gaming laptop', 'gaming notebook'],
                                        'Business Laptops' => ['business laptop', 'professional laptop'],
                                        'All-in-One PCs' => ['all in one', 'aio pc'],
                                        'Desktop Towers' => ['desktop tower', 'pc tower'],
                                        'Workstations' => ['workstation', 'professional desktop']
                                    ]
                                ],
                                'Smartphones & Tablets' => [
                                    'keywords' => ['phone', 'smartphone', 'tablet', 'mobile'],
                                    'subcategories' => [
                                        'Flagship Phones' => ['flagship', 'premium phone'],
                                        'Budget Phones' => ['budget phone', 'affordable phone'],
                                        'iPads & Tablets' => ['ipad', 'tablet'],
                                        'Foldable Phones' => ['foldable', 'fold phone'],
                                        'Gaming Phones' => ['gaming phone', 'game phone']
                                    ]
                                ],
                                'Audio & Headphones' => [
                                    'keywords' => ['headphone', 'earphone', 'speaker', 'audio'],
                                    'subcategories' => [
                                        'Wireless Headphones' => ['wireless headphone', 'bluetooth headphone'],
                                        'Gaming Headsets' => ['gaming headset', 'game headphone'],
                                        'True Wireless Earbuds' => ['true wireless', 'wireless earbud'],
                                        'Studio Monitors' => ['studio monitor', 'monitor speaker'],
                                        'Portable Speakers' => ['portable speaker', 'bluetooth speaker']
                                    ]
                                ],
                                'Gaming & Consoles' => [
                                    'keywords' => ['game', 'console', 'gaming', 'controller'],
                                    'subcategories' => [
                                        'Gaming Consoles' => ['playstation', 'xbox', 'nintendo'],
                                        'Gaming PCs' => ['gaming pc', 'gaming desktop'],
                                        'Gaming Accessories' => ['gaming mouse', 'gaming keyboard'],
                                        'VR Headsets' => ['vr headset', 'virtual reality'],
                                        'Gaming Monitors' => ['gaming monitor', 'game display']
                                    ]
                                ],
                                'Cameras & Photography' => [
                                    'keywords' => ['camera', 'photo', 'lens', 'digital camera'],
                                    'subcategories' => [
                                        'DSLR Cameras' => ['dslr', 'digital slr'],
                                        'Mirrorless Cameras' => ['mirrorless', 'mirror less'],
                                        'Action Cameras' => ['action camera', 'gopro'],
                                        'Camera Lenses' => ['camera lens', 'photography lens'],
                                        'Camera Accessories' => ['camera accessory', 'photo accessory']
                                    ]
                                ],
                                'Networking & Internet' => [
                                    'keywords' => ['router', 'network', 'wifi', 'modem'],
                                    'subcategories' => [
                                        'WiFi Routers' => ['wifi router', 'wireless router'],
                                        'Mesh Systems' => ['mesh wifi', 'mesh system'],
                                        'Network Switches' => ['network switch', 'ethernet switch'],
                                        'Modems' => ['modem', 'cable modem'],
                                        'Network Cards' => ['network card', 'wifi card']
                                    ]
                                ],
                                'Storage & Memory' => [
                                    'keywords' => ['storage', 'memory', 'ssd', 'hard drive'],
                                    'subcategories' => [
                                        'SSDs' => ['ssd', 'solid state'],
                                        'Hard Drives' => ['hard drive', 'hdd'],
                                        'USB Drives' => ['usb drive', 'flash drive'],
                                        'Memory Cards' => ['memory card', 'sd card'],
                                        'External Storage' => ['external drive', 'portable drive']
                                    ]
                                ],
                                'Components & Parts' => [
                                    'keywords' => ['component', 'part', 'processor', 'motherboard'],
                                    'subcategories' => [
                                        'Processors' => ['processor', 'cpu'],
                                        'Graphics Cards' => ['graphics card', 'gpu'],
                                        'Motherboards' => ['motherboard', 'mainboard'],
                                        'RAM' => ['ram', 'memory'],
                                        'Power Supplies' => ['power supply', 'psu']
                                    ]
                                ],
                                'Accessories & Peripherals' => [
                                    'keywords' => ['accessory', 'peripheral', 'keyboard', 'mouse'],
                                    'subcategories' => [
                                        'Keyboards' => ['keyboard', 'mechanical keyboard'],
                                        'Mice' => ['mouse', 'gaming mouse'],
                                        'Monitors' => ['monitor', 'display'],
                                        'Webcams' => ['webcam', 'camera'],
                                        'Printers' => ['printer', 'scanner']
                                    ]
                                ],
                                'Smart Home & IoT' => [
                                    'keywords' => ['smart home', 'iot', 'smart device', 'automation'],
                                    'subcategories' => [
                                        'Smart Speakers' => ['smart speaker', 'voice assistant'],
                                        'Smart Lighting' => ['smart light', 'smart bulb'],
                                        'Security Cameras' => ['security camera', 'cctv'],
                                        'Smart Plugs' => ['smart plug', 'smart outlet'],
                                        'Smart Displays' => ['smart display', 'smart screen']
                                    ]
                                ]
                            ];
                            ?>

                            <?php foreach ($categories as $category => $data) { ?>
                                <div class="category-group">
                                    <div class="category-header">
                                        <h4><?php echo $category ?></h4>
                                    </div>
                                    <div class="category-content">
                                        <?php foreach ($data['subcategories'] as $subcat => $subkeywords) { ?>
                                            <div class="subfilter">
                                                <input type="checkbox" name="subfilter[]" id="<?php echo $subcat ?>" value="<?php echo $subcat ?>">
                                                <label for="<?php echo $subcat ?>"><?php echo $subcat ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="filter-section collapsed">
                        <h3>Price Range</h3>
                        <div class="filter-content">
                            <div class="price-range">
                                <input type="number" name="min_price" placeholder="Min €" min="0">
                                <span>-</span>
                                <input type="number" name="max_price" placeholder="Max €" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section collapsed">
                        <h3>Discounts</h3>
                        <div class="filter-content">
                            <div class="filter">
                                <input type="checkbox" name="discounted_only" id="discounted_only" value="1">
                                <label for="discounted_only">Show only items on sale</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id='items'>
                <button class="filter-toggle-top"><i class="fas fa-filter"></i></button>
                <?php if(!isset($_GET['subfilter']) && !isset($_GET['min_price']) && !isset($_GET['max_price']) && !isset($_GET['discounted_only']) && !isset($_GET['search']) && $current_page === 1) { ?>   
                    <h1 id='topItemsHeader'>Top Products</h1>
                    <div class="carousel-container">
                        <button class="carousel-arrow" id="wheelPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    <div class='wheel-carousel'>
                        <div class='wheel-track' id='topItems'>
                                <?php foreach (getData("SELECT * FROM products WHERE products.price>900 LIMIT 8") as $prod) { ?>
                                <div class='wheel-item'>
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                    <?php if ($prod['discount'] > 0) { ?>
                                        <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                    <?php } ?>
                                        <img src="images/product_<?php echo $prod['product_id'] ?>.png" 
                                             alt="<?php echo htmlspecialchars($prod['description']); ?>"
                                             width="225"
                                             height="180">
                                        <div class='title'><?php echo htmlspecialchars($prod['description']); ?></div>
                                    <div class='bottom-container'>
                                        <button class="wishlist-btn <?php echo in_array($prod['product_id'], $wishlist_items) ? 'active' : ''; ?>" data-product-id="<?php echo $prod['product_id']; ?>">
                                            <i class="<?php echo in_array($prod['product_id'], $wishlist_items) ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </button>
                                        <div class='price'>
                                            <?php if ($prod['discount'] > 0) { 
                                                $originalPrice = $prod['price'];
                                                $discountedPrice = $originalPrice * (1 - $prod['discount'] / 100);
                                            ?>
                                                <span class="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                                <span class="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                            <?php } else { ?>
                                                <span class="discounted-price"><?php echo number_format($prod['price'], 2, '.', ',') ?>€</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                        </div>
                        <button class="carousel-arrow" id="wheelNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <h1 id='newItemsHeader'>New Products</h1>
                    <div class="carousel-container">
                        <button class="carousel-arrow" id="newItemsPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    <div class='itemLine' id='newItems'>
                        <?php foreach (getData("SELECT * FROM products ORDER BY product_id DESC LIMIT 8") as $prod) { ?>
                            <div class='item' id="newItemsItem">
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                <div class="new-badge">NEW</div>
                                    <?php if ($prod['discount'] > 0) { ?>
                                        <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                    <?php } ?>
                                    <img src="images/product_<?php echo $prod['product_id'] ?>.png" 
                                         alt="<?php echo htmlspecialchars($prod['description']); ?>"
                                         width="225"
                                         height="180">
                                    <div class='title'><?php echo htmlspecialchars($prod['description']); ?></div>
                                    <div class='bottom-container'>
                                    <button class="wishlist-btn <?php echo in_array($prod['product_id'], $wishlist_items) ? 'active' : ''; ?>" data-product-id="<?php echo $prod['product_id']; ?>">
                                        <i class="<?php echo in_array($prod['product_id'], $wishlist_items) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </button>
                                    <div class='price'>
                                        <?php if ($prod['discount'] > 0) { 
                                            $originalPrice = $prod['price'];
                                            $discountedPrice = $originalPrice * (1 - $prod['discount'] / 100);
                                        ?>
                                            <span class="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                            <span class="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                        <?php } else { ?>
                                            <span class="discounted-price"><?php echo number_format($prod['price'], 2, '.', ',') ?>€</span>
                                        <?php } ?>
                                    </div>
                                </div>
                                </a>
                            </div>
                        <?php } ?>
                        </div>
                        <button class="carousel-arrow" id="newItemsNext">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <h1 id="moreItemsText">More Products</h1>
                    <?php } else { ?>
                        <h1><?php echo isset($_GET['search']) ? 'Search Results' : 'Products'; ?></h1>
                    <?php } ?>
                    <div class='itemBox' id='randomItems'>
                    <?php 
                        if(isset($_GET['subfilter']) || isset($_GET['min_price']) || isset($_GET['max_price']) || isset($_GET['discounted_only']) || isset($_GET['search'])) {
                            $where_conditions = [];
                            
                            if (!empty($_GET['subfilter'])) {
                                $subfilter_conditions = [];
                                foreach ($_GET['subfilter'] as $subcat) {
                                    // Add the subcategory name itself as a search term
                                    $subfilter_conditions[] = "(LOWER(description) LIKE LOWER('%substr($subcat,0,-1)%') OR LOWER(name) LIKE LOWER('%substr($subcat,0,-1)%'))";
                                    
                                    // Also check the keywords for this subcategory
                                    foreach ($categories as $category => $data) {
                                        if (isset($data['subcategories'][$subcat])) {
                                            foreach ($data['subcategories'][$subcat] as $keyword) {
                                                $subfilter_conditions[] = "(LOWER(description) LIKE LOWER('%$keyword%') OR LOWER(name) LIKE LOWER('%$keyword%'))";
                                            }
                                        }
                                    }
                                }

                                if (!empty($subfilter_conditions)) {
                                    $where_conditions[] = "(" . implode(' OR ', $subfilter_conditions) . ")";
                                }
                            }
                            
                            if (!empty($_GET['min_price'])) {
                                $where_conditions[] = "price >= " . floatval($_GET['min_price']);
                            }
                            
                            if (!empty($_GET['max_price'])) {
                                $where_conditions[] = "price <= " . floatval($_GET['max_price']);
                            }

                            if (isset($_GET['discounted_only'])) {
                                $where_conditions[] = "discount > 0";
                            }

                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                // Function to normalize search terms (handle singular/plural)
                                function normalizeSearchTerm($term) {
                                    // Convert term to lowercase
                                    $term = strtolower(trim($term));
                                    
                                    // Common singular/plural mappings
                                    $mappings = [
                                        'watches' => 'watch',
                                        'watch' => 'watch',
                                        'phones' => 'phone',
                                        'phone' => 'phone',
                                        'laptops' => 'laptop',
                                        'laptop' => 'laptop',
                                        'computers' => 'computer',
                                        'computer' => 'computer',
                                        'televisions' => 'television',
                                        'television' => 'television',
                                        'tv' => 'tv',
                                        'tvs' => 'tv',
                                        'headphones' => 'headphone',
                                        'headphone' => 'headphone'
                                    ];
                                    
                                    // Check if term is in mappings
                                    if (isset($mappings[$term])) {
                                        return $mappings[$term];
                                    }
                                    
                                    // If not in mappings, try to convert plural to singular
                                    if (substr($term, -1) === 's') {
                                        return substr($term, 0, -1);
                                    }
                                    
                                    return $term;
                                }
                                
                                $original_search_term = mysqli_real_escape_string($conn, $_GET['search']);
                                $normalized_term = normalizeSearchTerm($original_search_term);
                                
                                // Build search condition with both original and normalized terms
                                $search_condition = "(LOWER(description) LIKE LOWER('%$original_search_term%') OR 
                                                    LOWER(name) LIKE LOWER('%$original_search_term%')";
                                
                                // Add normalized term if different from original
                                if ($normalized_term !== strtolower($original_search_term)) {
                                    $search_condition .= " OR LOWER(description) LIKE LOWER('%$normalized_term%') OR 
                                                         LOWER(name) LIKE LOWER('%$normalized_term%')";
                                }
                                
                                // Add singular/plural variation
                                $plural_term = $normalized_term . 's';
                                $search_condition .= " OR LOWER(description) LIKE LOWER('%$plural_term%') OR 
                                                     LOWER(name) LIKE LOWER('%$plural_term%')";
                                
                                $search_condition .= ")";
                                $where_conditions[] = $search_condition;
                            }
                            
                            $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
                            
                            // Get total count of filtered products for pagination
                            $count_query = "SELECT COUNT(*) as total FROM products $where_clause";
                            $count_result = $conn->query($count_query);
                            $total_products = $count_result->fetch_assoc()['total'];
                            $total_pages = ceil($total_products / $items_per_page);
                            
                            // Add pagination to the query
                            $offset = ($current_page - 1) * $items_per_page;
                            $products = getData("SELECT * FROM products $where_clause LIMIT $items_per_page OFFSET $offset");
                        } else {
                            $products = getProducts($current_page, $items_per_page);
                            $total_products = getTotalProducts();
                            $total_pages = ceil($total_products / $items_per_page);
                        }
                    
                        foreach ($products as $prod) { ?>
                            <div class='item'>
                                <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                <?php if ($prod['discount'] > 0) { ?>
                                    <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                <?php } ?>
                                    <img src="images/product_<?php echo $prod['product_id'] ?>.png" 
                                         alt="<?php echo htmlspecialchars($prod['description']); ?>"
                                         width="225"
                                         height="180">
                                    <div class='title'><?php echo htmlspecialchars($prod['description']); ?></div>
                                <div class='bottom-container'>
                                    <button class="wishlist-btn <?php echo in_array($prod['product_id'], $wishlist_items) ? 'active' : ''; ?>" data-product-id="<?php echo $prod['product_id']; ?>">
                                        <i class="<?php echo in_array($prod['product_id'], $wishlist_items) ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </button>
                                    <div class='price'>
                                        <?php if ($prod['discount'] > 0) { 
                                            $originalPrice = $prod['price'];
                                            $discountedPrice = $originalPrice * (1 - $prod['discount'] / 100);
                                        ?>
                                            <span class="original-price"><?php echo number_format($originalPrice, 2, '.', ',') ?>€</span>
                                            <span class="discounted-price"><?php echo number_format($discountedPrice, 2, '.', ',') ?>€</span>
                                        <?php } else { ?>
                                                <span class="discounted-price"><?php echo number_format($prod['price'], 2, '.', ',') ?>€</span>
                                        <?php } ?>   
                                    </div>
                                </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>

                <!-- Add pagination controls -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <div class="pagination-container">
                            <?php 
                            // Build query string for pagination links
                            $query_params = [];
                            if (isset($_GET['search'])) {
                                $query_params['search'] = $_GET['search'];
                            }
                            if (isset($_GET['subfilter'])) {
                                foreach ($_GET['subfilter'] as $filter) {
                                    $query_params['subfilter'][] = $filter;
                                }
                            }
                            if (isset($_GET['min_price'])) {
                                $query_params['min_price'] = $_GET['min_price'];
                            }
                            if (isset($_GET['max_price'])) {
                                $query_params['max_price'] = $_GET['max_price'];
                            }
                            if (isset($_GET['discounted_only'])) {
                                $query_params['discounted_only'] = $_GET['discounted_only'];
                            }
                            
                            // Function to build query string
                            function buildQueryString($page, $params) {
                                $params['page'] = $page;
                                return '?' . http_build_query($params);
                            }
                            ?>
                            
                            <?php if ($current_page > 1): ?>
                                <a href="<?php echo buildQueryString($current_page - 1, $query_params); ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>

                            <div class="page-numbers">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="<?php echo buildQueryString($i, $query_params); ?>" 
                                       class="page-number <?php echo $i === $current_page ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="<?php echo buildQueryString($current_page + 1, $query_params); ?>" class="pagination-btn">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php include "footer/footer.php" ?>
    </div>

<!-- Move scripts to bottom of page and optimize loading -->
<script>
    // Defer non-critical JavaScript execution
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize filter toggle button
        const filterToggleTop = document.querySelector('.filter-toggle-top');
        const filters = document.getElementById('filters');
        const closeFiltersBtn = document.querySelector('.close-filters');
        const body = document.body;

        if (filterToggleTop) {
            filterToggleTop.addEventListener('click', function() {
                console.log('Filter toggle clicked');
                filters.classList.add('active');
                console.log('Filter active class added:', filters.classList.contains('active'));
                
                // Create or show overlay
                let filterOverlay = document.querySelector('.filter-overlay');
                if (!filterOverlay) {
                    console.log('Creating new filter overlay');
                    filterOverlay = document.createElement('div');
                    filterOverlay.className = 'filter-overlay';
                    document.body.appendChild(filterOverlay);
                    
                    filterOverlay.addEventListener('click', function() {
                        console.log('Filter overlay clicked');
                        filters.classList.remove('active');
                        this.classList.remove('active');
                        body.style.overflow = '';
                    });
                }
                
                console.log('Adding active class to overlay');
                filterOverlay.classList.add('active');
                body.style.overflow = 'hidden';
            });
        }
        
        // Add close button functionality
        if (closeFiltersBtn) {
            closeFiltersBtn.addEventListener('click', function() {
                console.log('Close filters button clicked');
                filters.classList.remove('active');
                const filterOverlay = document.querySelector('.filter-overlay');
                if (filterOverlay) {
                    filterOverlay.classList.remove('active');
                }
                body.style.overflow = '';
            });
        }

        // Initialize lazy loading
        var lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));
        if ('IntersectionObserver' in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove('lazy');
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });
            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        }

        // Wheel Carousel functionality
        const wheelTrack = document.getElementById('topItems');
        const wheelItems = wheelTrack.querySelectorAll('.wheel-item');
        const wheelPrev = document.getElementById('wheelPrev');
        const wheelNext = document.getElementById('wheelNext');
        
        // Continue with the existing JavaScript code...
    });
</script>

<!-- Load jQuery and jQuery UI asynchronously -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" async></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" async></script>
</body>
</html>