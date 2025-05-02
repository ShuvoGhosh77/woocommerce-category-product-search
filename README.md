# WooCommerce AJAX Product Search by Category (Shortcode-Based)

This WordPress Shortcode allows you to add a custom AJAX-powered product search bar that filters WooCommerce products based on category. You can place the search bar using a simple shortcode and specify the product category as an attribute.

## 🔍 Features

- Custom shortcode: `[category_product_search category="your-category-slug"]`
- Search bar that filters WooCommerce products by keyword within a specific category
- AJAX-based results (no page reload)
- Lightweight and easy to customize
- Can be added anywhere (page, post, or widget)

## 📦 Installation

1. Clone or download this repository into your WordPress function.php:

## ⚙️ Usage
1. Pages or Posts
2. Elementor or other page builders (using shortcode blocks)

## 🛠️ How It Works
1 The shortcode outputs a search input field.
2 When the user types a query and submits the form, it sends an AJAX request.
3 The backend processes the request, filters products in the specified category, and returns matching results without reloading the page.
4 Results are dynamically displayed below the search bar.
