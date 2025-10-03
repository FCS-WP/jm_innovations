$("#shop-block").find(".button").html("View Detail");
$(".form-select");
jQuery(document).ready(function ($) {
  function calculateTotal() {
    let package_price =
      parseFloat(
        $(".woocommerce-Price-amount bdi")
          .clone()
          .children()
          .remove()
          .end()
          .text()
          .trim()
      ) || 0;
    let package_price_final = package_price * 10000;
    addon_price = parseFloat($());
    console.log(package_price_final);
  }
  $("#package").on("change", function () {
    calculateTotal();
  });
  $(".form-select").on("change", function () {
    let selected = $(this).find(":selected");
    let id = selected.data("id");
    if (id) {
      $(".add-ons-price-value").html(selected.data("price"));
    }
    calculateTotal();
  });
  $(".quantity").before("<div><label>Total:</label></div>");
  $;
  $("#flexCheckDefault").on("change", function () {
    if ($(this).prop("checked")) {
      $(".additinal-adult-select-block").css({
        "pointer-events": "all",
        "opacity": "1",
      });
    } else {
      $(".additinal-adult-select-block").css({
        "pointer-events": "none",
        "opacity": "0.5",
      });
      $("#additional_adult_select").val("");
    }
  });
});
