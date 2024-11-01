<div class="preview-settings__row _is-open">
	<?php $ruleTypes = $types->rules_types; ?>
	<div class="row">
		<div class="col-lg-12">

			<div class="preview-settings__row-title">Add Ruleset <i class="icon icon-arrow-small-down"></i></div>

			<div class="preview-settings__row-content">

				<div class="checkbox-card" id="url_mode_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="url_mode_enabled" name="form[url_mode_enabled]" value="1">
						<label for="url_mode_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">Visitors browsing specific pages</div>
					<div class="checkbox-card__dscr">Show when the visitor browsing specific pages</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when the URL path</div>

							<div class="conditions-container"></div>
							<a href="" onclick="urlRule.addCondition(); return false;" class="conditions-block__add button button_light button_green">
								<i class="icon icon-plus"></i>
								Add Condition
							</a>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="traffic_source_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="traffic_source_enabled" name="form[traffic_source_enabled]" value="1">
						<label for="traffic_source_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">Visitors from a specific traffic source (Referrer Detection)</div>
					<div class="checkbox-card__dscr">Show when the visitor came from a specific traffic source</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when a traffic source is</div>

							<div class="conditions-container"></div>
							<a href="" onclick="trafficRule.addCondition(); return false;" class="conditions-block__add button button_light button_green">
								<i class="icon icon-plus"></i>
								Add Condition
							</a>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="scrolling_down_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="scrolling_down_enabled" name="form[scrolling_down_enabled]" value="1">
						<label for="scrolling_down_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">After scrolling 'X' amount</div>
					<div class="checkbox-card__dscr">Show when the visitor has scrolled a certain amount.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when user has scrolled at least</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<input type="number" step="100" min="100" max="10000" name="form[scrolling_down]" class="form-control number-input">
									<span class="form-control__opt">pixels</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="on_exit_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="on_exit_enabled" name="form[on_exit_enabled]" value="1">
						<label for="on_exit_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">On Exit-Intent</div>
					<div class="checkbox-card__dscr">Show when the visitor tries to leave the site.</div>
				</div>

				<div class="checkbox-card" id="new_return_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="new_return_enabled" name="form[new_return_enabled]" value="1">
						<label for="new_return_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">Visitor who are new vs. returning</div>
					<div class="checkbox-card__dscr">Show when the visitor is new or returning.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when the visitor</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<div class="select2-wrapper custom-select2">
										<select name="form[new_return]" class="select2">
											<?php foreach($ruleTypes->new_returns as $k => $val): ?>
												<option value="<?php echo $k ?>"><?php echo $val ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="on_pageview_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="on_pageview_enabled" name="form[on_pageview_enabled]" value="1">
						<label for="on_pageview_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">Visitor has viewed 'X' pages</div>
					<div class="checkbox-card__dscr">Show when the visitor has viewed a certain amount of pages on your site.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when user has has viewed</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<input type="number" min="1" max="1000" name="form[on_pageview]" class="form-control number-input">
									<span class="form-control__opt">pages</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="device_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="device_enabled" name="form[device_enabled]" value="1">
						<label for="device_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">Visitors on specific device</div>
					<div class="checkbox-card__dscr">Show only when visitors using are on a phone, tablet or desktop.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when the visitor is on</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<div class="select2-wrapper custom-select2">
										<select name="form[device]" class="select2">
											<?php foreach($ruleTypes->device_types as $k => $val): ?>
												<option value="<?php echo $k ?>"><?php echo $val ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="after_seconds_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="after_seconds_enabled" name="form[after_seconds_enabled]" value="1">
						<label for="after_seconds_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">After 'X' Seconds</div>
					<div class="checkbox-card__dscr">Show when the visitor has viewed your site for a certain period of time.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when time on page is at least</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<input type="number" min="1" max="10000" name="form[after_seconds]" class="form-control number-input">
									<span class="form-control__opt">seconds</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="inactivity_seconds_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="inactivity_seconds_enabled" name="form[inactivity_seconds_enabled]" value="1">
						<label for="inactivity_seconds_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">User inactivity</div>
					<div class="checkbox-card__dscr">Show when the visitor is inactive for a certain period of time.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when the visitor is inactive for</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<input type="number" min="1" max="10000" name="form[inactivity_seconds]" class="form-control number-input">
									<span class="form-control__opt">seconds</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="checkbox-card" id="visitor_time_rule">
					<div class="checkbox-item">
						<input type="checkbox" id="visitor_time_enabled" name="form[visitor_time_enabled]" value="1">
						<label for="visitor_time_enabled"><i class="icon icon-check"></i></label>
					</div>
					<div class="checkbox-card__title">Based on visitor's time</div>
					<div class="checkbox-card__dscr">Show when current time is between selected.</div>
					<div class="conditions-block">
						<a href="" class="conditions-block__btn">Show conditions</a>
						<div class="conditions-block__content">
							<div class="conditions-block__title">Show when the current time is</div>
							<div class="conditions-block__item">
								<div class="form-group">
									<span class="form-control__opt">From</span>
									<input name="form[visitor_time_from]" class="form-control timepicker">

								</div>
								<div class="form-group">
									<span class="form-control__opt">To</span>
									<input name="form[visitor_time_to]" class="form-control timepicker">
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>
	</div>

</div>
<script type="text/javascript">
	var rulesetParams = {
		showUrlPatternOn : <?php echo json_encode($ruleTypes->url_modes_show_pattern) ?>,
	urlModes : <?php echo json_encode($ruleTypes->url_modes) ?>,
	conditionsHTML : {
		url_mode : '<div class="conditions-block__item">' +
		'<div class="form-group">' +
		'<div class="select2-wrapper custom-select2">' +
		'<select class="rule-url-mode-select select2" name="form[url_mode][]">' +
		<?php foreach($ruleTypes->url_modes as $k => $val): ?>
		'<option value="<?php echo $k ?>"><?php echo esc_html($val) ?></option>'+
		<?php endforeach; ?>
		'</select>' +
		'</div>' +
		'</div>' +
		'<div class="form-group">' +
		'<input type="text" name="form[url_pattern][]" class="form-control rule-url-mode-pattern hide">' +
		'</div>' +

		'</div>',
			traffic_source : '<div class="conditions-block__item">' +
		'<div class="form-group">' +
		'<div class="select2-wrapper custom-select2">' +
		'<select class="select2" name="form[traffic_source][]">' +
		<?php foreach($ruleTypes->traffic_sources as $k => $val): ?>
		'<option value="<?php echo $k ?>"><?php echo esc_html($val) ?></option>'+
		<?php endforeach; ?>
		'</select>' +
		'</div>' +
		'</div>' +
		'</div>',
	}
	};
</script>