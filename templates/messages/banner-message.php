<script type="text/template" id="tmpl-banner-message">
	<div class="banner-message-inner">
		<# if ( data.content ) { #>
			<p class="coil-banner-message-content">{{data.content}}</p>
		<# } #>
		<# if ( data.button.href ) { #>
			<a target="_blank" href="{{data.button.href}}" class="coil-banner-message-button">{{data.button.text}}</a>
		<# } #>
		<span class="coil-banner-message-dismiss" id="js-coil-banner-dismiss">&times;</span>
	</div>
</script>
