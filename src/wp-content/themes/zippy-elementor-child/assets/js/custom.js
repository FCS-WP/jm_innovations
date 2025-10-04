$("#shop-block").find(".button").html("View Detail");
$(".form-select");
jQuery(document).ready(function ($) {
  //$(".quantity").before("<div><label>Total:$</label></div>");
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
