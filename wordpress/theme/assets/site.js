(function () {
	var $ = function (s, r) { return (r || document).querySelector(s); };
	var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

	function closeDd() {
		$$('.dd.open').forEach(function (d) {
			d.classList.remove('open');
			var b = $('.dd-btn', d);
			if (b) b.setAttribute('aria-expanded', 'false');
		});
	}

	function toggleMenu(open) {
		var m = $('#mnav'), b = $('[data-menu]');
		if (!m || !b) return;
		if (open === undefined) open = m.hidden;
		if (open) m.style.setProperty('--mnav-top', $('#hdr').getBoundingClientRect().bottom + 'px');
		m.hidden = !open;
		b.setAttribute('aria-expanded', String(open));
		$('.i-open', b).hidden = open;
		$('.i-close', b).hidden = !open;
		$('.sr', b).textContent = open ? b.dataset.labelClose : b.dataset.labelOpen;
		document.documentElement.classList.toggle('lock', open);
	}

	document.addEventListener('click', function (e) {
		var dd = e.target.closest('[data-dd]');
		if (dd) {
			var w = dd.parentElement, willOpen = !w.classList.contains('open');
			closeDd();
			if (willOpen) { w.classList.add('open'); dd.setAttribute('aria-expanded', 'true'); }
			return;
		}
		if (!e.target.closest('.dd')) closeDd();
		if (e.target.closest('[data-menu]')) toggleMenu();
	});
	document.addEventListener('focusout', function (e) {
		var dd = e.target.closest && e.target.closest('.dd');
		if (dd && !dd.contains(e.relatedTarget)) closeDd();
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closeDd(); toggleMenu(false); }
	});
	window.addEventListener('resize', function () {
		if (window.innerWidth >= 1180) toggleMenu(false);
	});
})();
