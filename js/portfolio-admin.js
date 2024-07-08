jQuery(document).ready(function ($) {
  $("#portfolio_image_gallery_button").on("click", function (event) {
    event.preventDefault();

    let frame = wp.media({
      title: "Add Images",
      button: {
        text: "Add",
      },
      multiple: true,
    });

    frame.on("select", function () {
      let selection = frame.state().get("selection");
      selection.map(function (attachmentg) {
        attachmentg = attachmentg.toJSON();
        let imgHtml =
          '<li class="image" data-attachment_id="' + attachmentg.id + '">';
        imgHtml +=
          '<img src="' +
          attachmentg.url +
          '" style="max-width:100px;margin:5px;" />';
        imgHtml += '<a href="#" class="remove-icon">×</a>';
        imgHtml += "</li>";

        $(".portfolio-image-gallery-items").append(imgHtml);
      });
    });

    frame.open();
  });

  $(".portfolio-image-gallery-items").on("click", ".remove-icon", function (e) {
    e.preventDefault();
    $(this).closest("li").remove();
  });


  $(".save-portfolio-image").on("click", function () {
    let images = [];
    $("#portfolio-image-gallery-container .image").each(function () {
      let image_id = $(this).data("attachment_id");
      images.push(image_id);
    });
    $("#text_portfolio_image_gallery").val(JSON.stringify(images));
  });

  // Agregar íconos
  $(".add-icons").on("click", function (e) {
    e.preventDefault();

    let frame = wp.media({
      title: "Add Icons",
      button: {
        text: "Add",
      },
      multiple: true,
    });

    frame.on("select", function () {
      let selection = frame.state().get("selection");
      selection.map(function (attachment) {
        attachment = attachment.toJSON();
        var iconHtml =
          '<li class="icon" data-attachment_id="' + attachment.id + '">';
        iconHtml +=
          '<img src="' +
          attachment.url +
          '" style="max-width:100px;margin:5px;" />';
        iconHtml +=
          '<input type="text" name="icon_names[]" placeholder="Icon Name" />';
        iconHtml +=
          '<input type="text" name="icon_links[]" placeholder="Icon Link" />';
        iconHtml += '<a href="#" class="remove-icon">×</a>';
        iconHtml += "</li>";

        $(".portfolio-icon-gallery-items").append(iconHtml);
      });
    });

    frame.open();
  });

  // Eliminar íconos
  $(".portfolio-icon-gallery-items").on("click", ".remove-icon", function (e) {
    e.preventDefault();
    $(this).closest("li").remove();
  });

  $(".save-portfolio-icons").on("click", function () {
    let icons = [];
    $("#portfolio-icon-gallery-container .icon").each(function () {
      let icon = {
        image_id: $(this).data("attachment_id"),
        name: $(this).find('input[name="icon_names[]"]').val(),
        link: $(this).find('input[name="icon_links[]"]').val(),
      };
      icons.push(icon);
    });
    $("#text_portfolio_icon_gallery").val(JSON.stringify(icons));
  });
});
