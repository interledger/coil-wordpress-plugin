<script type="text/template" id="tmpl-banner-message">
	<div class="banner-message-inner">
		<p class="coil-banner-message-content"><?php esc_html_e( 'This site is monetized using Coil. If you enjoy the content, consider supporting us by signing up for a Coil Membership. Here\'s how…', 'coil-web-monetization' ); ?></p>
		<# if ( data.button.href ) { #>
			<a target="_blank" href="{{data.button.href}}" class="coil-banner-message-button">{{data.button.text}}</a>
		<# } #>
		<span class="coil-banner-message-dismiss" id="js-coil-banner-dismiss">&times;</span>
	</div>
</script>