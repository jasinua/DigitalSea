<?php 
    session_start();
    require_once 'model/dbh.inc.php';
    require_once 'controller/home.inc.php';

    if(isset($_SESSION['redirect_back']) && $_SESSION['redirect_back'] == true && isset($_SESSION['user_id'])){
        header("Location: " . $_SESSION['last_page']);
        unset($_SESSION['last_page']);
        unset($_SESSION['redirect_back']);
    }

    // Helper function to get image source
    // function getImageSource($product_id, $image_url) {
    //     $local_image = "images/product_$product_id.png";
    //     return file_exists($local_image) ? $local_image : htmlspecialchars($image_url);
    // }

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
                    
                    <div class="filter-section collapsed">
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
                        <div class="filter-content" style="border-left: none;">
                            <div class="price-range">
                                <input type="number" name="min_price" placeholder="Min €" min="0" step="0.01">
                                <span>-</span>
                                <input type="number" name="max_price" placeholder="Max €" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section collapsed">
                        <h3>Discounts</h3>
                        <div class="filter-content" style="border-left: none;">
                            <div class="filter">
                                <input type="checkbox" name="discounted_only" id="discounted_only" value="1">
                                <label for="discounted_only">Show only items on sale</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id='items'>
                <!-- Discount circle always rendered, fixed position -->
                <!-- <a href="index.php?discounted_only=1" class="discount-circle">
                    <i class="fas fa-percent"></i>
                </a> -->
                <button class="filter-toggle-top"><i class="fas fa-filter"></i></button>
                <?php if(!isset($_GET['subfilter']) && !isset($_GET['min_price']) && !isset($_GET['max_price']) && !isset($_GET['discounted_only']) && !isset($_GET['search'])) { ?>
                    <div class="background-gradient">
                        <h1 id='topItemsHeader'>Top Products</h1>
                        <div class="carousel-container">
                            <button class="carousel-arrow" id="wheelPrev">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class='wheel-carousel'>
                                <div class='wheel-track' id='topItems'>
                                    <?php foreach (getData("SELECT *,
                                            CASE 
                                                WHEN discount > 0 THEN price * (1 - discount/100)
                                                ELSE price 
                                            END as final_price 
                                            FROM products 
                                            WHERE CASE 
                                                WHEN discount > 0 THEN price * (1 - discount/100)
                                                ELSE price END>1100 LIMIT 10") as $prod) { ?>
                                    <div class='wheel-item'>
                                        <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                            <?php if ($prod['discount'] > 0) { ?>
                                                <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                            <?php } ?>
                                            <img src="<?php echo getImageSource($prod['product_id'], $prod['image_url']); ?>" 
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
                    </div>

                    <h1 id='newItemsHeader'>New Products</h1>
                    <div class="new-items-carousel">
                        <button class="carousel-arrow" id="newItemsPrev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class='itemLine' id='newItems'>
                            <?php foreach (getData("SELECT * FROM products ORDER BY product_id DESC LIMIT 8") as $prod) { ?>
                                <div class='item newItemsItem' id="newItemsItem">
                                    <a href="product.php?product=<?php echo $prod['product_id'] ?>" class="product-link">
                                    <div class="new-badge">NEW</div>
                                    <?php if ($prod['discount'] > 0) { ?>
                                        <div class="discount-badge">-<?php echo $prod['discount'] ?>%</div>
                                    <?php } ?>
                                    <img src="<?php echo getImageSource($prod['product_id'], $prod['image_url']); ?>" 
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
                        <h1 style="margin: 40px 0 0 50px; color: var(--noir-color); text-align: center;"><?php echo isset($_GET['search']) ? 'Search Results' : 'Filtered Products'; ?></h1>
                    <?php } ?>
                    <div class='itemBox' id='randomItems'>
                    <?php 
                        if(isset($_GET['subfilter']) || isset($_GET['min_price']) || isset($_GET['max_price']) || isset($_GET['discounted_only']) || isset($_GET['search'])) {
                            $where_conditions = [];
                            
                            if (!empty($_GET['subfilter'])) {
                                $subfilter_conditions = [];
                                foreach ($_GET['subfilter'] as $subcat) {
                                    $subfilter_conditions[] = "(LOWER(description) LIKE LOWER('%" . substr($subcat, 0, -1) . "%') OR LOWER(name) LIKE LOWER('%" . substr($subcat, 0, -1) . "%'))";
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
                                function normalizeSearchTerm($term) {
                                    $term = strtolower(trim($term));
                                    $mappings = [
                                        'watches' => 'watch', 'watch' => 'watch',
                                        'phones' => 'phone', 'phone' => 'phone',
                                        'laptops' => 'laptop', 'laptop' => 'laptop',
                                        'computers' => 'computer', 'computer' => 'computer',
                                        'televisions' => 'television', 'television' => 'television',
                                        'tv' => 'tv', 'tvs' => 'tv',
                                        'headphones' => 'headphone', 'headphone' => 'headphone'
                                    ];
                                    if (isset($mappings[$term])) {
                                        return $mappings[$term];
                                    }
                                    if (substr($term, -1) === 's') {
                                        return substr($term, 0, -1);
                                    }
                                    return $term;
                                }
                                
                                $original_search_term = mysqli_real_escape_string($conn, $_GET['search']);
                                $normalized_term = normalizeSearchTerm($original_search_term);
                                
                                $search_condition = "(LOWER(description) LIKE LOWER('%$original_search_term%') OR 
                                                    LOWER(name) LIKE LOWER('%$original_search_term%')";
                                if ($normalized_term !== strtolower($original_search_term)) {
                                    $search_condition .= " OR LOWER(description) LIKE LOWER('%$normalized_term%') OR 
                                                         LOWER(name) LIKE LOWER('%$normalized_term%')";
                                }
                                $plural_term = $normalized_term . 's';
                                $search_condition .= " OR LOWER(description) LIKE LOWER('%$plural_term%') OR 
                                                     LOWER(name) LIKE LOWER('%$plural_term%')";
                                $search_condition .= ")";
                                $where_conditions[] = $search_condition;
                            }
                            
                            $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
                            $count_query = "SELECT COUNT(*) as total FROM products $where_clause";
                            $count_result = $conn->query($count_query);
                            $total_products = $count_result->fetch_assoc()['total'];
                            $total_pages = ceil($total_products / $items_per_page);
                            
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
                                    <img src="<?php echo getImageSource($prod['product_id'], $prod['image_url']); ?>" 
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

                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <div class="pagination-container">
                                <?php 
                                $query_params = [];
                                if (isset($_GET['search'])) {
                                    $query_params['search'] = $_GET['search'];
                                }
                                if (isset($_GET['subfilter'])) {
                                    $query_params['subfilter'] = $_GET['subfilter'];
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
                                ?>
                                
                                <?php if ($current_page > 1): ?>
                                    <a href="#" class="pagination-btn" data-page="<?php echo $current_page - 1; ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                <?php endif; ?>

                                <div class="page-numbers">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <a href="#" 
                                           class="page-number <?php echo $i === $current_page ? 'active' : ''; ?>" 
                                           data-page="<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($current_page < $total_pages): ?>
                                    <a href="#" class="pagination-btn" data-page="<?php echo $current_page + 1; ?>">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clear Filters
            window.clearAllFilters = function() {
                window.location.href = 'index.php';
            };

            // New Items Carousel
            const newItems = document.getElementById('newItems');
            const newItemsPrev = document.getElementById('newItemsPrev');
            const newItemsNext = document.getElementById('newItemsNext');
            let currentNewItemsIndex = 0;
            let itemWidth = 330;
            let itemMargin = 20;
            let visibleNewItems = 4;

            function updateVisibleItems() {
                if (window.innerWidth <= 1150) {
                    visibleNewItems = 3;
                    itemWidth = 220;
                    itemMargin = -10;
                } else if (window.innerWidth <= 1550) {
                    visibleNewItems = 3;
                    itemWidth = 330;
                    itemMargin = 10;
                } else {
                    visibleNewItems = 4;
                    itemWidth = 330;
                    itemMargin = 20;
                }
                const scrollAmount = itemWidth + itemMargin;

                if (newItems) {
                    const items = newItems.querySelectorAll('.newItemsItem');
                    newItems.style.width = `${(itemWidth + itemMargin) * items.length}px`;
                    items.forEach(item => {
                        item.style.width = `${itemWidth}px`;
                        item.style.marginRight = `${itemMargin}px`;
                    });
                    // Reset scroll position to align with current index
                    newItems.scrollTo({
                        left: currentNewItemsIndex * scrollAmount,
                        behavior: 'auto'
                    });
                }
                if (newItems && newItemsPrev && newItemsNext) {
                    updateNewItemsArrows();
                }
            }

            function updateNewItemsArrows() {
                if (newItems && newItemsPrev && newItemsNext) {
                    const items = newItems.querySelectorAll('.newItemsItem');
                    newItemsPrev.disabled = currentNewItemsIndex <= 0;
                    newItemsNext.disabled = currentNewItemsIndex >= items.length - visibleNewItems;
                }
            }

            if (newItems && newItemsPrev && newItemsNext) {
                newItemsPrev.addEventListener('click', () => {
                    if (currentNewItemsIndex > 0) {
                        currentNewItemsIndex--;
                        newItems.scrollTo({
                            left: currentNewItemsIndex * (itemWidth + itemMargin),
                            behavior: 'smooth'
                        });
                        updateNewItemsArrows();
                    }
                });

                newItemsNext.addEventListener('click', () => {
                    const items = newItems.querySelectorAll('.newItemsItem');
                    if (currentNewItemsIndex < items.length - visibleNewItems) {
                        currentNewItemsIndex++;
                        newItems.scrollTo({
                            left: currentNewItemsIndex * (itemWidth + itemMargin),
                            behavior: 'smooth'
                        });
                        updateNewItemsArrows();
                    }
                });

                newItems.addEventListener('scroll', () => {
                    if (!newItems) return;
                    const scrollPosition = newItems.scrollLeft;
                    const scrollAmount = itemWidth + itemMargin;
                    currentNewItemsIndex = Math.min(
                        Math.max(0, Math.round(scrollPosition / scrollAmount)),
                        newItems.querySelectorAll('.newItemsItem').length - visibleNewItems
                    );
                    updateNewItemsArrows();
                });

                window.addEventListener('resize', updateVisibleItems);
                updateVisibleItems();
            }

            // Wheel Carousel
            const wheelTrack = document.getElementById('topItems');
            const wheelItems = wheelTrack ? wheelTrack.querySelectorAll('.wheel-item') : [];
            const wheelPrev = document.getElementById('wheelPrev');
            const wheelNext = document.getElementById('wheelNext');
            let currentWheelIndex = wheelItems.length ? Math.floor(wheelItems.length / 2) : 0;

            function updateWheelCarousel() {
                if (!wheelItems.length || !wheelPrev || !wheelNext) return;
                wheelItems.forEach((item, index) => {
                    item.classList.remove('active', 'left', 'right', 'hidden');
                    if (currentWheelIndex === 0) {
                        if (index === 0) item.classList.add('active');
                        else if (index === 1) item.classList.add('right');
                        else item.classList.add('hidden');
                    } else if (currentWheelIndex === wheelItems.length - 1) {
                        if (index === wheelItems.length - 1) item.classList.add('active');
                        else if (index === wheelItems.length - 2) item.classList.add('left');
                        else item.classList.add('hidden');
                    } else {
                        const relativePos = (index - currentWheelIndex + wheelItems.length) % wheelItems.length;
                        if (relativePos === 0) item.classList.add('active');
                        else if (relativePos === wheelItems.length - 1) item.classList.add('left');
                        else if (relativePos === 1) item.classList.add('right');
                        else item.classList.add('hidden');
                    }
                });
                wheelPrev.style.opacity = currentWheelIndex === 0 ? '0' : '1';
                wheelPrev.style.cursor = currentWheelIndex === 0 ? 'default' : 'pointer';
                wheelNext.style.opacity = currentWheelIndex >= wheelItems.length - 1 ? '0' : '1';
                wheelNext.style.cursor = currentWheelIndex >= wheelItems.length - 1 ? 'default' : 'pointer';
            }

            if (wheelTrack && wheelPrev && wheelNext && wheelItems.length > 0) {
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
                updateWheelCarousel();
            }

            // Keyboard Navigation
            if (newItems || wheelTrack) {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        if (document.activeElement === wheelTrack && wheelPrev) wheelPrev.click();
                        else if (document.activeElement === newItems && newItemsPrev) newItemsPrev.click();
                    } else if (e.key === 'ArrowRight') {
                        if (document.activeElement === wheelTrack && wheelNext) wheelNext.click();
                        else if (document.activeElement === newItems && newItemsNext) newItemsNext.click();
                    }
                });
            }

            if (wheelTrack) wheelTrack.setAttribute('tabindex', '0');
            if (newItems) newItems.setAttribute('tabindex', '0');

            // Filter Functionality
            const categoryHeaders = document.querySelectorAll('.category-header');
            categoryHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    if (content) {
                        header.classList.toggle('collapsed');
                        content.style.display = content.style.display === 'block' ? 'none' : 'block';
                    }
                });
            });

            const filterSections = document.querySelectorAll('.filter-section h3');
            filterSections.forEach(header => {
                header.addEventListener('click', () => {
                    const section = header.parentElement;
                    const content = header.nextElementSibling;
                    if (section && content) {
                        section.classList.toggle('collapsed');
                        content.style.display = content.style.display === 'block' ? 'none' : 'block';
                    }
                });
            });

            // Filter Toggle
            const filterToggleTop = document.querySelector('.filter-toggle-top');
            const filters = document.getElementById('filters');
            const closeFiltersBtn = document.querySelector('.close-filters');
            const body = document.body;

            if (filterToggleTop) {
                filterToggleTop.addEventListener('click', () => {
                    if (filters) filters.classList.add('active');
                    let filterOverlay = document.querySelector('.filter-overlay');
                    if (!filterOverlay) {
                        filterOverlay = document.createElement('div');
                        filterOverlay.className = 'filter-overlay';
                        document.body.appendChild(filterOverlay);
                        filterOverlay.addEventListener('click', () => {
                            if (filters) filters.classList.remove('active');
                            filterOverlay.classList.remove('active');
                            body.style.overflow = '';
                        });
                    }
                    filterOverlay.classList.add('active');
                    body.style.overflow = 'hidden';
                });
            }

            if (closeFiltersBtn) {
                closeFiltersBtn.addEventListener('click', () => {
                    if (filters) filters.classList.remove('active');
                    const filterOverlay = document.querySelector('.filter-overlay');
                    if (filterOverlay) filterOverlay.classList.remove('active');
                    body.style.overflow = '';
                });
            }

            // Wishlist Functionality
            function updateAllProductHearts(productId, isActive) {
                const heartButtons = document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"]`);
                heartButtons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (icon) {
                        btn.classList.toggle('active', isActive);
                        icon.classList.toggle('far', !isActive);
                        icon.classList.toggle('fas', isActive);
                    }
                });
            }

            const wishlistButtons = document.querySelectorAll('.wishlist-btn');
            wishlistButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const productId = btn.dataset.productId;
                    fetch('controller/add_to_wishlist.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `product_id=${productId}`
                    })
                    .then(response => response.text())
                    .then(result => {
                        result = result.trim();
                        if (result === 'not_logged_in') {
                            window.location.href = 'login.php';
                        } else if (result === 'added') {
                            updateAllProductHearts(productId, true);
                        } else if (result === 'removed') {
                            updateAllProductHearts(productId, false);
                        } else if (result === 'error') {
                            console.error('Error updating wishlist');
                            alert('Error updating wishlist. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating wishlist. Please try again.');
                    });
                });
            });

            wishlistButtons.forEach(btn => {
                const productId = btn.dataset.productId;
                fetch('controller/check_wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}`
                })
                .then(response => response.text())
                .then(result => {
                    if (result.trim() === 'true') {
                        updateAllProductHearts(productId, true);
                    }
                })
                .catch(error => console.error('Error checking wishlist status:', error));
            });

            // Lazy Loading Images
            const lazyImages = document.querySelectorAll('img.lazy');
            if ('IntersectionObserver' in window) {
                const lazyImageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove('lazy');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });
                lazyImages.forEach(lazyImage => lazyImageObserver.observe(lazyImage));
            }

            // Pagination
            function loadProducts(page) {
                const randomItems = document.getElementById('randomItems');
                const paginationContainer = document.querySelector('.pagination-container');
                const queryParams = new URLSearchParams(window.location.search);
                queryParams.set('page', page);

                fetch('controller/fetch_products.php?' + queryParams.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    randomItems.innerHTML = '';
                    data.products.forEach(prod => {
                        const isInWishlist = <?php echo json_encode($wishlist_items); ?>.includes(prod.product_id.toString());
                        let priceHtml = prod.discount > 0
                            ? `<span class="original-price">${parseFloat(prod.price).toFixed(2).replace('.', ',')}€</span>
                            <span class="discounted-price">${(prod.price * (1 - prod.discount / 100)).toFixed(2).replace('.', ',')}€</span>`
                            : `<span class="discounted-price">${parseFloat(prod.price).toFixed(2).replace('.', ',')}€</span>`;

                        const productHtml = `
                            <div class='item'>
                                <a href="product.php?product=${prod.product_id}" class="product-link">
                                    ${prod.discount > 0 ? `<div class="discount-badge">-${prod.discount}%</div>` : ''}
                                    <img src="${prod.image_src}" 
                                        alt="${prod.description}"
                                        width="225"
                                        height="180">
                                    <div class='title'>${prod.description}</div>
                                    <div class='bottom-container'>
                                        <button class="wishlist-btn ${isInWishlist ? 'active' : ''}" data-product-id="${prod.product_id}">
                                            <i class="${isInWishlist ? 'fas' : 'far'} fa-heart"></i>
                                        </button>
                                        <div class='price'>${priceHtml}</div>
                                    </div>
                                </a>
                            </div>
                        `;
                        randomItems.insertAdjacentHTML('beforeend', productHtml);
                    });

                    paginationContainer.innerHTML = data.pagination;

                    const newPaginationLinks = document.querySelectorAll('.pagination-btn, .page-number');
                    newPaginationLinks.forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            loadProducts(link.getAttribute('data-page'));
                        });
                    });

                    const newWishlistButtons = document.querySelectorAll('.wishlist-btn');
                    newWishlistButtons.forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const productId = btn.dataset.productId;
                            fetch('controller/add_to_wishlist.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `product_id=${productId}`
                            })
                            .then(response => response.text())
                            .then(result => {
                                result = result.trim();
                                if (result === 'not_logged_in') {
                                    window.location.href = 'login.php';
                                } else if (result === 'added') {
                                    updateAllProductHearts(productId, true);
                                } else if (result === 'removed') {
                                    updateAllProductHearts(productId, false);
                                } else if (result === 'error') {
                                    console.error('Error updating wishlist');
                                    alert('Error updating wishlist. Please try again.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error updating wishlist. Please try again.');
                            });
                        });
                    });

                    queryParams.set('page', page);
                    window.history.pushState({}, '', '?' + queryParams.toString());
                    randomItems.scrollIntoView({ behavior: 'smooth' });
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    alert('Error loading products. Please try again.');
                });
            }

            const paginationLinks = document.querySelectorAll('.pagination-btn, .page-number');
            paginationLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    loadProducts(link.getAttribute('data-page'));
                });
            });

            // Discount Circle
            const discountCircle = document.querySelector('.discount-circle');
            if (discountCircle && new URLSearchParams(window.location.search).get('discounted_only') === '1') {
                discountCircle.classList.add('active');
            }

            // Background and Filter Toggle Scroll Effects
            const container = document.getElementById('container');
            const newItemsHeader = document.getElementById('newItemsHeader');

            window.addEventListener('load', () => {
                if (container) {
                    container.style.backgroundColor = window.location.search.includes('subfilter') || 
                        window.location.search.includes('search') || 
                        window.location.search.includes('min_price') || 
                        window.location.search.includes('max_price') || 
                        window.location.search.includes('discounted_only') 
                        ? 'white' : 'var(--noir-color)';
                }
            });

            window.addEventListener('scroll', () => {
                if (container) {
                    // Skip background change on filtered pages
                    if (window.location.search.includes('subfilter') || 
                        window.location.search.includes('search') || 
                        window.location.search.includes('min_price') || 
                        window.location.search.includes('max_price') || 
                        window.location.search.includes('discounted_only')) {
                        container.style.backgroundColor = 'white';
                    } else {
                        const containerTop = container.getBoundingClientRect().top;
                        container.style.backgroundColor = containerTop <= 0 ? 'white' : 'var(--noir-color)';
                    }
                }

                if (filterToggleTop && newItemsHeader) {
                    const headerPosition = newItemsHeader.getBoundingClientRect().top;
                    filterToggleTop.style.backgroundColor = headerPosition > 100 ? 'white' : 'var(--noir-color)';
                    filterToggleTop.style.color = headerPosition > 100 ? 'var(--noir-color)' : 'white';
                }
            });
        });
    </script>
</body>
</html>