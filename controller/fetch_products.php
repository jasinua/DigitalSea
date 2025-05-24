<?php
session_start();
require_once '../model/dbh.inc.php';
require_once '../controller/home.inc.php';

// Helper function to get image source
function getImageSource($product_id, $image_url) {
    $local_image = "../images/product_$product_id.png"; // Relative to fetch_products.php
    return file_exists($local_image) ? "images/product_$product_id.png" : htmlspecialchars($image_url);
}

$items_per_page = 18;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Initialize categories array (same as in index.php)
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

// Build query
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

// Get total count
$count_query = "SELECT COUNT(*) as total FROM products $where_clause";
$count_result = $conn->query($count_query);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $items_per_page);

// Get products
$products = getData("SELECT * FROM products $where_clause LIMIT $items_per_page OFFSET $offset");

// Prepare products array with resolved image source
$products_array = [];
foreach ($products as $prod) {
    $products_array[] = [
        'product_id' => $prod['product_id'],
        'description' => $prod['description'],
        'price' => $prod['price'],
        'discount' => $prod['discount'],
        'image_src' => getImageSource($prod['product_id'], $prod['image_url']) // Resolved image source
    ];
}

// Build pagination HTML
$pagination_html = '';
if ($total_pages > 1) {
    if ($current_page > 1) {
        $pagination_html .= '<a href="#" class="pagination-btn" id="prevBtn" data-page="' . ($current_page - 1) . '"><i class="fas fa-chevron-left"></i><span>Previous</span></a>';
    }
    $pagination_html .= '<div class="page-numbers">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $pagination_html .= '<a href="#" class="page-number ' . ($i === $current_page ? 'active' : '') . '" data-page="' . $i . '">' . $i . '</a>';
    }
    $pagination_html .= '</div>';
    if ($current_page < $total_pages) {
        $pagination_html .= '<a href="#" class="pagination-btn" id="nextBtn" data-page="' . ($current_page + 1) . '"><span>Next</span><i class="fas fa-chevron-right"></i></a>';
    }
    $pagination_html .= '</div>';
}

// Prepare response
$response = [
    'products' => $products_array,
    'pagination' => $pagination_html
];

header('Content-Type: application/json');
echo json_encode($response);
?>