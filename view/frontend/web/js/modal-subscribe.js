define(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
    'use strict';

    return function initSpaModal() {
        var $modal = $('#octocub-spa-modal');
        if (!$modal.length) return;

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Back in Stock Alert',
            buttons: []
        };

        modal(options, $modal);

        function open(productId, productName) {
            $modal.find('input[name="product_id"]').val(productId);
            $modal.find('.octocub-spa-title').text('Subscribe for: ' + (productName || 'Product'));

            var required = window.OCTOCUB_SPA && window.OCTOCUB_SPA.consentRequired;
            var text = window.OCTOCUB_SPA && window.OCTOCUB_SPA.consentText;

            if (required) {
                $modal.find('[data-consent-wrapper]').show();
                $modal.find('[data-consent-text]').text(text || 'I agree to receive alerts.');
            } else {
                $modal.find('[data-consent-wrapper]').hide();
            }

            $modal.modal('openModal');
        }

        $(document).on('click', '.octocub-spa-btn', function () {
            var productId = $(this).data('product-id');
            var productName = $(this).data('product-name');
            open(productId, productName);
        });

        $(document).on('submit', '#octocub-spa-form', function (e) {
            e.preventDefault();

            var url = window.OCTOCUB_SPA && window.OCTOCUB_SPA.subscribeUrl;
            var data = $(this).serialize();

            $.post(url, data, function (res) {
                alert(res && res.message ? res.message : 'Done');
                if (res && res.success) {
                    $modal.modal('closeModal');
                }
            }).fail(function () {
                alert('Failed. Please try again.');
            });
        });
    };
});
