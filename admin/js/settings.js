jQuery(document).ready(function ($) {
    var ftg_date_format = $('.ftg_date_time_format');
	if ( ftg_date_format.length > 0 )  {
        $( document.body ).on('change', $('.ftg_date_time_format'), function()	{
            if ( 'one-day-ago' === $('.ftg_date_time_format').val() ) {
                $('.custom_time_ago_wrap').show();
            } else  {
                $('.custom_time_ago_wrap').hide();
            }
        });
    }
});
