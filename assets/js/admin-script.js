// jQuery(document).ready(function ($) {
// 	$(document).on('click', '[data-plugin="flexi-real-estate/flexi-real-estate.php"] .deactivate a', function(e) {
// 		e.preventDefault();
// 		var url = $(this).attr('href');
// 		console.log('url', url);

// 		if ( confirm('Do you want to keep the Real Estate custom post type and custom fields?') ) {
// 			// User chose to keep data
// 			$.post(ajaxurl, {
// 				action: 'flexi_real_estate_keep_data',
// 				_ajax_nonce: flexiRealEstateAdmin.nonce
// 			}, function() {
// 				window.location.href = url;
// 			});
// 		} else {
// 			// User chose to remove data
// 			window.location.href = url;
// 		}
// 	});
// });


jQuery(document).ready(function ($) {
  // Delete custom post type and taxonomy when plugin is uninstalled
  $('#flexi-real-estate-uninstall').on('click', function (e) {
    e.preventDefault();

    // Confirm deletion
    if (confirm('Are you sure you want to delete the custom post type "real-estate" and taxonomy "district"?')) {
      // Send AJAX request to delete custom post type and taxonomy
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          action: 'flexi_real_estate_uninstall',
          post_type: 'real-estate',
          taxonomy: 'district',
        },
        success: function (response) {
          console.log(response);
          // Reload page after deletion
          window.location.reload();
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
        },
      });
    }
  });
});