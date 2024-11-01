<?php add_thickbox(); ?>

<div class="wrap list-builder-wrap">
	<?php if (!empty($redirect)) : ?>
		<script>
			window.location.href = '<?php echo $redirect; ?>';
		</script>
	<?php return; endif; ?>
	<a href="?page=s3_menu_list_builders"><?php echo __('All Lists', 'social3'); ?></a>

	<h1><?php echo __('Form:', 'social3'); ?> <?php echo $list->id; ?></h1>
	<form method="post" enctype="multipart/form-data" id="subscriptionForm" class="preview-settings">
		<input type="hidden" name="form[id]" value="<?php echo $list->id; ?>" id="form_id"/>
		<input type="hidden" name="form[status]" value="<?php echo ($list->status) ? $list->status : 0; ?>" />
		<input type="hidden" name="form[main_site_id]" value="<?php echo $list->main_site_id; ?>"/>
		<input type="hidden" name="form[brand_id]" value="<?php echo $list->brand_id; ?>"/>
		<input type="hidden" name="form[name]" value="<?php echo $list->name; ?>"/>
		<?php wp_nonce_field('s3_list_builder_action_save', 's3_list_builder_action_save_nonce') ?>

		<div class="form-group">
			<label class="" for=""><?php echo __('Name', 'social3'); ?></label>
			<input type="text" maxlength="250" class="form-control" name="form[name]" value="<?php echo $list->name; ?>">
		</div>

		<div class="form-group">
			<label class="" for=""><?php echo __('Priority', 'social3'); ?></label>
			<input type="number" min="1" class="form-control" name="form[priority]"
			       value="<?php echo (isset($list->priority)) ? $list->priority : 1; ?>">
		</div>

		<div class="preview-settings__row _is-open">
			<div class="row">
				<div class="col-lg-12">
					<div class="preview-settings__row-title">
						<?php echo __('Style', 'social3'); ?>
						<i class="icon icon-arrow-small-down"></i>
					</div>

					<div class="preview-settings__row-content">
						<div class="subscribe-styles">
							<?php foreach($types->form_designs as $index=>$value) : ?>
								<?php
								if (empty($list->form_design)) {
									$list->form_design = $index;
								}
								?>
								<label id="form_design_<?php echo $index; ?>" class="list-design-label
								<?php echo (empty($value->image)) ? 'm-standart' : ''; ?>
								<?php echo ($index == $list->form_design) ? 'checked' : ''; ?>">
									<div class="hidden">
										<input type="radio" id="form_design_<?php echo $index; ?>" name="form[form_design]"
											<?php echo ($index == $list->form_design) ? 'checked="checked"' : ''; ?>
											   value="<?php echo $index; ?>"/>
									</div>

									<div class="list-image">
										<img src="<?php echo $value->image; ?>"/>
									</div>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="preview-settings__row _is-open">
			<div class="row">
				<div class="col-lg-12">
					<div class="preview-settings__row-title">
						<?php echo __('Type', 'social3'); ?>
						<i class="icon icon-arrow-small-down"></i>
					</div>

					<div class="preview-settings__row-content">
						<div class="form-group">
							<div class="select2-wrapper custom-select2">
								<select name="form[form_type]" class="select2 select2-hidden-accessible">
									<?php foreach($types->form_types as $index=>$title) : ?>
										<option <?php echo ($index == $list->form_type) ? 'selected="selected"' : ''; ?>
											value="<?php echo $index; ?>">
											<?php echo $title; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="preview-settings__row">
			<div class="row">
				<div class="col-lg-12">
					<div class="preview-settings__row-title">
						<?php echo __('Desktop Placement', 'social3'); ?>
						<i class="icon icon-arrow-small-down"></i>
					</div>

					<div class="preview-settings__row-content">
						<div class="form-group">
							<div class="select2-wrapper custom-select2">
								<select name="form[form_placement]" id="form_type" class="select2 select2-hidden-accessible">
									<option value="bottom-left"
										<?php echo ("bottom-left" == $list->form_placement) ? 'selected="selected"' : ''; ?> >bottom left</option>
									<option value="bottom-right"
										<?php echo ("bottom-right" == $list->form_placement) ? 'selected="selected"' : ''; ?>>bottom right</option>
									<option value="top-left"
										<?php echo ("top-left" == $list->form_placement) ? 'selected="selected"' : ''; ?>>top left</option>
									<option value="top-right"
										<?php echo ("top-right" == $list->form_placement) ? 'selected="selected"' : ''; ?>>top right</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="preview-settings__row">

			<div class="row">
				<div class="col-lg-12">

					<div class="preview-settings__row-title">
						<?php echo __('Content', 'social3'); ?>
						<i class="icon icon-arrow-small-down"></i>
					</div>

					<div class="preview-settings__row-content">
						<div class="form-group">
							<label class="" for=""><?php echo __('Title', 'social3'); ?></label>
							<input type="text" maxlength="250" class="form-control" name="form[form_title]"
							       value="<?php echo ($list->form_title) ? : 'Subscribe form'; ?>">
						</div>
						<div class="form-group">
							<label class="" for=""><?php echo __('Text', 'social3'); ?></label>
							<textarea name="form[form_text]" maxlength="600"
							          class="form-control"><?php echo ($list->form_text) ? : 'Sign up today for free and be the first to get notified on new updates.'; ?></textarea>
						</div>
						<div class="form-group">
							<div class="tabs">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#text" data-toggle="tab" aria-expanded="false"><?php echo __('Text after Subscription', 'social3'); ?></a>
									</li>
									<li>
										<a href="#url" data-toggle="tab" aria-expanded="false"><?php echo __('Redirect URL', 'social3'); ?></a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="text">
										<textarea maxlength="600" class="form-control" name="form[form_after]"><?php echo $list->form_after; ?></textarea>
									</div>
									<div class="tab-pane" id="url">
										<input data-toggle="tooltip" class="form-control" type="text" maxlength="600"
										       data-original-title="This is the URL the user would get redirected to after clicking the Subscribe button"
										       name="form[form_redirect]" value="<?php echo $list->form_redirect; ?>">
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>

		<div class="preview-settings__row _is-open">
			<div class="row">
				<div class="col-lg-12">
					<div class="preview-settings__row-title">
						<?php echo __('Email Service ', 'social3'); ?>
						<i class="icon icon-arrow-small-down"></i>
					</div>

					<div class="preview-settings__row-content">
						<input name="form[connections]" type="hidden" value="" />
						<div class="subscribe-email-clients">
							<?php $connections = (array)$types->available_connections; ?>
							<?php $connectionImages = (array)$types->connection_images; ?>
							<?php foreach($types->connection_types as $name => $index) : ?>
								<div id="connection_type_<?php echo $index; ?>"
								     class="subscribe-email-client subscribe-box <?php echo (!isset($connections[$name])) ? 'm-disabled' : ''; ?>">
									<label class="radio-field">
										<div class="hidden">
											<input name="connection_type" type="checkbox" value="<?php echo $index; ?>"
											       data-connection-id="<?php echo (isset($connections[$name])) ? $connections[$name] : ''; ?>">
										</div>
									</label>

									<div class="subscribe-email-client__box">
										<?php $images = (array)$connectionImages[$name]; ?>
										<img class="subscribe-email-client__img" src="<?php echo (isset($images['connected'])) ? $images['connected'] : ''; ?>" alt="">
										<img class="subscribe-email-client__img m-active" src="<?php echo (isset($images['active'])) ? $images['active'] : ''; ?>" alt="">
										<img class="subscribe-email-client__img m-disabled" src="<?php echo (isset($images['disabled'])) ? $images['disabled'] : ''; ?>" alt="">
									</div>

									<a href="#" class="remove-connection preview-settings-select__link"
										<?php echo (isset($connections[$name])) ? '' : 'style="display:none;"'; ?>>
										<i class="icon"></i> Disconnect
									</a>
								</div>
							<?php endforeach; ?>
						</div>

						<div id="selectListsBlock" class="clearfix">

						</div>
					</div>
				</div>
			</div>
		</div>

		<?php if (!$auto_integration && !empty($list->id)) : ?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php echo __('Auto integration with Social3 is disabled. Make sure next this code is included within your &lt;head&gt; tags.', 'social3'); ?>
					<br> <?php echo sprintf(__('Or enable auto integration <a href="%s">here.</a>', 'social3'), '/wp-admin/admin.php?page=s3-connector-options'); ?>
				</p>

				<textarea readonly="readonly" disabled="disabled" rows="5" cols="80"><?php echo $script; ?></textarea>
			</div>
		<?php endif; ?>

		<?php if (!empty($inline_code)) : ?>
			<div class="notice notice-warning">
				<p>
					<?php echo __('For this form type you should manually add next code where you need.', 'social3'); ?>
				</p>

				<textarea readonly="readonly" disabled="disabled" rows="5" cols="80"><?php echo $inline_code; ?></textarea>
			</div>
		<?php endif; ?>

		<?php require $this->template_path . 'list-builder/list-builder-ruleset-page.php'; ?>

		<a class="button" href="?page=s3_menu_list_builders"><?php echo __('Cancel', 'social3'); ?></a>
		<input type="submit" value="<?php echo __('Save', 'social3'); ?>" class="button button-primary"/>
	</form>

	<div id="connectionModal" style="display:none;">
		<div class="modal-header">
			<h2 class="modal-title">Create New Email Service Connection</h2>
		</div>
		<div class="modal-body">
			<form id="connectionForm" method="POST">
				<input type="hidden" name="type">
				<input type="hidden" name="all_data">

				<table class="form-table">

					<tr class="row emailBlock serviceBlock">
						<td><label for="email" class="control-label ">Email:</label></td>
						<td><input type="email" class="form-control" id="email" name="email" value=""></td>
					</tr>
					<tr class="row keyBlock serviceBlock">
						<td><label for="key" class="control-label ">API Key:</label></td>
						<td><input type="text" class="form-control" id="key" name="key" value=""></td>
					</tr>
					<tr class="row secretBlock serviceBlock">
						<td><label for="secret" class="control-label ">API Secret:</label></td>
						<td><input type="text" class="form-control" id="secret" name="secret" value=""></td>
					</tr>
					<tr class="row urlBlock serviceBlock">
						<td><label for="api_endpoint" class="control-label ">Url:</label></td>
						<td><input type="text" class="form-control" id="api_endpoint" name="api_endpoint" value=""></td>
					</tr>
					<tr class="row appIdBlock serviceBlock">
						<td><label for="app_id" class="control-label ">App Id:</label></td>
						<td><input type="text" class="form-control" id="app_id" name="app_id" value=""></td>
					</tr>
					<tr class="row userBlock serviceBlock">
						<td><label for="user" class="control-label ">User Login:</label></td>
						<td><input type="text" class="form-control" id="user" name="user" value=""></td>
					</tr>
					<tr class="row passBlock serviceBlock">
						<td><label for="pass" class="control-label ">Password:</label></td>
						<td><input type="password" class="form-control" id="pass" name="pass" value=""></td>
					</tr>

					<tr class="row redirectBlock hide serviceBlock">
						<td colspan="2">
							<p>After submitting form you will be redirected for authorization.</p>
						</td>
					</tr>
				</table>
			</form>
		</div>

		<div class="modal-footer">
			<button type="button" class="button button-primary save-connection">Save</button>
		</div>
	</div>

	<script>
		var emailServiceTypes = <?php echo json_encode($types->connection_types); ?>;
		var formData = <?php echo json_encode($list); ?>;
	</script>
</div>