<?php
add_action('woocommerce_before_add_to_cart_button', 'zippy_custom_function_before_add_to_cart');
function zippy_custom_function_before_add_to_cart()
{
    $html = '';
    $args = array(
        'limit' => -1,
        'status' => 'publish',
        'category' => 'add-ons',
    );
    $add_ons_products = wc_get_products($args);
    if (empty($add_ons_products)) return;
    foreach ($add_ons_products as $product) {
        $html .=  '<option value="' . esc_attr($product->get_id()) . '">'
            . esc_html($product->get_name()) . '</option>';
    }
    $add_ons_html = "<p>Add-Ons</p>
    <div class='add-ons-select-block'>
        <select class='add-ons-package'>
            <option selected>Please choose item</option>;
        $html
 </select>
    </div>";
    echo $add_ons_html;
}
