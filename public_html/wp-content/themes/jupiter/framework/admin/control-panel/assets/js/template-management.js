var mk_template_count_per_request = 10,
    mk_disable_until_server_respone = false,
    mk_install_types = ['reset_db', 'upload', 'unzip', 'validate', 'plugin', 'theme_content', 'menu_locations', 'setup_pages', 'theme_options', 'theme_widget', 'finilize'],
    // mk_install_types = ['theme_content', 'menu_locations', 'setup_pages', 'theme_options', 'theme_widget', 'finilize'],
    mk_template_id = null,
    mk_template_name = null,
    mk_template_media_import_status = false;
(function($) {
    if ($('.abb-template-page-load-more').length == 0) {
        return false;
    }
    $(".hidden").hide().removeClass("hidden");
    mkGetTemplatesCategories();
    mkGetTemplatesList(mk_template_count_per_request);
    $(window).scroll(function() {
        var hT = $('.abb-template-page-load-more').offset().top,
            hH = $('.abb-template-page-load-more').outerHeight(),
            wH = $(window).height(),
            wS = $(this).scrollTop();
        if (wS > (hT + hH - wH) && mk_disable_until_server_respone === false) {
            mkGetTemplatesList(mk_template_count_per_request);
        }
    });
    $(document).on('click', '.abb_template_install', function() {
        var $btn = $(this);
        swal({
            title: mk_cp_textdomain.important_notice,
            text: mk_cp_textdomain.installing_sample_data_will_delete_all_data_on_this_website,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#32d087",
            confirmButtonText: mk_cp_textdomain.yes_install + $btn.data('name'),
            closeOnConfirm: false
        }, function() {
            swal({
                title: mk_cp_textdomain.include_images_and_videos,
                text: mk_template_language(mk_cp_textdomain.would_you_like_to_import_images_and_videos_as_preview, ['<a href="https://artbees.net/themes/docs/installing-template/" target="_blank">Learn More</a>']),
                type: "warning",
                showCancelButton: true,
                html: true,
                confirmButtonColor: "#32d087",
                confirmButtonText: mk_cp_textdomain.do_not_include,
                cancelButtonText: mk_cp_textdomain.include,
                closeOnConfirm: false,
                closeOnCancel: false,
            }, function(media_import_status) {
                mk_template_media_import_status = !media_import_status;
                swal({
                    title: "<h2>" + mk_cp_textdomain.install_sample_data + "</h2>",
                    text: '<div class="import-modal-container"><ul><li class="upload">' + mk_cp_textdomain.downloading_sample_package_data + '<span class="result-message"></span></li><li class="plugin">' + mk_cp_textdomain.install_required_plugins + '<span class="result-message"></span></li><li class="install">' + mk_cp_textdomain.install_sample_data + '<span class="result-message"></span></li></ul><div id="mk_templates_progressbar"><div></div></div></div>',
                    html: true,
                    showConfirmButton: false,
                });
                mkInstallTemplate(0, $btn.data('slug'));
            });
        });
    });
    $(document).on('click', '.abb_template_uninstall', function() {
        var $btn = $(this);
        swal({
            title: mk_cp_textdomain.important_notice,
            text: mk_cp_textdomain.uninstalling_template_will_remove_all_your_contents_and_settings,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dd5434",
            confirmButtonText: mk_cp_textdomain.yes_uninstall + $btn.data('name'),
            closeOnConfirm: false
        }, function() {
            mkUninstallTemplate($btn.data('slug'));
        });
    });
    $(document).on('change', '.mk-templates-categories', function() {
        var $select = $(this);
        mkResetGetTemplateInfo();
        mk_template_id = $select.val();
        mkGetTemplatesList(mk_template_count_per_request);
    });
    $('.mk-search-template').on('keyup', _.debounce(function(e) {
        var txt = $(this);
        mkSearchTemplateByName(txt.val());
    }, 500));
}(jQuery));

function mkUninstallTemplate(template_slug) {
    jQuery.post(ajaxurl, {
        action: 'abb_uninstall_template',
    }).done(function(response) {
        console.log('Ajax Req : ', response);
        // Cache selectors
        var $link = jQuery('a[data-slug="' + template_slug + '"]');
        var $template_list = jQuery('#template-list');
        var $installed_template = jQuery('.mk-installed-template');
        // Handle related elements
        $link.html(mk_cp_textdomain.install);
        $link.closest('.mk-template-item').remove();
        $installed_template.hide();
        // Reset Get Templtes
        mkResetGetTemplateInfo();
        mk_template_id = 'all-categories';
        mkGetTemplatesList(mk_template_count_per_request);
        // Reset Categories
        jQuery('.mk-templates-categories').val('all-categories');
        // Alert
        swal(mk_cp_textdomain.template_uninstalled, "", "success");
    }).fail(function(data) {
        console.log('Failed msg : ', data);
    });
}

function mkInstallTemplate(index, template_name) {
    if (mk_install_types[index] == undefined) {

        // Cache selectors
        var $link = jQuery('a[data-slug="' + template_name + '"]');
        var $installed_template_list = jQuery('#installed-template-list');
        var $installed_template = jQuery('.mk-installed-template');

        // Handle related elements
        $link.html(mk_cp_textdomain.uninstall);
        $link.addClass('abb_template_uninstall').removeClass('abb_template_install');
        $installed_template_list.empty();
        $link.closest('.mk-template-item').appendTo($installed_template_list).addClass('mk-installed-template-item');

        // Show installed Templates
        $installed_template.show();

        // Alert
        swal(mk_cp_textdomain.hooray, mk_cp_textdomain.template_installed_successfully, "success");

        return;
    }
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: { action: 'abb_install_template_procedure', type: mk_install_types[index], template_name: template_name, import_media: mk_template_media_import_status },
        dataType: "json",
        success: function(response) {
            console.log('Install Template - ', mk_install_types[index], ' - Fetch media : ', mk_template_media_import_status, ' : Req data - ', template_name, ' , Response - ', response);
            if (response.hasOwnProperty('status')) {
                if (response.status == true) {
                    mkProgressBar(mkCalcPercentage(mk_install_types.length - 1, index), jQuery('#mk_templates_progressbar'));
                    mkShowResult(mk_install_types[index], response.message);
                    mkInstallTemplate(++index, template_name);
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

function mkGetTemplatesCategories() {
    var empty_category_list = '<option value="no-category">No template found</option>';
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: { action: 'abb_get_templates_categories' },
        dataType: "json",
        timeout: 60000,
        success: function(response) {
            if (response.hasOwnProperty('status') === true) {
                if (response.status === true) {
                    var category_list = '<option value="all-categories">All Categories</option>';
                    jQuery.each(response.data, function(key, val) {
                        category_list += '<option value="' + val.id + '">' + val.name + ' - ' + val.count + '</option>';
                    });
                    jQuery('.mk-templates-categories').html(category_list);
                } else {
                    jQuery('.mk-templates-categories').html(empty_category_list);
                    swal("Oops ...", response.message, "error");
                }
            } else {
                jQuery('.mk-templates-categories').html(empty_category_list);
                swal(mk_cp_textdomain.oops, mk_cp_textdomain.something_wierd_happened_please_retry_again, "error");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jQuery('.mk-templates-categories').html(empty_category_list);
            mkRequestErrorHandling(XMLHttpRequest, textStatus, errorThrown);
        }
    });
}

function mkShowResult(type, message) {
    message = '-    ' + message;
    switch (type) {
        case 'reset_db':
            jQuery('.import-modal-container .upload .result-message').text(message);
            break;
        case 'upload':
            jQuery('.import-modal-container .upload .result-message').text(message);
            break;
        case 'unzip':
            jQuery('.import-modal-container .upload .result-message').text(message);
            break;
        case 'validate':
            jQuery('.import-modal-container .upload .result-message').text(message);
            jQuery('.import-modal-container .upload').addClass('mk-done');
            break;
        case 'plugin':
            jQuery('.import-modal-container .plugin .result-message').text(message);
            jQuery('.import-modal-container .plugin').addClass('mk-done');
            break;
        case 'theme_content':
            jQuery('.import-modal-container .install .result-message').text(message);
            break;
        case 'menu_locations':
            jQuery('.import-modal-container .install .result-message').text(message);
            break;
        case 'setup_pages':
            jQuery('.import-modal-container .install .result-message').text(message);
            break;
        case 'theme_options':
            jQuery('.import-modal-container .install .result-message').text(message);
            break;
        case 'theme_widget':
            jQuery('.import-modal-container .install .result-message').text(message);
            break;
        case 'finilize':
            jQuery('.import-modal-container .install .result-message').text(message);
            jQuery('.import-modal-container .install').addClass('mk-done');
            break;
    }
}

function mkCalcPercentage(bigNumber, littleNumber) {
    return Math.round((littleNumber * 100) / bigNumber);
}

function mkGetTemplatesList(count_number) {
    var from_number = Number(jQuery('.abb-template-page-load-more').data('from'));
    mk_disable_until_server_respone = true;
    var req_data = {
        action: 'abb_template_lazy_load',
        from: from_number,
        count: count_number,
    }
    if (typeof mk_template_id !== 'undefined' && mk_template_id !== null) {
        req_data['template_category'] = mk_template_id;
    }
    if (typeof mk_template_name !== 'undefined' && mk_template_name !== null) {
        req_data['template_name'] = mk_template_name;
    }
    var $spinner = jQuery('<div class="mk-template-loading-spinner"><div class="mk-loading-spinner"></div></div>');
    jQuery('#template-list').append($spinner);
    console.log(req_data);
    jQuery.post(ajaxurl, req_data, function(response) {
        $spinner.remove();
        if (response.status == true) {
            if (response.data.length > 0) {
                jQuery('.abb-template-page-load-more').data('from', from_number + response.data.length);
                jQuery.each(response.data, function(key, val) {
                    // If is intalled but it's not in the page
                    if (val.installed == true && jQuery('.mk-installed-template-item').length == 0) {
                        jQuery('.mk-installed-template').show();
                        jQuery('#installed-template-list').empty().append(mkTemplateGenerator(val));
                        // Reset Get Templtes
                        mkResetGetTemplateInfo();
                        mk_template_id = 'all-categories';
                        mkGetTemplatesList(mk_template_count_per_request);
                        // If is NOT intalled
                    } else if (val.installed != true) {
                        jQuery('#template-list').append(mkTemplateGenerator(val));
                    }
                });
                mk_disable_until_server_respone = false;
            }
        } else {
            console.log(response);
            swal("Oops...", response.message, "error");
        }
    });
}

function mkTemplateGenerator(data) {
    if (data.installed == false) {
        var btn =
            '<a class="abb_template_install mk-template-item-btn mk-template-item-btn-action" data-name="' + data.name + '" data-slug="' + data.name + '">' +
            mk_cp_textdomain.install +
            '</a>' +
            '<a class="mk-template-item-btn mk-template-item-btn-preview" href="http://demos.artbees.net/jupiter5/' + data.name + '" target="_blank">' +
            mk_cp_textdomain.preview +
            '</a>';
    } else {
        var btn =
            '<a class="mk-template-item-btn mk-template-item-btn-action abb_template_uninstall" data-name="' + data.name + '" data-slug="' + data.name + '">' +
            mk_cp_textdomain.uninstall +
            '</a>' +
            '<a class="mk-template-item-btn mk-template-item-btn-preview" href="http://demos.artbees.net/jupiter5/' + data.name + '" target="_blank">' +
            mk_cp_textdomain.preview +
            '</a>';
    }
    if (data.installed == false) {
        var template =
            '<div class="mk-template-item">' +
            '<div class="mk-template-item-inner">' +
            '<form method="post">' +
            '<figure class="mk-template-item-fig">' +
            '<img src="' + data.img_url + '" alt="' + data.name + '">' +
            '</figure>' +
            '<div class="mk-template-item-desc">' +
            '<h4 class="mk-template-item-title">' + data.name + '</h4>' +
            '<div class="mk-template-item-buttons">' + btn + '</div>' +
            '</div>' +
            '</form>' +
            '</div>' +
            '</div>';
    } else {
        var template =
            '<div class="mk-template-item mk-installed-template-item">' +
            '<div class="mk-template-item-inner">' +
            '<form method="post">' +
            '<figure class="mk-template-item-fig">' +
            '<img src="' + data.img_url + '" alt="' + data.name + '">' +
            '</figure>' +
            '<div class="mk-template-item-desc">' +
            '<h4 class="mk-template-item-title">' + data.name + '<span class="mk-template-item-subtitle">Installed</span>' + '</h4>' +
            '<div class="mk-template-item-buttons">' + btn + '</div>' +
            '</div>' +
            '</form>' +
            '</div>' +
            '</div>';
    }
    return template;
}

function mkProgressBar(percent, $element) {
    var progressBarWidth = percent * $element.width() / 100;
    $element.find('div').animate({ width: progressBarWidth }, 500).html(percent + "% ");
}

function mkRequestErrorHandling(XMLHttpRequest, textStatus, errorThrown) {
    console.log(XMLHttpRequest.responseText);
    if (XMLHttpRequest.readyState == 4) {
        // HTTP error (can be checked by XMLHttpRequest.status and XMLHttpRequest.statusText)
        swal("Oops ...", 'Error in API (' + XMLHttpRequest.status + ')', "error");
    } else if (XMLHttpRequest.readyState == 0) {
        // Network error (i.e. connection refused, access denied due to CORS, etc.)
        swal("Oops ...", 'Error in network , please check your connection and try again', "error");
    } else {
        swal("Oops ...", 'Something wierd happened , please retry again', "error");
    }
}

function mkResetGetTemplateInfo() {
    jQuery("#template-list").fadeOut(300, function() {
        jQuery(this).empty().fadeIn(300);
    });
    jQuery('.abb-template-page-load-more').data('from', 0);
}

function mkSearchTemplateByName(template_name) {
    mkResetGetTemplateInfo();
    mk_template_name = template_name;
    mkGetTemplatesList(mk_template_count_per_request);
}

function mk_template_language(string, params) {
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
