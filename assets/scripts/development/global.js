$(function() {
	var $win = $(window),
			winScroll = $win.scrollTop(),
			$body = $('body'),
			$header = $('header'),
			headerHeight = $header.height(),
			headerScrolled = winScroll / headerHeight * 10,
			$headerContent = $('.header-content'),
			$sections = $('section'),
			$footer = $('footer');

	$body.addClass('js');
	sectionHeight();

	$win.on('resize', function() {
		sectionHeight();
	});
	
	$win.on('scroll resize', function() {
		winScroll = $win.scrollTop();
		headerHeight = $header.height();
		headerScrolled = winScroll / headerHeight * 10;
		if( winScroll <= headerHeight ) {
			$headerContent.css({'top': 50 - headerScrolled + '%'});
		}

		$sections.each(function() {
			var $s = $(this),
					$content = $s.find('.content'),
					offsetTop = $s.offset().top,
					height = $s.height(),
					offsetBottom = offsetTop + height,
					space = parseInt(height * 0.5),
					scrolled = (winScroll - offsetTop + space) / space * 25;

			if( winScroll >= offsetTop - space && winScroll < offsetBottom ) {
				$content.css({'bottom': 50 - scrolled + '%'});
			} else {
				$content.css({'bottom':'100%'});
			}
		});
	});

	function sectionHeight() {
		$sections.each(function() {
			var $s = $(this),
					$prev = $s.prev(),
					$allPrev = $s.prevAll(),
					prevHeight = $prev.height();

			$s.css({'top': $allPrev.length * prevHeight + 'px'});
		});

		$footerPrevAll = $footer.prevAll();
		$footer.css({'top': $footer.height() * $footerPrevAll.length});
	}
});