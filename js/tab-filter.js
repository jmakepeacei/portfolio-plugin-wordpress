jQuery(document).ready(function ($) {

    $(".tab").on("click", function () {
      var termId = $(this).data("term-id");

      $(".tab").removeClass("activa");
      $(this).addClass("activa");

      if (!termId) {
        return;
      }

      $(".blob-container").show();

      $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
          action: 'filter_portfolios',
          term_id: termId,
        },
        success: function (response) {
          $("#portfolio-gallery-images").html(response);
          $(".blob-container").hide();

        },
        error: function () {
          $(".blob-container").hide();
        },
      });

    });

});
