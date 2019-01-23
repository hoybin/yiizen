var yiizen = (function (self, $) {
    var _ajax = function (http, done) {
        return $.ajax(http.params).then(
            function(result, textStatus, jqXHR) {
                http.debug && console.info(textStatus);
                http.debug && console.info(result);
                return jqXHR;
            },
            function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus);
                console.error(errorThrown);
                return jqXHR;
            }
        ).done(done || function(result, textStatus, jqXHR) {
            var promptResult = '';
            if (result.msg) {
                window.alert(result.msg);
            } else if(result.confirm) {
                if (window.confirm(result.confirm)) {
                    return http
                        .url(result.url || http.params.url)
                        .data($.extend(http.params.data, result.data))
                        .post();
                }
            } else if(result.prompt) {
                promptResult = window.prompt(result.prompt, promptResult);
                if (promptResult !== null) {
                    return http
                        .url(result.url || http.params.url)
                        .data($.extend(http.params.data, result.data, {prompt: promptResult}))
                        .post();
                }
            }
            
            if(result.navUrl) {
                if(http.debug) {
                    window.alert('debug: click ok and redirect to: ' + result.navUrl);
                }
                location.replace(result.navUrl);
            } else if (result.reload) {
                if(http.debug) {
                    window.alert('debug: click ok to reload the page');
                }
                location.reload(true);
            }

            return jqXHR;
        });
    };

    self.http = {
        debug: false,
        params: {
            // contentType: 'application/x-www-form-urlencoded; charset=UTF-8', // default
            // contentType: 'application/json; charset=UTF-8',
            data: {},
            dataType : 'json'
        },
        contentType: function (contentType) {
            this.params.contentType = contentType;
            return this;
        },
        data: function (data) {
            this.params.data = data;
            return this;
        },
        dataType: function (dataType) {
            this.params.dataType = dataType;
            return this;
        },
        url: function (url) {
            url = url.replace(/[?&]_0\.[\d]+/, '');
            this.params.url = url + (url.indexOf('?') ? '&_' : '?_') + Math.random();
            return this;
        },
        get: function (done) {
            this.params.type = 'GET';
            return _ajax(this, done);
        },
        post: function (done) {
            this.params.type = 'POST';
            return _ajax(this, done);
        }
    };
    return self;
})(window.yiizen || {}, jQuery);
