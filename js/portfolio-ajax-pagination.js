jQuery(document).ready(function($) {

    function corre(){
    $('#portfolio-pagination a').on('click', function(e) {
        e.preventDefault();

        var page = $(this).attr('href').split('paged=')[1];
                
        $.ajax({
            url: portfolioAjax,
            type: 'POST',
            data: {
                action: 'filter_portfolios',
                paged: page,
            },
            beforeSend: function() {
                $(".blob-container").show();
            },
            success: function(response) {
                $(".blob-container").hide();
                $('#portfolio-gallery-images').html(response);
                corre();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(".blob-container").hide();
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown);
            }
        });
    });
    }

    corre();

});
