/*eslint quotes: ["error", "single"]*/
/*eslint-env es6*/

jQuery(document).ready(function($) {
	let addLoader = function() {
		$(this).attr({
			'data-src': $(this).attr('src'),
			src: smf_default_theme_url + '/images/loading_sm.gif'
		});
	};

	$('.sp-body img').each(addLoader);

	const previewBody = document.getElementById('preview_body');

	if (previewBody) {
		const config = {childList: true};
		const callback = function(mutationsList) {
			for (let mutation of mutationsList) {
				if (mutation.type === 'childList') {
					$('.sp-body img').each(addLoader);
				}
			}
		};

		const observer = new MutationObserver(callback);
		observer.observe(previewBody, config);
	}

	$('body').on('click', '.sp-head', function() {
		let $this = $(this);
		$this.toggleClass('opened');
		$this.parent().is('open') ? $this.next().slideUp('fast') : $this.next().slideDown('fast');
		$this.parent().children('.sp-body').find('img').each(function() {
			$(this).attr('src', $(this).attr('data-src'));
		});
	});

	$('body').on('click', '.sp-foot', function() {
		$this = $(this).parent();
		$this.prev().toggleClass('opened');
		$this.parent().removeAttr("open");
	});
});
