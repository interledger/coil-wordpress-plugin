<script type="text/html" id="tmpl-full-width-message">
	<div class="coil-message-inner">
		<div class="coil-message-header">
			<# if ( data.headerLogo ) { #>
				{{{data.headerLogo}}}
			<# } #>

			<# if ( data.title ) { #>
				<p class="coil-message-title">{{data.title}}</p>
			<# } #>

			<# if ( data.content ) { #>
				<p class="coil-message-content">{{data.content}}</p>
			<# } #>

			<# if ( data.button.href ) { #>
				<a href="{{data.button.href}}" class="coil-message-button">{{data.button.text}}</a>
			<# } #>
		</div>
		<div class="coil-message-footer">
			<p class="coil-footer-content">This content is for Coil subscribers only! To access, subscribe to coil.com. Coil requires use of a browser extension which your browser doesn't support. Please use Chrome or Firefox to access this content</p>
		</div>
	</div>
</script>
