<div class="wrap social3">
	<?php if (!empty($redirect)) : ?>
		<script>
			window.location.href = '<?php echo $redirect; ?>';
		</script>
	<?php return; endif; ?>

	<a href="?page=s3_menu_list_builders"><?php echo __('All Lists', 'social3'); ?></a>

	<h1><?php echo __('Start A/B test', 'social3'); ?></h1>
	<input type="hidden" id="convFrom" value="<?php echo $convFrom; ?>">
	<input type="hidden" id="convTo" value="<?php echo $convTo; ?>">

	<form method="post" id="testForm">
		<input type="hidden" name="form[status]" value="<?php echo ($test->status) ? $test->status : 1; ?>" />
		<input type="hidden" name="form[brand_id]" value="<?php echo ($test->brand_id) ? $test->brand_id : 0; ?>" />
		<?php wp_nonce_field('s3_list_builder_test_action_save', 's3_list_builder_test_action_save_nonce') ?>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label class="" for=""><?php echo __('Number of Visits Required', 'social3'); ?></label>
						<input name="form[threshold]" value="<?php echo $test->threshold; ?>" type="text"
						       class="form-control"/>
					</div>
				</div>
			</div>
			<div class="form-selection">
				<div class="form-selection__title"><?php echo __('Select Form', 'social3'); ?></div>

				<?php $i = 0; ?>
				<div class="row">
					<?php foreach($lists as $list) : ?>
						<div class="col-md-4">
							<div class="form-selection__item">
								<input type="checkbox" id="list<?php echo $list->id; ?>" name="form[lists][]"
                                       value="<?php echo $list->id; ?>"
                                       <?php echo (in_array($list->id, $test->lists)) ? 'checked' : ''; ?> />
								<label for="list<?php echo $list->id; ?>" data-title="Form: <?php echo $list->name; ?>"></label>
								<div class="form-item">
									<div class="form-item__header">
										<div class="form-item__title">
                                            Form: <?php echo $list->name; ?>
                                            <span class="enabled-indicator <?php echo $list->status ? '_enabled' : '_disabled'; ?>"></span>
                                        </div>
									</div>
									<div class="form-item__content">
										<div class="conversions-block">
											<div class="chart-block">
												<div class="chart" id="conversionsChart<?php echo $list->id; ?>"></div>
											</div>
											<div class="conversions-info">
												<div class="conversions-info__item conversions-info__item_accented">
													<span class="val"><?php echo $list->conversation; ?>%</span>
													<span class="option">Conversation</span>
												</div>
												<div class="conversions-info__item">
													<span class="val tests-cnt">0</span>
													<span class="option">Tests</span>
												</div>
												<div class="conversions-info__item">
													<span class="val clicks-cnt">0</span>
													<span class="option">Clicks</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						if (++$i%3 === 0) {
							echo '</div><div class="row">';
						}
						?>
					<?php endforeach; ?>
				</div>
			</div>

		<a class="button" href="?page=s3_menu_list_builders"><?php echo __('Cancel', 'social3'); ?></a>
		<input type="submit" value="<?php echo __('Start A/B test', 'social3'); ?>" class="button button-primary"/>
	</form>

	<script>
		( function ( $ ) {
			"use strict";

			var chartsData = [],
				chartsObject = [];

			jQuery(document).ready(function () {
				google.charts.load('current', {'packages': ['corechart']});
				google.charts.setOnLoadCallback(initialLoad);
			});

			function drawChart(listId, chartData) {
				if (chartData.conversions.length === 1) {
					chartData.conversions.push(['', 0]);
				}

				var dataConv = google.visualization.arrayToDataTable(chartData.conversions);

				var options1 = {
					title: '',
					hAxis: {
						textStyle: {
							fontSize: 12,
							color: '#8a97a3'
						}
					},
					vAxis: {
						textStyle: {
							fontSize: 12,
							color: '#8a97a3'
						},
						baseline: 'none'
					},
					curveType: 'function',
					legend: 'none',
					series: {
						0: {color: '#57d38c'}
					}
				};
				chartsObject[listId] = new google.visualization.AreaChart(document.getElementById('conversionsChart' + listId));
				chartsObject[listId].draw(dataConv, options1);
			}

			function getListData(listId) {
				$.ajax({
					type: "POST",
					url: ajaxurl,
					dataType: 'json',
					data: {
						action: 'list_builder_chart_data',
						list_id: listId,
						conv_from: $('#convFrom').val(),
						conv_to: $('#convTo').val()
					},
					success: function (data) {
						chartsData[listId] = data.chartData;
						drawChart(listId, data.chartData);
						$('#list'+listId+' .conversation-percent').text(data.conversation);
						$('#list'+listId+' .tests-cnt').text(data.tests);
						$('#list'+listId+' .clicks-cnt').text(data.clicks);
					},
					error: function (data) {
						var errors = $.parseJSON(data.responseText);
						if (typeof errors.message !== "undefined") {
							alert(errors.message);
						}
					}
				});
			}

			function initialLoad() {
				$('input[name="form[lists][]"]').each(function() {
					getListData($(this).val());
				});

				$(window).resize(throttle(function () {
					chartsData.forEach(function (item, index) {
						drawChart(index, item);
					});
				}, 200));
			}

			function throttle(func, ms) {

				var isThrottled = false,
					savedArgs,
					savedThis;

				function wrapper() {

					if (isThrottled) { // (2)
						savedArgs = arguments;
						savedThis = this;
						return;
					}

					func.apply(this, arguments); // (1)

					isThrottled = true;

					setTimeout(function() {
						isThrottled = false; // (3)
						if (savedArgs) {
							wrapper.apply(savedThis, savedArgs);
							savedArgs = savedThis = null;
						}
					}, ms);
				}

				return wrapper;
			}

		}( jQuery ) );
	</script>
</div>