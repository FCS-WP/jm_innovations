<?php
add_action('woocommerce_before_add_to_cart_button', 'zippy_custom_before_add_to_cart');
function zippy_custom_before_add_to_cart()
{
    global $product;
    $current_product_id = $product->get_id();
    //Get All Add ons Information
    $add_ons_features = get_field('add_ons');
    $all_add_ons = array();
    // Get all Additional Adult 
    $additional_adults = get_field('additional_adult');
    if (!empty($add_ons_features)) {
        foreach ($add_ons_features as $add_ons_items) {
            foreach ($add_ons_items as $items) {
                foreach ($items as $item) {
                    $add_on_product = wc_get_product($item->ID);
                    $add_ons_feature_list = array(
                        'name' => $add_on_product->get_name(),
                        'id' => $add_on_product->get_id(),
                        'price' => $add_on_product->get_regular_price()
                    );
                    $all_add_ons[] = $add_ons_feature_list;
                }
            }
        }
?>
        <div style="display: block; width: 100%;" class="add-ons-selection-block">
            <h2 class="add-ons-title">ADD ONS</h2>
            <select class="form-select" name="add_on_select" id="add_on_select" aria-label="Add-ons select">
                <option selected value="">Choose your option</option>
                <?php foreach ($all_add_ons as $add_on) : ?>
                    <option value="<?php echo esc_attr($add_on['id']); ?>"
                        data-id="<?php echo esc_attr($add_on['id']); ?>"
                        data-name="<?php echo esc_attr($add_on['name']); ?>"
                        data-price="<?php echo esc_attr($add_on['price']); ?>">
                        <?php echo esc_html($add_on['name']) . ' - ' . wc_price($add_on['price']); ?>
                    </option>
            <?php endforeach;
            } else {
                echo '';
            }
            ?>
            <div class="add-ons-price-block">
                <p class="add-ons-price-value"></p>
            </div>
            </select>
            <div class="additonal-adult-block form-check">
                <h2 class="additional-adult-tile">ADDITIONAL ADULT</h2>
                <div class="check-form">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Additional Adult
                        </label>
                        <?php
                        if (!empty($additional_adults)) { ?>
                            <select name="additional_adult_select" id="additional_adult_select" style="pointer-events: none;" class="additinal-adult-select-block form-select" aria-label="Default select example">
                                <option selected value="">Choose the quantity of additional person</option>
                                <?php foreach ($additional_adults as $item) : ?>
                                    <option value="<?php echo esc_attr($item['quantity']) ?>" data-price="<?php echo esc_attr($item['price']) ?>">
                                        <?php echo esc_html($item['quantity']) ?>
                                    </option>
                            <?php endforeach;
                            } else {
                                echo '';
                            }
                            ?>
                            </select>
                    </div>
                </div>
            </div>
        </div>
    <?php

}
add_filter('woocommerce_add_cart_item_data', 'custom_add_to_cart', 10, 2);
function custom_add_to_cart($cart_item_data)
{
    if (!empty($_POST['add_on_select'])) {
        $cart_item_data['add_on_id'] = intval($_POST['add_on_select']);
    }
    if (!empty($_POST['additional_adult_select'])) {
        $cart_item_data['additional_adult_select'] = intval($_POST['additional_adult_select']);
    }
    return $cart_item_data;
}
add_filter('woocommerce_get_item_data', 'custom_cart_item_data', 10, 2);
function custom_cart_item_data($item_data, $cart_item)
{
    $additional_adults = get_field('additional_adult', $cart_item['product_id']);
    $additional_price = 0;
    if (!empty($cart_item['add_on_id'])) {
        $addon_product = wc_get_product($cart_item['add_on_id']);
        $item_data[] = array(
            'name'  => __('Add-On', 'woocommerce'),
            'value' => $addon_product ? $addon_product->get_name() . 'x' . wc_price($addon_product->get_price()) : 'N/A',
        );
    }
    if (!empty($cart_item['additional_adult_select'])) {
        if (!empty($additional_adults)) {
            foreach ($additional_adults as $row) {
                if ($row['quantity'] == $cart_item['additional_adult_select']) {
                    $additional_price =  (int)$row['price'];
                    break;
                }
            }
        }
        $item_data[] = array(
            'name' => __('Adults', 'woocommerce'),
            'value' =>  wp_kses_post($cart_item['additional_adult_select'] . 'x' . wc_price((int)$additional_price)),
        );
    }
    return $item_data;
}
add_filter('woocommerce_widget_cart_item_quantity', 'custom_woocommerce_widget_cart_item_quantity', 10, 3);
function custom_woocommerce_widget_cart_item_quantity($html, $cart_item, $cart_item_key)
{
    $additional_adults = get_field('additional_adult', $cart_item['product_id']);
    $html = '';
    $new_price = 0;
    if (!empty($cart_item['add_on_id']) || !empty($cart_item['additional_adult_select'])) {
        $add_on_item = wc_get_product($cart_item['add_on_id']);
        if ($add_on_item) {
            $new_price += (float)($cart_item['data']->get_price()  + $add_on_item->get_price());
        }
        foreach ($additional_adults as $row) {
            if ($row['quantity'] == $cart_item['additional_adult_select']) {
                $additional_price =  (int)$row['price'];
                break;
            }
        }
        $new_price += $additional_price;
        $quantity = $cart_item['quantity'];
        $standard_price = wc_price($new_price);
        $html = "<span>$standard_price * $quantity</span>";
    } else {
        $intial_price = wc_price(($cart_item['data'])->get_price());
        $intial_quantity = (float)($cart_item['quantity']);
        $html = "<span>$intial_price * $intial_quantity</span>";
    }
    return $html;
}
add_action('woocommerce_before_calculate_totals', 'caculate_total_cart_fee');
function caculate_total_cart_fee($cart)
{
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $additional_adults = get_field('additional_adult', $cart_item['product_id']);
        $add_on_item = (!empty($cart_item['add_on_id'])) ? wc_get_product($cart_item['add_on_id']) : 0;
        $add_on_item_price = ($add_on_item) ? $add_on_item->get_price() : 0;
        if (!empty($cart_item['product_id'])) {
            foreach ($additional_adults as $row) {
                if ($row['quantity'] == $cart_item['additional_adult_select']) {
                    $additional_price =  (int)$row['price'];
                    break;
                }
            }
        }
        if ($add_on_item || $additional_price) {
            $cart_item['data']->set_price(
                $cart_item['data']->get_price() + $add_on_item_price + $additional_price
            );
        }
    }
}
