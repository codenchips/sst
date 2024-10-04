<h2>Home</h2>

<script id="handlebars-demo" type="text/x-handlebars-template">
		  {{#studyStatus students}}
			 {{name}} has {{passingYear}}.<br>
		  {{/studyStatus}}
		</script>

		<div class="uk-grid-small uk-child-width-auto uk-margin" uk-grid uk-countdown="date: 2024-10-05">
			<div>
				<div class="uk-countdown-number uk-countdown-days"></div>
			</div>
			<div>
				<div class="uk-countdown-number uk-countdown-hours"></div>
			</div>
			<div>
				<div class="uk-countdown-number uk-countdown-minutes"></div>
			</div>
		</div>
