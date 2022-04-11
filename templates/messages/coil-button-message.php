<script type="text/template" id="tmpl-streaming-support-widget-message">
	<div class="streaming-support-widget">
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
		<span class="streaming-support-widget-dismiss" id="js-streaming-support-widget-dismiss">&times;</span>
	</div>
</script>
