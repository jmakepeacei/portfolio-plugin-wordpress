jQuery(document).ready(function($) {   
    $(document).on('click','.portfolio-link', function(e) {
                
        let postId = $(this).data('post-id');        
        
        let modalContent = $('<div class="portfolio-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true">');
        let modalDialog = $('<div class="modal-dialog modal-xl" role="document">');
        let modalContentInner = $('<div class="modal-content">');
        let modalBody = $('<div class="modal-body">');
        
        let $galleryItems = $('#gallery-item-' + postId).find('.gallery-item');
        
        let carouselInner = $('<div class="carousel-inner">');
        let carouselIndicators = $('<ol class="carousel-indicators">');
        let thumbnailContainer = $('<div class="carousel-thumbnails">');

        $galleryItems.each(function(index) {
            let imageUrl = $(this).find('img').attr('src');
            
            carouselInner.append('<div class="carousel-item' + (index === 0 ? ' active' : '') + '"><img src="' + imageUrl + '" class="d-block w-100" alt="Image' + (index + 1) + '"></div>');
            
            carouselIndicators.append('<li data-target="#portfolioCarousel" data-bs-slide-to="' + index + '" class="' + (index === 0 ? 'active' : '') + '"></li>');
            
            thumbnailContainer.append('<img src="' + imageUrl + '" width="60px" class="carousel-thumbnail' + (index === 0 ? ' active' : '') + '" data-bs-slide-to="' + index + '" alt="Thumbnail' + (index + 1) + '">');
        });

        var carousel = $('<div id="portfolioCarousel" class="carousel slide" data-ride="carousel">');
        carousel.append(carouselIndicators);
        carousel.append(carouselInner);

        modalBody.append(carousel);
        modalBody.append(thumbnailContainer);
        modalContentInner.append(modalBody);
        modalDialog.append(modalContentInner);
        modalContent.append(modalDialog);
        
        modalContent.modal('show'); 
        
        modalContent.on('hidden.bs.modal', function () {
            modalContent.remove();
        });

        $('#portfolioCarousel').carousel();
        
        thumbnailContainer.on('click', '.carousel-thumbnail', function() {
            var slideTo = $(this).data('bs-slide-to');
            console.log(slideTo);
            $('#portfolioCarousel').carousel(slideTo);
            $('.carousel-thumbnail').removeClass('active');
            $(this).addClass('active');
        });

        $('#portfolioCarousel').on('slide.bs.carousel', function (e) {
            let nextIndex = $(e.relatedTarget).index();
            $('.carousel-thumbnail').removeClass('active');
            $('.carousel-thumbnail[data-bs-slide-to="' + nextIndex + '"]').addClass('active');
        });

        modalContent.appendTo('body');
    });    
});



