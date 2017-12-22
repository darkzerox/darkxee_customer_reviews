(function ($, $window, $document) {
    $.fn.fadeOutAndRemove = function (speed) {
        $(this).fadeOut(speed, function () {
            $(this).remove();
        })
    }

    $(document).ready(function () {
        
        $('.rev-item .rev-img').click(function () {
            $this = $(this).parent();
            $('.customer-rev-pop').fadeOutAndRemove('fast');

            var img = $this.find('.rev-img img').attr('src');
            var title = $this.find('h3').text();
            var detail = $this.find('.rev-detail').text();

            // console.log(img + title + detail);

            var skele = `            
            <div class="customer-rev-pop">
            <div class="customer-rev-shadow"></div>
                <div class="customer-rev-connenner">
                    <div class="customer-rev-img">
                        <img src= "${img}" />
                    </div>
                    <div class="customer-rev-title">
                    ${title}
                    </div>
                    <div class="customer-rev-detail">
                    ${detail}
                    </div>
                    <div class="customer-rev-close"><i class="fa fa-times" aria-hidden="true"></i></div>
                </div>
            </div>       
        `;
        $('body').append($(skele).hide().fadeIn(200));



            // console.log($this.text());

            $('.customer-rev-close , .customer-rev-shadow').click(function () {
                $('.customer-rev-pop').fadeOutAndRemove('fast');
            });
          

        });
    });


})(jQuery, jQuery(window), jQuery(document));