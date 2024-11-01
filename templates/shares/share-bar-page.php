<div class="wrap">
	<?php if (!empty($redirect)) : ?>
		<script>
			window.location.href = '<?php echo $redirect; ?>';
		</script>
	<?php return; endif; ?>
	<a href="?page=s3_main_slug"><?php echo __('All Share Accounts', 'social3'); ?></a>

	<h1><?php echo __('Share Account:', 'social3'); ?> <?php echo $account->id; ?></h1>
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="share_bar[id]" value="<?php echo $account->id; ?>"/>
		<input type="hidden" name="share_bar[site_id]" value="<?php echo $account->site_id; ?>"/>
		<input type="hidden" name="share_bar[brand_id]" value="<?php echo $account->brand_id; ?>"/>
		<?php wp_nonce_field('s3_share_bar_action_save', 's3_share_bar_action_save_nonce') ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th></th>
					<td>
						<label for="share_count">
							<input name="share_bar[share_count]" type="checkbox" id="share_count" value="1"
							       <?php echo ($account->share_count) ? 'checked="checked"': ''; ?>
							>
							<?php echo __('Show share count', 'social3'); ?>
						</label>
					</td>
				</tr>

				<tr class="share_min <?php echo ($account->share_count) ? '': 'hidden'; ?>">
					<th><label for="share_min"><?php echo __('Minimum # To Show', 'social3'); ?>:</label></th>
					<td>
						<input id="share_min" name="share_bar[share_min]" value="<?php echo ($account->share_min) ? : ''; ?>"
							<?php echo ($account->share_count) ? '' : 'disabled="disabled"'; ?> type="number" min="0"/>
					</td>
				</tr>

				<tr>
					<th><label for="placement"><?php echo __('Select placement desktop', 'social3'); ?>:</label></th>
					<td>
						<select name="share_bar[placement]" id="placement">
							<?php foreach($types->placements as $placement) : ?>
								<option <?php echo ($placement == $account->placement) ? 'selected="selected"' : ''; ?>
									value="<?php echo $placement; ?>">
									<?php echo $placement; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th><label for="placement_mobile"><?php echo __('Select placement mobile', 'social3'); ?>:</label></th>
					<td>
						<select name="share_bar[placement_mobile]" id="placement_mobile">
							<?php foreach($types->placements_mobile as $placement) : ?>
								<option <?php echo ($placement == $account->placement_mobile) ? 'selected="selected"' : ''; ?>
									value="<?php echo $placement; ?>">
									<?php echo $placement; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<tr>
					<th><label for=""><?php echo __('Select network', 'social3'); ?>:</label></th>
					<td>
						<?php foreach($types->types as $type) : ?>
							<label for="share_type_<?php echo $type->id; ?>">
								<input name="share_bar[share_types][]" type="checkbox"
								       id="share_type_<?php echo $type->id; ?>"
								       value="<?php echo $type->id; ?>"
								       <?php echo ($this->is_checked_type($type->id, $account->share_types)) ? 'checked="checked"': ''; ?>	>
								<?php echo __($type->title, 'social3'); ?>
							</label>
							</br>
							</br>
						<?php endforeach; ?>
					</td>
				</tr>

				<?php if (!$auto_integration && !empty($account->id)) : ?>
				<tr>
					<th></th>
					<td>
						<div class="notice notice-warning is-dismissible">
							<p>
								<?php echo __('Auto integration with Social3 is disabled. Make sure next this code is included within your &lt;head&gt; tags.', 'social3'); ?>
								<br> <?php echo sprintf(__('Or enable auto integration <a href="%s">here.</a>', 'social3'), '/wp-admin/admin.php?page=s3-connector-options'); ?>
							</p>

							<textarea readonly="readonly" disabled="disabled" rows="5" cols="80"><?php echo $script; ?></textarea>
						</div>
					</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<a class="button" href="?page=s3_main_slug"><?php echo __('Cancel', 'social3'); ?></a>
		<input type="submit" value="<?php echo __('Save', 'social3'); ?>" class="button button-primary"/>
	</form>

	<script>
		( function ( $ ) {
			"use strict";

			$('#share_count').on( 'change', function() {
				if ($(this).prop('checked')) {
					$('.share_min').removeClass('hidden');
					$('.share_min').find('input').prop('disabled', false);
				} else {
					$('.share_min').addClass('hidden');
					$('.share_min').find('input').prop('disabled', true);
				}
			});
		}( jQuery ) );
	</script>
</div>