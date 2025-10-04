<?php
remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
add_action('woocommerce_review_order_before_payment', 'woocommerce_checkout_coupon_form');
add_action('woocommerce_after_order_itemmeta', 'display_sub_products_in_admin_order', 10, 3);
function display_sub_products_in_admin_order($item_id, $item, $product)
{
    $meta_data = $item->get_meta_data();
}
add_action('woocommerce_checkout_create_order_line_item', 'add_selected_sub_products_to_order_item', 10, 4);
function add_selected_sub_products_to_order_item($item, $cart_item_key, $values, $order)
{
    if (!empty($values['add_on_id'])) {
        $addon_product = wc_get_product($values['add_on_id']);
        $item->add_meta_data('Add on', $addon_product->get_name(), true);
    }
    if (!empty($values['additional_adult_select'])) {
        $item->add_meta_data('Additonal Adult', $values['additional_adult_select'], true);
    }
}
