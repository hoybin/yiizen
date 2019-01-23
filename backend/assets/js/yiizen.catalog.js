var yiizen = (function (self, $) {
    const PATH_SEPARATOR = '/';
    const NAME_LEVEL_PREFIX = '┈';

    self.catalog = {
        pathSelect: function () {
            var $catalogPath = $('#catalog-path'),
                $catalogLocation = $('#catalog-location'),
                $catalogPosition = $('#catalog-position'),
                $options = $catalogPath.find('option'),
                selfPath = $catalogPath.data('path');

            $options.each(function () {
                if (selfPath !== '0') {
                    // 禁止节点自身及其子节点成为自身的父级
                    if ($(this).val().length >= selfPath.length) {
                        if ($(this).val().indexOf(selfPath) === 0) {
                            $(this).prop('disabled', true);
                        }
                    }
                }
            });

            $catalogLocation.on('change', function () {
                if (!$(this).val() || $(this).val() === $catalogPath.val()) {
                    $catalogPosition
                        .find('label').addClass('disabled')
                        .find('input').prop('disabled', true);
                    if (!$(this).prop('disabled')) {
                        $('#catalog-position-0')
                            .prop('checked', true)
                            .prop('disabled', false)
                            .parent()
                            .removeClass('disabled');
                    }
                } else {
                    $catalogPosition
                        .find('label').removeClass('disabled')
                        .find('input').prop('disabled', false);
                }
            });

            $catalogPath.on('change', function () {
                var $_options,
                    value = $(this).val();
                value = value ? value : '0';
                $catalogLocation.empty()
                    .prop('disabled', true)
                    .data('location', value);
                $options.each(function () {
                    var regex = new RegExp('^' + value + '(' + PATH_SEPARATOR + '[0-9]+)?$');
                    if (regex.test($(this).val())) {
                        $catalogLocation.append($(this).clone());
                    }
                });

                $_options = $catalogLocation.find('option');
                if ($_options.length > 0) {
                    $_options.each(function () {
                        var regex = new RegExp(NAME_LEVEL_PREFIX, 'g');
                        $(this).html($(this).html().replace(regex, ''));
                    });

                    $catalogLocation.prop('disabled', false);
                    if ('0' !== $catalogLocation.data('location')) {
                        $catalogLocation.val($catalogLocation.data('location'));
                    } else {
                        if ($catalogLocation.find('option').last().val() !== selfPath) {
                            $catalogLocation.find('option').last().prop('selected', 'selected');
                        } else {
                            // 已禁用
                        }
                    }
                }

                $catalogLocation.trigger('change');
            }).trigger('change');
        },

        delete: function () {
            $('.ajax-delete').on('click', function(e) {
                if (confirm($(this).data('confirm'))) {
                    // self.http.debug = true;
                    self.http.url($(this).attr('href')).post();
                }
                e.preventDefault();
                return false;
            });
        },

        run: function () {
            var _this = this;
            $(function () {
                _this.pathSelect();
                _this.delete();
            });
        }
    };
    return self;
})(window.yiizen || {}, jQuery);
yiizen.catalog.run();
