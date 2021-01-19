jQuery(document).ready(function($) {
	$('.sp-body img').each(function() {
		$(this).attr({
			alt: $(this).attr('src'),
			src: smf_default_theme_url + '/images/loading_sm.gif'
		});
	});
	$("body").on("click", ".sp-head", function() {
		$this = $(this);
		if ($this.hasClass("unfolded")) {
			$this.removeClass("unfolded");
			$this.next().slideUp("fast");
			$this.next().addClass("folded");
		} else {
			$this.addClass("unfolded");
			$this.next().slideDown("fast");
			$this.next().removeClass("folded");
		}
		c = $this.parent().children('.sp-body');
		c.find('img').each(function() {
			$(this).attr('src', $(this).attr('alt'));
		});
	});
	$("body").on("click", ".sp-foot", function() {
		$this = $(this).closest("div.sp-body").prev();
		if ($this.hasClass("unfolded")) {
			$this.removeClass("unfolded");
			$this.next().slideUp("fast");
			$this.next().addClass("folded");
		}
	});
});