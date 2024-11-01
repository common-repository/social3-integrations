<?php add_thickbox(); ?>

<div class="wrap page_right-sidebar social3">
	<h2>
		<?php echo __('Audience Builder', 'social3'); ?>
		<a href="?page=s3_menu_list_builder&action=new" class="page-title-action"><?php echo __('Add Form', 'social3'); ?></a>
	</h2>

    <br>

    <div class="form-list">
        <?php foreach($lists as $list) : ?>
        <div id="list<?php echo $list->id; ?>" class="form-list__item form-item">
            <div class="form-item__labels">
                <div class="form-item__label form-item__label_green <?php echo $list->status ? '' : 'hide'; ?> status-active">Active</div>
                <div class="form-item__label form-item__label_red <?php echo $list->status ? 'hide' : ''; ?> status-paused">Paused</div>
                <?php if(count($list->test)) : ?>
                    <div class="form-item__label form-item__label_blue">A/B Testing</div>
                <?php endif; ?>
            </div>
            <div class="form-item__header">
                <div class="form-item__title">Form: <?php echo $list->name; ?>
                    <span class="enabled-indicator <?php echo $list->status ? '_enabled' : '_disabled'; ?>"></span>
                </div>
                <div class="form-item__actions form-actions">
                    <div class="form-actions__item">
                        <button class="pause-btn button button_light button_red change-status-row <?php echo $list->status ? '' : 'hide'; ?>"
                                data-form-id="<?php echo $list->id; ?>" data-action="disable">
                            <i class="icon-media-pause dashicons dashicons-controls-pause"></i>Pause Form
                        </button>
                        <button class="play-btn button button_light button_green change-status-row <?php echo $list->status ? 'hide' : ''; ?>"
                                data-form-id="<?php echo $list->id; ?>" data-action="active">
                            <i class="icon-play dashicons dashicons-controls-play"></i>Start Form
                        </button>
                    </div>
                    <div class="form-actions__item">
                        <button class="button button_light button_grey js-edit-form" data-id="<?php echo $list->id; ?>">
                            <i class="icon-pencil dashicons dashicons-edit"></i>Edit
                        </button>
                        <div class="form-actions__nav actions-block-<?php echo $list->id; ?>">
                            <i class="triangle-icon"></i>
                            <a href="?page=s3_menu_list_builder&action=edit&form_id=<?php echo $list->id; ?>" class="form-actions__nav-item">Edit</a>
                            <a href="#" class="form-actions__nav-item list-clone" data-form-id="<?php echo $list->id; ?>">Clone</a>
                            <a href="#" class="form-actions__nav-item _delete remove-list" data-form-id="<?php echo $list->id; ?>">Delete</a>
                        </div>
                    </div>
                </div>
                <div class="form-item__accounts accounts-list">
                    <?php foreach($list->connections as $connection) : ?>
                    <div class="accounts-list__item">
                        <div class="image-wr">
                            <img src="<?php echo $connection->icon; ?>" alt="" title="<?php echo $connection->typeName; ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-item__content">
                <div class="conversions-block">
                    <div class="conversions-block__title">Conversions</div>
                    <div class="chart-block">
                        <div class="chart-block__info chart-info">
                            <div class="chart-info__item">
                                <div class="chart-info__select select2-wrapper">
                                    <select name="" class="conversions-period-select select2" data-id="<?php echo $list->id; ?>">
                                        <?php foreach($datePeriods as $name => $period) : ?>
                                        <option data-from="<?php echo $period[0]; ?>" data-to="<?php echo $period[1]; ?>"><?php echo $name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="chart-info__item">
                                <div class="chart-info__text"><span class="conversions-cnt">0</span> conversions</div>
                            </div>
                        </div>
                        <div class="chart" id="conversionsChart<?php echo $list->id; ?>"></div>
                    </div>
                    <div class="conversions-info">
                        <div class="conversions-info__item conversions-info__item_accented">
                            <span class="val"><span class="conversation-percent"><?php echo $list->conversation; ?></span>%</span>
                            <span class="option">conversion</span>
                        </div>
                        <div class="conversions-info__item">
                            <span class="val tests-cnt"><?php echo $list->tests_cnt; ?></span>
                            <span class="option">Tests</span>
                        </div>
                        <div class="conversions-info__item">
                            <span class="val clicks-cnt"><?php echo $list->conversions_cnt; ?></span>
                            <span class="option">Conversions</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="sidebar sidebar_medium">
        <button class="sidebar__button">A\B Test</button>
        <div class="test-block">
            <div class="scroll-wrapper">
                <form method="GET" action="/wp-admin/admin.php">
                    <input type="hidden" name="page" value="s3_menu_list_builder_test">
                    <input type="hidden" name="action" value="new">

                    <div class="test-block__title">Get Better Results with an A/B Test</div>
                    <div class="test-selection">
                        <div class="test-selection__title">Start with Selecting Most Effective Design</div>
                        <div class="test-selection__list selection-list">
                            <?php foreach($typesConv as $index => $data): ?>
                            <div class="selection-list__item">
                                <div class="selection-list__radio">
                                    <input type="radio" name="form_type"
                                           <?php echo ((isset($data->checked)) ? 'checked' : '') ?>
                                           id="selection<?php echo $index; ?>" value="<?php echo $index; ?>">
                                    <label for="selection<?php echo $index; ?>">
                                        <i class="icon dashicons dashicons-yes"></i>
                                    </label>
                                </div>
                                <div class="selection-list__text">
                                    <p><?php echo $data->title; ?></p>
                                    <p><?php echo $data->val; ?>% conversion rate</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="button button_full button_white test-selection__button" type="submit">Start A/B Test</button>
                </form>
            </div>
        </div>
    </div>

</div>

<div id="cloneModal" style="display:none;">
    <div class="modal-body">
        <form id="cloneForm" method="POST">
            <input type="hidden" name="form_id">

            <div class="form-group">
                <label for="key" class="control-label ">Cloned Form Name</label>
                <input type="text" class="form-control" id="name" min="1" name="name">
                <p class="help-block hide"></p>
            </div>
        </form>
    </div>

    <div class="modal-footer">
        <button type="button" class="button button-primary submit-clone-form">Clone</button>
    </div>
</div>

<script>
    ( function ( $ ) {
        "use strict";

        var ajaxWork = false,
            chartsData = [],
            chartsObject = [];

        $('body').on('click', '.remove-list', function() {
            var $this = $(this);

            if (ajaxWork) {
                return false;
            }

            if (confirm('<?php echo __('Are you sure you want to delete the Form?', 'social3'); ?>')) {
                ajaxWork = true;

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete_list_builder',
                        form_id: $this.data('form-id')
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
            var $this = $(this),
                listId = $this.data('form-id'),
                status = $this.data('action');

            if (ajaxWork) {
                return false;
            }

            ajaxWork = true;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'status_list_builder',
                    form_id: listId,
                    s3_action: status
                },
                success: function () {
                    ajaxWork = false;

                    if (status == 'active') {//disable for all
                        $('.enabled-indicator').removeClass('_enabled').addClass('_disabled');
                        $('.pause-btn').addClass('hide');
                        $('.play-btn').removeClass('hide');
                        $('.status-active').addClass('hide');
                        $('.status-paused').removeClass('hide');
                    }
                    $('#list' + listId + ' .enabled-indicator').toggleClass('_enabled').toggleClass('_disabled');
                    $('#list' + listId + ' .pause-btn').toggleClass('hide');
                    $('#list' + listId + ' .play-btn').toggleClass('hide');
                    $('#list' + listId + ' .status-active').toggleClass('hide');
                    $('#list' + listId + ' .status-paused').toggleClass('hide');
                },
                error: function (data) {
                    ajaxWork = false;

                    var errors = $.parseJSON(data.responseText);
                    if (typeof errors.message !== "undefined") {
                        alert(errors.message);
                    }
                }
            });

            return false;
        });

        $('body').on('click', '.list-clone', function() {
            var $this = $(this),
                $cloneForm = $('#cloneForm');

            $cloneForm[0].reset();
            $cloneForm.find('input[name=form_id]').val($this.data('form-id'));

            tb_show("Clone Form", "#TB_inline?inlineId=cloneModal&height=200");

            return false;
        });

        $('.submit-clone-form').click(function() {
            var $cloneForm = $('#cloneForm');

            if (ajaxWork) {
                return false;
            }

            ajaxWork = true;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'clone_list_builder',
                    form_id: $cloneForm.find('input[name=form_id]').val(),
                    name: $cloneForm.find('input[name=name]').val()
                },
                success: function () {
                    ajaxWork = false;
                    location.reload();
                }
            });
        });

        $('.conversions-period-select').change(function (el) {
            getListData($(el.target).data('id'));
        });

        function initialLoad() {
            $('.conversions-period-select').each(function () {
                getListData($(this).data('id'));
            });

            $(window).resize(throttle(function () {
                chartsData.forEach(function (item, index) {
                    drawChart(index, item);
                });
            }, 200));
        }

        function drawChart(listId, chartData) {
            if (chartData.conversions.length === 1) {
                chartData.conversions.push(['', 0]);
            }

            var dataConv = google.visualization.arrayToDataTable(chartData.conversions);
            var options = {
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
                    baseline: 'none',
                },
                curveType: 'function',
                legend: 'none',
                series: {
                    0: {color: '#57d38c'}
                }
            };
            chartsObject[listId] = new google.visualization.AreaChart(document.getElementById('conversionsChart' + listId));
            chartsObject[listId].draw(dataConv, options);
        }

        function getListData(listId) {
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'list_builder_chart_data',
                    list_id: listId,
                    conv_from: $('#list' + listId + ' .conversions-period-select').find(":selected").data('from'),
                    conv_to: $('#list' + listId + ' .conversions-period-select').find(":selected").data('to')
                },
                success: function (data) {
                    chartsData[listId] = data.chartData;
                    drawChart(listId, data.chartData);
                    $('#list' + listId + ' .conversions-cnt').text(data.conversions);
                    $('#list' + listId + ' .conversation-percent').text(data.conversation);
                },
                error: function (data) {
                    var errors = $.parseJSON(data.responseText);
                    if (typeof errors.message !== "undefined") {
                        alert(errors.message);
                    }
                }
            });
        }

        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(initialLoad);

        if ($('.js-edit-form').length) {
            var $editFormBtn = $('.js-edit-form');

            $editFormBtn.each(function () {
                var $this = $(this);
                $this.on('click', function (e) {
                    e.preventDefault();
                    if (!$this.next('.form-actions__nav').hasClass('_is-open')) {
                        $('.form-actions__nav').removeClass('_is-open');
                        $this.next('.form-actions__nav').addClass('_is-open');
                    } else {
                        $('.form-actions__nav').removeClass('_is-open');
                    }
                })
            });

            $(document).click(function (e) {
                if (!$(e.target).closest('.form-actions__item').length) {
                    $('.form-actions__nav').removeClass('_is-open');
                }
            })
        }

        if ($('.sidebar__button').length) {
            $('.sidebar__button').click(function (e) {
                $(this).parents('.sidebar').toggleClass('is-open');
            });

            $(document).on('click touchstart', function (e) {

                if (!$(e.target).closest('.sidebar').length) {
                    $('.sidebar').removeClass('is-open');
                }

            });

            $(window).resize(function () {
                $('.sidebar').removeClass('is-open');
            })
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