<script type="text/template" id="tmpl-streaming-widget-message">
	<div class="streaming-widget">
		<# if ( data.button.href ) { #>
			<a target="_blank" href="{{data.button.href}}">
				<# if ( data.headerLogo ) { #>
					<img src="{{data.headerLogo}}">
				<# } #>
				<# if ( data.button.href ) { #>
					<div>
						{{data.button.text}}
					</div>
				<# } #>
			</a>
		<# } #>
		<span class="streaming-widget-dismiss" id="js-streaming-widget-dismiss">&times;</span>
	</div>
</script>
