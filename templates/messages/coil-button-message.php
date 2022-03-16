<script type="text/template" id="tmpl-coil-button-message">
	<div class="coil-button">
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
		<span class="coil-button-dismiss" id="js-coil-button-dismiss">&times;</span>
	</div>
</script>
