(function($){
	$(document).ready(function(){
		$('body').on('change', 'input[type=radio][name=mail_smtp_encrypt]', function(){
			$('.mail-smtp-port').val($(this).val());
		});
	});
})(jQuery);