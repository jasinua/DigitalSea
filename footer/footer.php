<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<style>
    .footer {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 40px 0;
        text-align: left;
        margin:0;
        background-color: var(--noir-color);
    }

    .footer-column {
        flex: 1;
        margin-left:5%;
    }

    .footer-column h3 {
        color: var(--text-color);
        font-size: 18px;
        margin-bottom: 10px;
    }

    .footer-links {
        display: block;
    }

    .footer-column a, .footer-links li a {
        color: var(--footer-items-color);
        text-decoration: none;
        display: block;
        margin-bottom: 10px;
    }

    .footer-column a:hover, .footer-links li a:hover {
        text-decoration: underline;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        background-color: var(--noir-color);
        margin: 0;
    }

    .social-icons a {
        color: var(--footer-items-color);
        font-size: 25px;
        margin: 0 25px;
        margin-bottom: 1%;
    }

    #facebook:hover {
        transition:ease-out 0.3s;
        color:#1877F2;
    }
    
    #twitter:hover {
        transition:ease-out 0.3s;
        color:#1DA1F2;
    }
    
    #instagram:hover {
        transition:ease-out 0.3s;
        color:#e1306c;
    }
    
    #linkedin:hover {
        transition:ease-out 0.3s;
        color:#0a66c2;
    }

    #youtube:hover {
        transition:ease-out 0.3s;
        color:red;
    }

    .footer-links li {
        list-style: none;
    }

    @media screen and (max-width: 1350px) {
        .footer {
            flex-direction: column !important;
            text-align: center;
            padding: 30px 10px;
        }
        
        .footer > div {
            flex-direction: column !important;
            display: flex !important;
            align-items: stretch !important;
        }
        
        .footer-column {
            flex: 0 0 100%;
            margin-left: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
            max-width: 100vw;
        }
        
        .footer-column:last-child {
            margin-bottom: 5px;
            border-bottom: none;
        }
        
        .footer-column h3 {
            font-size: 1.1rem;
            padding: 10px 25px 10px 0;
            margin-bottom: 0;
            text-align: left;
            cursor: pointer;
            position: relative;
        }
        
        .footer-column h3::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
        }
        
        .footer-column:not(.collapsed) h3::after {
            transform: translateY(-50%) rotate(180deg);
        }
        
        .footer-links {
            padding: 0 10px 10px 10px;
            font-size: 1rem;
            display: none;
        }

        .footer-column:not(.collapsed) .footer-links {
            display: block;
        }

        .footer-column a, .footer-links li a {
            font-size: 1.1rem;
            margin-bottom: 12px;
            padding: 10px 0;
            display: block;
        }
        
        .social-icons {
            padding: 10px 0 15px 0;
            justify-content: center;
            flex-direction: row !important;
            display: flex !important;
        }
        
        .social-icons a {
            font-size: 1.5rem;
            margin: 0 10px;
            padding: 10px;
        }
    }

    @media screen and (min-width: 1351px) {
        .footer-column h3::after {
            display: none;
        }
        .footer-links {
            display: block !important;
        }
    }

    /* Media queries for responsive footer */
    @media screen and (max-width: 992px) {
        .footer {
            flex-wrap: wrap;
            padding: 30px 0;
        }
        
        .footer-column {
            flex: 0 0 50%;
            margin-bottom: 25px;
        }
    }
    
    @media screen and (max-width: 768px) {
        .footer {
            padding: 25px 15px;
        }
        
        .footer-column h3 {
            font-size: 16px;
        }
        
        .footer-column a, .footer-links li a {
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .social-icons a {
            font-size: 22px;
            margin: 0 20px;
        }
    }
    
    @media screen and (max-width: 576px) {
        .footer {
            flex-direction: column;
            text-align: center;
            padding: 20px 10px;
        }
        
        .footer-column {
            flex: 0 0 100%;
            margin-left: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 5px;
        }
        
        .footer-column:last-child {
            margin-bottom: 5px;
            border-bottom: none;
        }
        
        .footer-column h3 {
            position: relative;
            cursor: pointer;
            padding: 10px 0;
            margin-bottom: 0;
        }
        
        .footer-column h3::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 10px;
            transition: transform 0.3s;
        }
        
        .footer-column.active h3::after {
            transform: rotate(180deg);
        }
        
        .footer-links {
            display: none;
            padding: 0 10px 10px 10px;
        }
        
        .footer-column.active .footer-links {
            display: block;
        }
        
        .footer-column a, .footer-links li a {
            font-size: 1.1rem;
            margin-bottom: 12px;
            padding: 10px 0;
            display: block;
        }
        
        .social-icons {
            padding: 10px 0 15px 0;
        }
        
        .social-icons a {
            font-size: 20px;
            margin: 0 15px;
        }
    }
    
    @media screen and (max-width: 400px) {
        .footer-column h3 {
            font-size: 15px;
            padding: 8px 0;
        }
        
        .footer-column a, .footer-links li a {
            font-size: 13px;
            margin-bottom: 6px;
        }
        
        .social-icons a {
            font-size: 18px;
            margin: 0 12px;
        }
        
        .footer {
            padding: 15px 10px;
            display:flex;
            flex-direction: column;
        }
    }
<<<<<<< HEAD

    @media (max-width: 335px) {
        .social-icons {
            width: 100%;
        }
    }

    @media (max-width: 300px) {
        .stock-status {
            display: none;
        }

        .product-details {
            justify-content: center;
        }

        .wishlist-item {
            padding: 10px;
        }

        .product-info img {
            height: 100px;
        }

        .social-icons {
            justify-content: space-between;
        }

        .social-icons a {
            margin: 0;
        }
    }

</style>
<body>
    <div class="footer" <?php if(strpos($_SERVER['REQUEST_URI'], 'login.php') !== false): ?>style="display: none;"<?php endif; ?>>

        <div style="display:flex;">
            <div class="footer-column">
                <h3>Products</h3>
                <div class="footer-links">
                    <?php
                    // Use the same categories structure as in index.php
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
                            'subcategories' => []
                        ],
                        'Gaming & Consoles' => [
                            'keywords' => ['game', 'console', 'gaming', 'controller'],
                            'subcategories' => []
                        ],
                        'Smart Home & IoT' => [
                            'keywords' => ['smart home', 'iot', 'smart device', 'automation', 'tv', 'television'],
                            'subcategories' => []
                        ],
                        'Wearables & Accessories' => [
                            'keywords' => ['watch', 'wearable', 'smartwatch', 'fitness tracker'],
                            'subcategories' => []
                        ]
                    ];
                    
                    // Footer product links to check against valid categories
                    $footer_products = [
                        'Desktop' => ['desktop', 'computer', 'pc'],
                        'Watches' => ['watch', 'watches', 'wearable', 'smart watch'],
                        'Phones' => ['phone', 'phones', 'smartphone', 'smartphones', 'mobile'],
                        'Laptop' => ['laptop', 'laptops', 'notebook', 'notebooks'],
                        'TV' => ['tv', 'television', 'televisions', 'smart tv']
                    ];
                    
                    // Function to check if a product belongs to any category
                    function isValidProduct($product_keywords, $categories) {
                        foreach ($product_keywords as $prod_keyword) {
                            // Get singular form for comparison (simple removal of 's' at the end)
                            $singular_keyword = rtrim($prod_keyword, 's');
                            
                            foreach ($categories as $category => $data) {
                                // Check main category keywords
                                foreach ($data['keywords'] as $cat_keyword) {
                                    // Get singular form of category keyword
                                    $singular_cat_keyword = rtrim($cat_keyword, 's');
                                    
                                    // Check for partial matches with both singular and plural forms
                                    if (stripos($cat_keyword, $singular_keyword) !== false || 
                                        stripos($singular_cat_keyword, $singular_keyword) !== false ||
                                        stripos($prod_keyword, $singular_cat_keyword) !== false) {
                                        return true;
                                    }
                                }
                                
                                // Check subcategory keywords if they exist
                                if (isset($data['subcategories'])) {
                                    foreach ($data['subcategories'] as $subcat => $subcat_keywords) {
                                        foreach ($subcat_keywords as $subcat_keyword) {
                                            // Get singular form of subcategory keyword
                                            $singular_subcat_keyword = rtrim($subcat_keyword, 's');
                                            
                                            // Check for partial matches with both singular and plural forms
                                            if (stripos($subcat_keyword, $singular_keyword) !== false || 
                                                stripos($singular_subcat_keyword, $singular_keyword) !== false ||
                                                stripos($prod_keyword, $singular_subcat_keyword) !== false) {
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return false;
                    }
                    
                    // Always include these core products regardless of filter matching
                    $core_products = ['Desktop', 'Watches', 'Phones', 'Laptop', 'TV'];
                    
                    // Display product links - always show core products, filter others
                    foreach ($footer_products as $product => $keywords) {
                        if (in_array($product, $core_products) || isValidProduct($keywords, $categories)) {
                            // Find the matching category for linking purposes
                            $category_param = '';
                            $subcategory_param = '';
                            
                            foreach ($categories as $category => $data) {
                                foreach ($keywords as $keyword) {
                                    if (in_array($keyword, $data['keywords'])) {
                                        $category_param = $category;
                                        break 2;
                                    }
                                    
                                    // Also check subcategories
                                    if (isset($data['subcategories'])) {
                                        foreach ($data['subcategories'] as $subcat => $subcat_keywords) {
                                            foreach ($subcat_keywords as $subcat_keyword) {
                                                if (stripos($subcat_keyword, $keyword) !== false || 
                                                    stripos($keyword, $subcat_keyword) !== false) {
                                                    $category_param = $category;
                                                    $subcategory_param = $subcat;
                                                    break 3;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            
                            // Build link URL with appropriate filters
                            $link_url = 'index.php';
                            $params = [];
                            
                            if (!empty($subcategory_param)) {
                                $params[] = 'subfilter[]=' . urlencode($subcategory_param);
                            } else if (!empty($category_param)) {
                                // If no specific subcategory, use the product name as search
                                // Use singular form for searches
                                $search_term = $product;
                                
                                // Use specific search terms for certain products
                                $custom_search_terms = [
                                    'Watches' => 'watch',
                                    'Phones' => 'phone',
                                    'Laptops' => 'laptop',
                                    'TVs' => 'tv'
                                ];
                                
                                if (isset($custom_search_terms[$product])) {
                                    $search_term = $custom_search_terms[$product];
                                } else {
                                    // Convert to singular if it ends with 's'
                                    if (substr($search_term, -1) === 's') {
                                        $search_term = substr($search_term, 0, -1);
                                    }
                                }
                                
                                $params[] = 'search=' . urlencode($search_term);
                            }
                            
                            if (!empty($params)) {
                                $link_url .= '?' . implode('&', $params);
                            }
                            
                            echo '<a href="' . $link_url . '">' . $product . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="footer-column">
                <h3>Resources</h3>
                <div class="footer-links">
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </div>
            </div>
            <div class="footer-column">
                <h3>Work with DigitalSea</h3>
                <div class="footer-links">
                    <a href="#">Partners</a>
                    <a href="#">Dealers</a>
                    <a href="#">OEM</a>
                </div>
            </div>
            <div class="footer-column">
                <h3>About</h3>
                <div class="footer-links">
                    <a href="index.php">DigitalSea, Inc.</a>
                    <a href="developers.php">Developers</a>
                    <a href="#">Team</a>
                </div>
            </div>
        </div>
    
        <div class="social-icons" style="display:flex; flex-direction:row !important;">
            <a href="#">
                <i id="facebook" class="fab fa-facebook-f"></i>
            </a>
            <a href="#">
                <i id="twitter" class="fab fa-twitter"></i>
            </a>
            <a href="#">
                <i id="instagram" class="fab fa-instagram"></i>
            </a>
            <a href="#">
                <i id='linkedin' class="fab fa-linkedin-in"></i>
            </a>
            <a href="#">
                <i id='youtube' class="fab fa-youtube"></i>
            </a>
        </div>
    </div>
    <script>
    // Add accordion functionality for mobile footer
    document.addEventListener('DOMContentLoaded', function() {
        const isSmallScreen = window.innerWidth <= 1350;
        const footerColumns = document.querySelectorAll('.footer-column');
        
        if (isSmallScreen) {
            footerColumns.forEach(column => {
                column.classList.add('collapsed');
                const heading = column.querySelector('h3');
                
                heading.addEventListener('click', () => {
                    column.classList.toggle('collapsed');
                });
            });
        }

        window.addEventListener('resize', () => {
            const isSmallScreen = window.innerWidth <= 1350;
            footerColumns.forEach(column => {
                if (isSmallScreen) {
                    column.classList.add('collapsed');
                } else {
                    column.classList.remove('collapsed');
                }
            });
=======
        if (window.innerWidth <= 576) {
            const footerHeadings = document.querySelectorAll('.footer-column h3');
            
            footerHeadings.forEach(heading => {
                heading.addEventListener('click', function() {
                    const parent = this.parentElement;
                    parent.classList.toggle('active');
                });
            });
        }
        
        // Add resize listener to handle accordion behavior
        window.addEventListener('resize', function() {
            const footerColumns = document.querySelectorAll('.footer-column');
            const footerLinks = document.querySelectorAll('.footer-links');
            
            if (window.innerWidth <= 576) {
                footerColumns.forEach(column => {
                    const heading = column.querySelector('h3');
                    if (!heading.hasAttribute('listener')) {
                        heading.setAttribute('listener', 'true');
                        heading.addEventListener('click', function() {
                            column.classList.toggle('active');
                        });
                    }
                });
                
                // Hide links by default on mobile
                footerLinks.forEach(link => {
                    link.style.display = 'none';
                });
            } else {
                // Show all links on larger screens
                footerLinks.forEach(link => {
                    link.style.display = 'block';
                });
                
                // Remove active class from all columns
                footerColumns.forEach(column => {
                    column.classList.remove('active');
                });
            }
>>>>>>> parent of 95e045d (developer css fixed and footer for small media fixed)
        });
    });
    </script>
</body>
</html>