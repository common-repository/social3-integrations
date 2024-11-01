/**
 * Created by dvasilevskiy on 25.05.18.
 */
var ruleSetFields = [
    'url_mode_data',
    'scrolling_down',
    'new_return',
    'on_pageview',
    'device',
    'traffic_sources',
    'after_seconds',
    'inactivity_seconds',
    'visitor_time',
    'on_exit',
];

function formRule(name) {
    this.name = name;
    this.$container = $('#' + name + '_rule');

    /**
     *
     * @param {object} values (el name => value)
     * @returns {undefined}
     */
    this.addCondition = function(values) {
        var condition = rulesetParams.conditionsHTML[this.name];
        this.$container.find('.conditions-container').append(condition);
        for (key in values) {
            this.$container.find('.conditions-block__item:last [name="'+key+'"]').val(values[key]).trigger('change');
        }
        updateSelects(this.$container.find('.conditions-block__item:last .select2'));
    }
}

function fillRuleFormItem(name, val) {
    this.check = function(name) {
        if (!$('input[name="form['+name+']"]').is(':checked')) {
            $('label[for='+name+']').trigger("click");
        }
    };

    if (name === 'url_mode_data' && val) {
        this.check('url_mode_enabled');
        if (Array.isArray(val)) {
            val.forEach(function (element) {
                urlRule.addCondition({'form[url_mode][]' : element.url_mode, 'form[url_pattern][]' : element.url_pattern})
            });
        }
    } else if (name === 'traffic_sources' && val) {
        this.check('traffic_source_enabled');
        if (Array.isArray(val)) {
            val.forEach(function (element) {
                trafficRule.addCondition({'form[traffic_source][]' : element})
            });
        }
    } else if (name === 'visitor_time' && val) {
        this.check('visitor_time_enabled');
        $('[name="form[visitor_time_from]"]').val(val[0]);
        $('[name="form[visitor_time_to]"]').val(val[1]);
    } else if (val) {
        this.check(name + '_enabled');
        if ($('[name="form['+name+']"]').length) {
            $('[name="form['+name+']"]').val(val).trigger('change');
        }
    }
}

var urlRule = new formRule('url_mode');
var trafficRule = new formRule('traffic_source');

jQuery(document).ready(function () {

    $(document).on("change", '.rule-url-mode-select', function() {
        if (rulesetParams.showUrlPatternOn.indexOf($(this).val()) !== -1) {
            $(this).parents('.conditions-block__item').find('.rule-url-mode-pattern').removeClass('hide');
        } else {
            $(this).parents('.conditions-block__item').find('.rule-url-mode-pattern').addClass('hide');
        }
    });

    if ( $('.conditions-block').length ) {

        $('.conditions-block__btn').each(function () {

            if(!$(this).closest('.conditions-block').hasClass('_is-open')) {
                $(this).next('.conditions-block__content').hide();
            } else {
                $(this).next('.conditions-block__content').show();
            }

        });

        $('.conditions-block__btn').each(function () {
            $(this).on('click', function (e) {
                e.preventDefault();
                if(!$(this).closest('.conditions-block').hasClass('_is-open')) {
                    $(this).closest('.conditions-block').addClass('_is-open');
                    $(this).html('Hide conditions');
                    $(this).next('.conditions-block__content').slideDown();
                } else {
                    $(this).closest('.conditions-block').removeClass('_is-open');
                    $(this).html('Show conditions');
                    $(this).next('.conditions-block__content').slideUp();
                }
            })
        })
    }

});