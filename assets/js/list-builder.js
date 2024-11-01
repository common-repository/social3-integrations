/**
 * Created by dvasilevskiy on 25.05.18.
 */
( function ( $ ) {
    "use strict";

    var $previewPlacement = $('select[name="form[form_placement]"]').parents(".preview-settings__row");

    $('.list-design-label input').on( 'change', function() {
        $('.list-design-label').removeClass('checked');
        $(this).closest('label').addClass('checked');
    });

    $('select[name="form[form_type]"]').on('change', function () {
        setPlacement();
    });

    $('.nav-tabs a').click(function () {
        var $this = $(this);

        $this.closest('.nav-tabs').find('li').removeClass('active');
        $this.closest('.tabs').find('.tab-pane').removeClass('active');

        $this.closest('li').addClass('active');
        $this.closest('.tabs').find('.tab-pane' + $this.attr('href')).addClass('active');

        return false;
    });

    function setPlacement() {
        if ($('select[name="form[form_type]"]').val() == 3) {
            $previewPlacement.removeClass("hidden").show();
        } else {
            $previewPlacement.hide();
        }
    }

    setPlacement();

    function editMode() {
        if ($('#form_id').val() > 0) {
            return true;
        }
        return false;
    }

    function connectionChange(connectionId, type) {
        if (connectionId) {
            var added = $('input[data-connection-id='+connectionId+']').is(':checked');
            if (added) {
                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        action: 'get_connection_lists',
                        connection_id: connectionId
                    },
                    success: function (data) {
                        drawListSelect(connectionId, data.data, type);

                        if (formData.connections) {
                            var connections = JSON.parse(formData.connections.replace(/\\/g, ''));

                            for (var key in connections) {
                                if (connectionId == connections[key].connection_id) {
                                    $('#list_name_'+connectionId).val(connections[key].list_id).trigger('change');
                                }
                            }
                        }
                    },
                    error: function (data) {
                        var errors = $.parseJSON(data.responseText);

                        if (typeof errors.message !== "undefined") {
                            alert(errors.message);
                        }
                    }
                });
            } else {
                deselectService(connectionId);
            }
        }
    }

    function drawListSelect(connectionId, lists, type) {
        for (var key in emailServiceTypes) {
            if(type == emailServiceTypes[key]) {
                var name = key;
            }
        }
        var html = '<div class="form-group list-name-block select-list-block'+connectionId+'">'+
            '<label>List from '+name+'</label>'+
            '<div class="select2-wrapper custom-select2">'+
            '<select id="list_name_'+connectionId+'" data-connection-id="'+connectionId+'" class="select2"></select>'+
            '</div>'+
            '</div>';

        $('#selectListsBlock').append(html);
        populateSelect($('#list_name_'+connectionId), lists);
        updateConnectionsData();
    }

    function updateConnectionsData() {
        var connections = [];
        $('#selectListsBlock select').each(function() {
            var connectionId = $(this).data('connection-id');
            connections.push({
                connection_id: connectionId,
                list_id: $(this).val(),
                list_name: $(this).find(':selected').text()
            });
        });

        var json = JSON.stringify(connections);
        $('input[name="form[connections]"]').val(json);
    }

    function deselectService(connectionId) {
        $('.select-list-block' + connectionId).remove();
        updateConnectionsData();
    }

    function populateSelect(el, values) {
        el.empty();
        $.each(values, function (index, text) {
            el.append('<option value=' + text + '>' + index + '</option>');
        });
        el.select2({
            minimumResultsForSearch: -1
        });
    }

    $(document).on("change", '#selectListsBlock select', function() {
        updateConnectionsData();
    });

    $('.subscribe-email-client .radio-field input').click(function () {
        if ($(this).prop('checked')) {
            $(this).closest('.subscribe-email-client:not(.m-disabled)').addClass('is-checked');
        } else {
            $(this).closest('.subscribe-email-client').removeClass('is-checked');
        }

        var connectionId = $(this).data('connection-id');
        var type = $(this).val();
        if (connectionId > 0) {
            connectionChange(connectionId, type);
        } else {
            showFieldsByType(type);

            tb_show("", "#TB_inline?inlineId=connectionModal");
        }
    });

    if (formData.email_service_connection_id > 0) {
        $('.subscribe-email-client .radio-field input[data-connection-id='+formData.email_service_connection_id+']').trigger('click');
    }

    if (formData.connections) {
        var connections = JSON.parse(formData.connections.replace(/\\/g, ''));
        for (var index in connections) {
            $('.subscribe-email-client .radio-field input[data-connection-id='+connections[index].connection_id+']').trigger('click');
        }
    }

    function showFieldsByType(type) {
        $('.serviceBlock').addClass('hide');

        if (
            type == emailServiceTypes['infusionsoft'] ||
            type == emailServiceTypes['constantcontact'] ||
            type == emailServiceTypes['verticalresponse'] ||
            type == emailServiceTypes['mailchimp']
        ) {
            $('.redirectBlock').removeClass('hide');
        } else if (type == emailServiceTypes['icontact']) {
            $('.appIdBlock').removeClass('hide');
            $('.userBlock').removeClass('hide');
            $('.passBlock').removeClass('hide');
        } else if (
            type == emailServiceTypes['getresponse'] ||
            type == emailServiceTypes['hubspot'] ||
            type == emailServiceTypes['mailerlite']
        ) {
            $('.keyBlock').removeClass('hide');
        } else if (type == emailServiceTypes['activecampaign']) {
            $('.keyBlock').removeClass('hide');
            $('.urlBlock').removeClass('hide');
        } else if (type == emailServiceTypes['mailjet']) {
            $('.keyBlock').removeClass('hide');
            $('.secretBlock').removeClass('hide');
        } else if (type == emailServiceTypes['madmimi']) {
            $('.emailBlock').removeClass('hide');
            $('.keyBlock').removeClass('hide');
        }
    }

    $('.save-connection').click(function () {
        connectionFormSubmit();
    });

    function connectionFormSubmit() {
        var type = $('input[name=connection_type]:checked').val();
        if (
            type == emailServiceTypes['infusionsoft'] ||
            type == emailServiceTypes['mailchimp'] ||
            type == emailServiceTypes['verticalresponse'] ||
            type == emailServiceTypes['constantcontact']
        ) {
            $('#connectionForm input[name=type]').val(type);
            $('#connectionForm input[name=all_data]').val($('#subscriptionForm').serialize());
            saveConnection(type);
        } else {
            saveConnection(type);
        }
    }

    function saveConnection(type) {
        var data = $('#connectionForm').serialize() + '&type=' + type + '&action=save_connection';

        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: 'json',
            data: data,
            success: function (data) {
                tb_remove();

                if (data.connection) {
                    $('#email_service_connection_id').val(data.connection.id);
                    connectionChange();
                    $('#connection_type_' + type).removeClass('m-disabled').addClass('is-checked');
                    $('#connection_type_' + type + ' input[name=connection_type]').attr('data-connection-id', data.connection.id)
                        .data('connection-id', data.connection.id);

                    $('#connectionForm')[0].reset();
                } else if (data.redirect) {
                    location.href = data.redirect;
                }
            },
            error: function (data) {
                var errors = $.parseJSON(data.responseText);
                if (typeof errors.message_alert !== "undefined") {
                    alert(errors.message_alert);
                } else if (typeof errors.message !== "undefined") {
                    alert(errors.message);
                }
            }
        });
    }

    $('.remove-connection').click(function(event) {
        event.preventDefault();

        if (confirm('Are you sure you want to delete the Form?')) {

            var $this = $(this),
                $checkbox = $this.closest('.subscribe-email-client').find('input');

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'remove_connection',
                    id: $checkbox.data('connection-id')
                },
                success: function (data) {
                    $this.closest('.subscribe-email-client').removeClass('is-checked').addClass('m-disabled');
                    $this.hide();

                    deselectService($checkbox.data('connection-id'));

                    console.log($checkbox);

                    $checkbox.data('connection-id', null);
                    $checkbox.prop('checked', false);

                },
                error: function (data) {
                    var errors = $.parseJSON(data.responseText);
                    if (typeof errors.message !== "undefined") {
                        alert(errors.message);
                    }
                }
            });
        }
    });

    $('.preview-settings__row-title').each(function () {
        var $this = $(this);

        $this.on('click', function () {
            $this.closest('.preview-settings__row').toggleClass('_is-open');
        });
    });

    $('.preview-settings__row .switch-block input[type="checkbox"]').click(function () {
        $(this).closest('.preview-settings__row').find('.preview-settings__switcher-subblock').toggleClass('is-active');
    });

    $('.preview-settings__row .switch-block input[type="checkbox"]').trigger('click');

    function fillForm() {
        $.each(formData, function (i, item) {
           if (i === 'rulesetData') {
                for (var k in item) {
                    if (ruleSetFields.indexOf(k) !== -1) {
                        fillRuleFormItem(k, item[k]);
                    }
                }
            }
        });
    }

    setTimeout(function () {
        fillForm();
    }, 100);

    var $defaultDate;

    $('input.timepicker ').each(function (i, elem) {
        if ($(elem).val().trim() == '') {
            if ($(elem).hasClass('default_value_current')) {
                $defaultDate = moment(new Date());
            } else {
                $defaultDate = "";
            }
            $(elem).datetimepicker({
                format: "h:mm A",
                defaultDate: $defaultDate
            }).on('dp.change', function (e) {
                if (!e.oldDate || !e.date.isSame(e.oldDate, 'day')) {
                    $(this).data('DateTimePicker').hide();
                }
            });
        } else {
            var defaultDate = moment(new Date($(elem).val()));

            $(elem).datetimepicker({
                useCurrent: false,
                defaultDate: defaultDate,
                format: "h:mm A",
            }).on('dp.change', function (e) {
                if (!e.oldDate || !e.date.isSame(e.oldDate, 'day')) {
                    $(this).data('DateTimePicker').hide();
                }
            });
        }
    });

    $('.select2').each(function () {
        var $this = $(this);
        $this.select2({
            minimumResultsForSearch: -1,
            dropdownParent: $this.closest('.select2-wrapper')
        });
    });

}( jQuery ) );

function updateSelects($selector)
{
    $selector.each(function() {
        var $this = $(this);
        $this.select2({
            minimumResultsForSearch: -1,
            dropdownParent: $this.closest('.select2-wrapper')
        });
    });
}