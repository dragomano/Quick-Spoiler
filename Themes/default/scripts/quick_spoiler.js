jQuery(document).ready(function($) {
	$('.sp-body img').each(function() {
		$(this).attr({
			alt: $(this).attr('src'),
			src: smf_default_theme_url + '/images/loading_sm.gif'
		});
	});
	$('body').on('click', '.sp-head', function() {
		$this = $(this);
		if ($this.parent().is('open')) {
			$this.next().slideUp('fast');
		} else {
			$this.next().slideDown('fast');
		}
		c = $this.parent().children('.sp-body');
		c.find('img').each(function() {
			$(this).attr('src', $(this).attr('alt'));
		});
	});
	$('body').on('click', '.sp-foot', function() {
		$this = $(this).parent();
		$this.parent().removeAttr("open");
	});
});