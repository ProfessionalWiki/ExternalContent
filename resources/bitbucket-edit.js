Prism.plugins.toolbar.registerButton('bitbucket-edit', {
	text: 'Edit',
	onClick: function (env) {
		var pre = env.element.parentNode;
		
		if (!pre || !pre.hasAttribute('data-src')) {
			return;
		}
		
		var src = pre.getAttribute('data-src');

		window.open(src, '_blank');
	}
});