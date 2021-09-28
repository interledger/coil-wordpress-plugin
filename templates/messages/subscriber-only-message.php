<script type="text/template" id="tmpl-subscriber-only-message">
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
				<a target="_blank" href="{{data.button.href}}" class="coil-message-button">{{data.button.text}}</a>
			<# } #>
		</div>
	</div>
</script>
