var mk_plugin_count_per_request = 10,
    mk_disable_until_server_respone = false;
(function($) {
    if ($('.mk-plugin-load-more').length == 0) {
        return false;
    }
    // Get List of Categories and Plugins
    mkGetInstalledPluginsList();
    mkGetPluginsList(mk_plugin_count_per_request);

    // Load More
    $(document).on('click', '.mk-plugin-load-more', function() {
        mkGetPluginsList(mk_plugin_count_per_request);
    });

    // Activate Plugin
    $(document).on('click', '.abb_plugin_activate', function() {
        var $btn = $(this);
        swal({
            title: mk_cp_textdomain.installing_notice,
            text: mk_language(mk_cp_textdomain.are_you_sure_you_want_to_install, [$btn.data('name')]),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#32d087",
            confirmButtonText: mk_cp_textdomain.conitune,
            closeOnConfirm: false,
            html: true,
        }, function() {
            mkActivatePlugin($btn.data('slug'));
        });
    });

    // Deactivate Plugin
    $(document).on('click', '.abb_plugin_deactivate', function() {
        event.preventDefault();
        var $btn = $(this);
        swal({
            title: mk_language(mk_cp_textdomain.important_notice, []),
            text: mk_language(mk_cp_textdomain.are_you_sure_you_want_to_remove_plugin, [$btn.data('name')]),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#32d087",
            confirmButtonText: mk_language(mk_cp_textdomain.conitune, []),
            closeOnConfirm: false,
            html: true
        }, function() {
            mkDeactivatePlugin($btn.data('slug'));
        });
    });

    // Update Plugin
    $(document).on('click', '.abb_plugin_update', function() {
        event.preventDefault();
        var $btn = $(this);
        mkUpdatePlugin($btn.data('slug'));
    });
}(jQuery));

function mkGetInstalledPluginsList() {
    mkResetSection('.mk-installed-plugins');
    var req_data = { action: 'abb_installed_plugins' }
    jQuery.post(ajaxurl, req_data, function(response) {
        console.log('Install Plugin :', req_data, ' Response :', response);
        if (response.status == true) {
            jQuery.each(response.data, function(key, val) {
                jQuery('.mk-installed-plugins').append(mkInstalledPluginTemplateGenerator(val));
            });
            mk_disable_until_server_respone = false;
        } else {
            swal("Oops ...", response.message, "error");
        }
    });
}

function mkPluginLoadingIndic($which_div, show_hide_status) {
    if (show_hide_status) {
        $which_div.removeClass('mk-addon-load-more-non-active');
    } else {
        $which_div.addClass('mk-addon-load-more-non-active');
    }
}

function mkGetPluginsList(count_number) {
    var from_number = Number(jQuery('.mk-plugin-load-more').data('from'));
    mk_disable_until_server_respone = true;
    var req_data = {
        action: 'abb_lazy_load_plugin_list',
        from: from_number,
        count: count_number,
    }
    mkPluginLoadingIndic(jQuery('.mk-plugin-load-more'), true);
    jQuery.post(ajaxurl, req_data, function(response) {
        console.log('Get Plugin :', req_data, ' Response :', response);
        mkPluginLoadingIndic(jQuery('.mk-plugin-load-more'), false);
        if (response.status == true) {
            if (response.data.length > 0) {

                // Set counter for new loading
                jQuery('.mk-plugin-load-more').data('from', from_number + response.data.length);

                // Remove load more if response is empty
                if (response.data.length < mk_plugin_count_per_request) {
                    jQuery('.mk-plugin-load-more').hide();
                } else {
                    jQuery('.mk-plugin-load-more').show();
                }

                jQuery.each(response.data, function(key, val) {
                    jQuery('.mk-api-plugins-list').append(mkApiPluginTemplateGenerator(val));
                });
                mk_disable_until_server_respone = false;
            } else {
                // Response data is empty
                jQuery('.mk-plugin-load-more').hide();
            }
        } else {
            swal("Oops ...", response.message, "error");
        }
    });
}

function mkApiPluginTemplateGenerator(data) {
    // <a class="mk-btn-border mk-blue mk-small mk-plugin-btn">Learn More</a>
    var template = '<div class="mk-plugin-tbl-row"><div class="mk-plugin-tbl-col-icon">' +
        '<img src="'+data.img_url+'" class="icon"></div><div class="mk-plugin-tbl-col-title-desc">' +
        '<div class="mk-plugin-name"><span class="mk-bold">' + data.name + '</span>' +
        '<span class="mk-plugin-subtitle">Version ' + data.version + '</span></div>' +
        '<div class="mk-plugin-desc">' + data.desc + '</div></div>' +
        '<div class="mk-plugin-tbl-col-action-btn"><a class="cp-button green small mk-plugin-btn abb_plugin_activate" data-slug="' +
        data.slug + '" data-name="' + data.name + '">Activate</a></div></div>';
    return template;
}

function mkInstalledPluginTemplateGenerator(data) {
    var btn = '',
        update_tag = '';
    if (data.update_needed == true) {
        btn += '<a href="#" class="mk-btn mk-btn-update ' +
            'abb_plugin_update" data-slug="' + data.slug + '" data-name="' + data.name + '">' +
            '<span class="mk-btn-txt">Update</span>' +
            '<span class="mk-btn-spinner"></span>' +
            '</a>';
        update_tag = '<span class="mk-plugin-update-tag">Update Available</span>';
    }

    btn += '<a href="#" class="cp-button red small mk-plugin-btn abb_plugin_deactivate" data-slug="' + data.slug + '" data-name="' + data.name + '">Remove</a>';
    var template = '<div class="mk-plugin-tbl-row"><div class="mk-plugin-tbl-col-icon">' +
        '<img src="'+data.img_url+'" class="icon"></div><div class="mk-plugin-tbl-col-title-desc">' +
        '<div class="mk-plugin-name"><span class="mk-bold">' + data.name + '</span>' +
        '<span class="mk-plugin-subtitle">Version ' + data.version + '</span>' + update_tag +
        '</div><div class="mk-plugin-desc">' + data.desc +
        '</div></div><div class="mk-plugin-tbl-col-action-btn">' + btn + '</div></div>';
    return template;
}

function mkDeactivatePlugin(plugin_slug) {
    var $btn = jQuery('.abb_plugin_deactivate[data-slug="' + plugin_slug + '"]');
    var plugin_name = $btn.data('name');
    var req_data = {
        action: 'abb_remove_plugin',
        abb_controlpanel_plugin_name: plugin_name,
        abb_controlpanel_plugin_slug: plugin_slug,
    }
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: req_data,
        dataType: "json",
        timeout: 60000,
        success: function(response) {
            console.log('Deactivate Process : ' , req_data , 'Response : ' , response);
            if (response.hasOwnProperty('status')) {
                if (response.status == true) {
                    $btn.closest('.mk-plugin-tbl-row').prependTo('.mk-api-plugins-list');
                    $btn.closest('.mk-plugin-update-tag').remove();
                    $btn.addClass('green').removeClass('red');
                    jQuery('.abb_plugin_update[data-slug="' + plugin_slug + '"]').remove();
                    $btn.html(mk_cp_textdomain.activate);
                    $btn.addClass('abb_plugin_activate').removeClass('abb_plugin_deactivate');
                    swal({
                        title: mk_cp_textdomain.deactivating_notice,
                        text: mk_language(mk_cp_textdomain.plugin_deactivate_successfully, []),
                        type: "success",
                        html: true
                    });
                    return true;
                } else {
                    // Something goes wrong in install progress
                    swal(mk_cp_textdomain.oops, response.message, "error");
                }
            } else {
                // Something goes wrong in server response
                swal(mk_cp_textdomain.oops, mk_cp_textdomain.something_wierd_happened_please_retry_again, "error");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            mkRequestErrorHandling(XMLHttpRequest, textStatus, errorThrown);
        }
    });
}

function mkActivatePlugin(plugin_slug) {
    var $btn = jQuery('.abb_plugin_activate[data-slug="' + plugin_slug + '"]');
    var plugin_name = $btn.data('name');
    var req_data = {
        action: 'abb_install_plugin',
        abb_controlpanel_plugin_name: plugin_name,
        abb_controlpanel_plugin_slug: plugin_slug,
    }
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: req_data,
        dataType: "json",
        timeout: 60000,
        success: function(response) {
            console.log('Install Plugin :', req_data, ' Response :', response);
            if (response.hasOwnProperty('status')) {
                if (response.status == true) {
                    
                    $btn.closest('.mk-plugin-tbl-row').prependTo('.mk-installed-plugins');
                    $btn.addClass('red').removeClass('green');
                    $btn.html(mk_cp_textdomain.remove);
                    $btn.addClass('abb_plugin_deactivate').removeClass('abb_plugin_activate');

                    // $btn.closest('.mk-plugin-tbl-row').remove();
                    // mkGetInstalledPluginsList();
                    swal({
                        title: mk_cp_textdomain.all_done,
                        text: mk_language(mk_cp_textdomain.plugin_is_successfully_installed, [plugin_name]),
                        type: "success",
                        html: true
                    });
                    return true;
                } else {
                    // Something goes wrong in install progress
                    swal(mk_cp_textdomain.oops, response.message, "error");
                }
            } else {
                // Something goes wrong in server response
                swal(mk_cp_textdomain.oops, mk_cp_textdomain.something_wierd_happened_please_retry_again, "error");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            mkRequestErrorHandling(XMLHttpRequest, textStatus, errorThrown);
        }
    });
}

function mkUpdatePlugin(plugin_slug) {
    var $btn = jQuery('.abb_plugin_update[data-slug="' + plugin_slug + '"]');
    var plugin_name = $btn.data('name');
    jQuery('.abb_plugin_update[data-slug="' + plugin_slug + '"]').addClass('mk-btn-updating');
    var req_data = {
        action: 'abb_update_plugin',
        abb_controlpanel_plugin_name: plugin_name,
        abb_controlpanel_plugin_slug: plugin_slug,
    }
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: req_data,
        dataType: "json",
        timeout: 60000,
        success: function(response) {
            console.log('Update Plugin :', req_data, ' Response :', response);
            if (response.hasOwnProperty('status')) {
                if (response.status == true) {
                    $btn.removeClass('mk-plugin-btn-updating');
                    $btn.closest('.mk-plugin-tbl-row').find('.mk-plugin-update-tag').slideUp("normal", function() { jQuery(this).remove(); });
                    $btn.remove();
                    return true;
                } else {
                    // Something goes wrong in install progress
                    swal(mk_language(mk_cp_textdomain.something_went_wrong, []), response.message, "error");
                }
            } else {
                // Something goes wrong in server response
                swal(mk_cp_textdomain.oops, mk_cp_textdomain.something_wierd_happened_please_retry_again, "error");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            mkRequestErrorHandling(XMLHttpRequest, textStatus, errorThrown);
        }
    });
}

function mkRequestErrorHandling(XMLHttpRequest, textStatus, errorThrown) {
    console.log(XMLHttpRequest);
    if (XMLHttpRequest.readyState == 4) {
        // HTTP error (can be checked by XMLHttpRequest.status and XMLHttpRequest.statusText)
        swal("Oops ...", XMLHttpRequest.status, "error");
    } else if (XMLHttpRequest.readyState == 0) {
        // Network error (i.e. connection refused, access denied due to CORS, etc.)
        swal("Oops ...", 'Error in network , please check your connection and try again', "error");
    } else {
        swal("Oops ...", 'Something wierd happened , please retry again', "error");
    }
}

function mkResetSection($which_section) {
    jQuery($which_section).fadeOut(300, function() {
        jQuery(this).empty().fadeIn(300);
    });
}

function mkResetFromNumber() {
    jQuery('.mk-plugin-load-more').data('from', 0);
}

/**
 * [ description]
 * 
 * @author Reza Marandi <ross@artbees.net>
 * @since 5.5
 * @package Jupiter
 * 
 * @param {string} string The string of translation we want to replace param with
 * @param {array} params The array of params we want to replace in translate text
 *
 * @return {string} Will return string of translate text after replacing params
 */

function mk_language(string, params) {
    if (typeof string == 'undefined' || string == '') {
        return;
    }
    array_len = params.length;
    if (array_len < 1) {
        return string;
    }
    indicator_len = (string.match(/{param}/g) || []).length;

    if (array_len == indicator_len) {
        jQuery.each(params, function(key, val) {
            string = string.replace('{param}', val);
        });
        return string;
    }

    // Array len and indicator lengh is not same;
    console.log('Array len and indicator lengh is not same, Contact support with ID : (3-6H1T4I) .');
    return string;
}
