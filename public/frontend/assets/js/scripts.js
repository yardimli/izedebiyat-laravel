$(document).ready(function () {
	
	$('#UserMenu').on('click', function () {
		$('#UserMenuDiv').css({"top": $('#UserMenu').position().top + 9, "left": $('#UserMenu').position().left + 144});
		$('#UserMenuDiv').toggle();
	});
	
	
	$(window).scroll(function (e) {
		var $el = $('#UserMenuDiv');
		if ($el.is(':visible')) {
			var isPositionFixed = ($el.css('position') == 'fixed');
			if ($(this).scrollTop() > 60 && !isPositionFixed) {
				$el.css({'position': 'fixed', 'top': '-52px'});
			}
			if ($(this).scrollTop() < 60 && isPositionFixed) {
				$el.css({'position': 'absolute', 'top': $('#UserMenu').position().top + 9});
			}
		}
	});
	
	// popover({
	//     html : true,
	//     placement: "bottom",
	//     // container:'body',
	//     content: function() {
	//       var TempHTML = $('#UserMenuDiv').html();
	//       TempHTML = TempHTML.replace(/login2/g,"login");
	//       TempHTML = TempHTML.replace(/signup2/g,"signup");
	//       return TempHTML;
	//     }
	//   });
	// }
	//
	// $('#UserMenu').on('shown.bs.popover', function () {
	//   console.log("!!!");
	//   $("#login_tab").click(function (e) {
	//     e.preventDefault();
	//     $(this).tab('show');
	//   });
	//
	//   $("#signup_tab").click(function (e) {
	//     e.preventDefault();
	//     $(this).tab('show');
	//   });
	// });
	
	
	function stickMenu() {
		$(".stick").scrollToFixed({
			preFixed: function () {
				$(".menu-top").animate({
					height: 83
				}, 400, function () {
					$(this).css("overflow", "visible")
				})
			},
			postFixed: function () {
				$(".menu-top").css("overflow", "hidden").animate({
					height: 0
				}, 400)
			}
		})
	}
	
	function mobileMenu() {
		
		$('.menu-toggle-icon').on('click', function (event) {
			$(this).toggleClass('act');
			if ($(this).hasClass('act')) {
				$('.mobi-menu').addClass('act');
			} else {
				$('.mobi-menu').removeClass('act');
			}
		});
		
		$('.mobi-menu .menu-item-has-children').append('<span class="sub-menu-toggle"></span>');
		
		$('.sub-menu-toggle').on('click', function (event) {
			$(this).parent('li').toggleClass('open-submenu');
		});
	}
	
	$(function () {
		$('.lazy').Lazy({
				scrollDirection: 'vertical',
				effect: 'fadeIn',
				visibleOnly: true,
			}
		);
	});
	
	function backToTop() {
		$(window).scroll(function () {
			$(this).scrollTop() > 100 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
		});
		
		$(".back-to-top").on('click', function () {
			return $("html, body").animate({
				scrollTop: 0
			}, 250), !1
		});
	}
	
	function searchForm() {
		$(".search-toggle").on('click', function () {
			$('header .search-form').toggleClass('open-search');
		})
	}
	
	function scrollBar() {
		// $(window).scroll(function () {
		//   // calculate the percentage the user has scrolled down the page
		//   var scrollPercent = 100 * $(window).scrollTop() / ($(document).height() - $(window).height());
		//   $('.top-scroll-bar').css('width', scrollPercent + "%");
		//
		// });
	}
	
	function theiaSticky() {
		$('.sticky-sidebar').theiaStickySidebar({
			additionalMarginTop: 70
		});
	};
	
	
	$(document).ready(function () {
		backToTop();
		mobileMenu();
		stickMenu();
		searchForm();
		scrollBar();
		theiaSticky();
	});
	
	
});
