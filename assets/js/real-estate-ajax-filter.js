jQuery(document).ready(function ($) {
  var $form = $('#real-estate-filter-form');
  var $results = $('#real-estate-filter-results');
  var $navigation = $('#filter-navigation');

  $form.on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action', 'real_estate_ajax_filter');
    formData.append('nonce', realEstateAjaxFilter.nonce);

    ajaxRequest(formData);
  });

  function ajaxRequest(formData) {
    $.ajax({
      url: realEstateAjaxFilter.ajaxurl,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          $results.html(response.data);
          initPagination();
        }
      },
      complete: function () {
        // initPagination();
      }
    });
  }

  function initPagination() {
    var $navigation = $('#filter-navigation');
    $('#front-widget-pagination').on('click', 'a', function (e) {
      e.preventDefault();
      var page = $(this).attr('href').replace(/\D/g, "");
      var formData = new FormData($form[0]);
      formData.append('pagination-paged', page);
      formData.append('action', 'real_estate_ajax_filter');
      formData.append('nonce', realEstateAjaxFilter.nonce);
      ajaxRequest(formData);
    });
  }

  initPagination();
});