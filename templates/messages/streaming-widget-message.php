<script type="text/template" id="tmpl-streaming-widget-message">
	<div class="streaming-widget">
		<# if ( data.widget.href ) { #>
			<a target="_blank" href="{{data.widget.href}}">
				<# if ( data.headerLogo ) { #>
					<img src="{{data.headerLogo}}">
				<# } #>
				<# if ( data.widget.text ) { #>
					<div>
						{{data.widget.text}}
					</div>
				<# } #>
			</a>
		<# } #>
		<span class="streaming-widget-dismiss" id="js-streaming-widget-dismiss">&times;</span>
	</div>
</script>
