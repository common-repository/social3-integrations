<div class="wrap">

	<h2>
		<?php echo __('Share Accounts', 'social3'); ?>
        <?php if (count($sharesListTable->items) == 0) :?>
		    <a href="?page=s3_menu_share_bar&action=new" class="page-title-action"><?php echo __('Add Share Account', 'social3'); ?></a>
        <?php endif; ?>
	</h2>

	<form id="s3-share-accounts-filter" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $sharesListTable->display() ?>
	</form>
	<script>
		( function ( $ ) {
			"use strict";

			var ajaxWork = false;

            // delete is disabled
			$('body').on('click', '.delete-row', function() {
				var $this = $(this);

				if (ajaxWork) {
					return false;
				}

				if (confirm('<?php echo __('Are you sure you want to delete the Share Account?', 'social3'); ?>')) {
					ajaxWork = true;

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'delete_share_account',
							account_id: $this.data('account-id')
						},
						success: function () {
							ajaxWork = false;
							location.reload();
						}
					});
				}

				return false;
			});

			$('body').on('click', '.change-status-row', function() {
				var $this = $(this);

				if (ajaxWork) {
					return false;
				}

				ajaxWork = true;

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'status_share_account',
						account_id: $this.data('account-id'),
						s3_action: $this.data('action')
					},
					success: function () {
						ajaxWork = false;
						location.reload();
					}
				});

				return false;
			});

		}( jQuery ) );
	</script>

</div>