define([
    'ko',
    'jquery',
    "uiComponent",
    'Magento_Catalog/js/price-utils',
    'mage/storage',
], function(ko, $, Component, priceUtils, storage) {
    'use strict';
    return Component.extend({
        defaults: {
            totalFormatted: '',
            total:0,
            countDownHours:'',
            tracks: {
                'total': true,
                'totalFormatted': true,
                'countDownHours': true
            },
        },
        initialize: function(config, element) {
            this._super();
            this.productsToConfirm = ko.observableArray();
            this.disabledButton = ko.observableArray([]);
            this.total = ko.computed(this.recalculateTotal, this);
            this.totalFormatted = ko.computed(() => priceUtils.formatPrice(this.total(),this.formatting));
            this.isCountdownEnded = ko.observable(false);
            this.countDownHours = ko.observable(this.countDownStart);
            this.startCountdown();

        },
        moveToConfirm: function(productId, data, event) {
            if (this.isCountdownEnded()) {
                return;
            }
            if (this.isDisabled(productId)) {
                this.disabledButton.remove(productId);
                this.productsToConfirm.remove(productId);
            } else {
                this.disabledButton.push(productId);
                this.productsToConfirm.push(productId);
            }
        },

        addToOrder: function(data, event) {
            if (this.isCountdownEnded()) return;
            var self = this;
            $('.success-upsell').fadeOut();
            $('.success-upsell__message--success')
                .fadeIn()
                .find('.success-upsell__message__text')
                .text(this.processingMessage);

            return storage.post(
                this.addToOrderUrl,
                JSON.stringify({
                    productIds: this.productsToConfirm(),
                    publicToken: this.publicToken,
                    orderId: this.orderId
                }),
                false
            ).then(function (response, textStatus, jqXHR) {
                var code = response.status;
                if (code === 201) {
                    var reauthorizeId = (response && response.message) ? response.message : null;
                    if (reauthorizeId) {
                        self._startStatusPolling(reauthorizeId);
                    }
                } else if (code === 200) {
                    $('.success-upsell').fadeOut();
                    $('.success-upsell__message--success')
                        .fadeIn()
                        .find('.success-upsell__message__text')
                        .text(response.message || self.successMessage);
                } else if (code === 403) {
                    self._retryCount = (self._retryCount || 0) + 1;

                    if (self._retryCount <= 2) {
                        setTimeout(function () {
                            self.addToOrder(data, event);
                        }, 2000);
                    } else {
                        $('.success-upsell__message--success')
                            .fadeIn()
                            .find('.success-upsell__message__text')
                            .text(response && response.message ? response.message : 'Request completed.');
                        self._retryCount = 0;
                    }
                } {
                    $('.success-upsell__message--success')
                        .fadeIn()
                        .find('.success-upsell__message__text')
                        .text(response && response.message ? response.message : 'Request completed.');
                }

            }).fail(function (response) {
                console.error(response);
                alert(self.errorMessage);
            });
        },

        _startStatusPolling: function(reauthorizeId) {
            if (this._statusPollInFlight) return;
            this._statusPollInFlight = true;
            this._statusPollAttempt = 0;

            this._pollStatusOnce(reauthorizeId);
        },

        _pollStatusOnce: function(reauthorizeId) {
            var self = this;
            var maxAttempts = 5;
            this._statusPollAttempt++;
            return storage.post(
                this.statusForOrderUrl,
                JSON.stringify({
                    reauthorizationId: reauthorizeId,
                    publicToken: this.publicToken,
                    orderId: this.orderId
                }),
                false
            ).then(function (response, textStatus, jqXHR) {
                var code = response && response.status ? response.status : null;

                if (code === 200) {
                    self._statusPollInFlight = false;
                    $('.success-upsell').fadeOut();
                    $('.success-upsell__message--success')
                        .fadeIn()
                        .find('.success-upsell__message__text')
                        .text(self.successMessage);
                } else if (code === 201) {
                    if (self._statusPollAttempt < maxAttempts) {
                        setTimeout(function () {
                            self._pollStatusOnce(reauthorizeId);
                        }, 2000);
                    } else {
                        self._statusPollInFlight = false;
                        $('.success-upsell__message--success')
                            .fadeIn()
                            .find('.success-upsell__message__text')
                            .text(self.processingMessage);
                    }
                } else {
                    self._statusPollInFlight = false;
                    $('.success-upsell__message--success')
                        .fadeIn()
                        .find('.success-upsell__message__text')
                        .text(response && response.message ? response.message : 'Request received.');
                }
            }).fail(function (xhr) {
                self._statusPollInFlight = false;
                console.error(xhr);
                alert('Error while checking order status');
            });
        },

        recalculateTotal: function() {
            var sum = 0
            ko.utils.arrayForEach(this.productsToConfirm(), function(productId) {
                sum += this.products[productId].price;
            }, this);
            return sum;
        },
        isDisabled: function(productId) {
            return this.disabledButton().includes(productId);
        },
        startCountdown: function() {
            let self = this;
            let timeParts = this.countDownHours().split(':').map(part => parseInt(part));
            let countDownSeconds = timeParts[0] * 3600 + timeParts[1] * 60 + timeParts[2];

            let countdownHandler = setInterval(function() {
                countDownSeconds--;

                let hours = Math.floor(countDownSeconds / 3600);
                let minutes = Math.floor((countDownSeconds % 3600) / 60);
                let seconds = countDownSeconds % 60;

                let hoursStr = String(hours).padStart(2, '0');
                let minutesStr = String(minutes).padStart(2, '0');
                let secondsStr = String(seconds).padStart(2, '0');

                self.countDownHours(hoursStr + ':' + minutesStr + ':' + secondsStr);

                if (countDownSeconds === 0) {
                    self.isCountdownEnded(true);
                    self.productsToConfirm.removeAll();
                    self.disabledButton.removeAll();
                    clearInterval(countdownHandler);
                    setTimeout(function() {
                        $('.success-upsell').fadeOut();
                    }, 2000);
                }
            }, 1000);
        },
    });
});
