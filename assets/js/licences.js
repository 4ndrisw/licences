// Init single licence
function init_licence(id) {
    load_small_table_item(id, '#licence', 'licenceid', 'licences/get_licence_data_ajax', '.table-licences');
}


// Validates licence add/edit form
function validate_licence_form(selector) {

    selector = typeof (selector) == 'undefined' ? '#licence-form' : selector;

    appValidateForm($(selector), {
        clientid: {
            required: {
                depends: function () {
                    var customerRemoved = $('select#clientid').hasClass('customer-removed');
                    return !customerRemoved;
                }
            }
        },
        date: 'required',
        office_id: 'required',
        number: {
            required: true
        }
    });

    $("body").find('input[name="number"]').rules('add', {
        remote: {
            url: admin_url + "licences/validate_licence_number",
            type: 'post',
            data: {
                number: function () {
                    return $('input[name="number"]').val();
                },
                isedit: function () {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function () {
                    return $('input[name="number"]').data('original-number');
                },
                date: function () {
                    return $('body').find('.licence input[name="date"]').val();
                },
            }
        },
        messages: {
            remote: app.lang.licence_number_exists,
        }
    });

}


// Get the preview main values
function get_licence_item_preview_values() {
    var response = {};
    response.description = $('.main textarea[name="description"]').val();
    response.long_description = $('.main textarea[name="long_description"]').val();
    response.qty = $('.main input[name="quantity"]').val();
    return response;
}

// From licence table mark as
function licence_mark_as(status_id, licence_id) {
    var data = {};
    data.status = status_id;
    data.licenceid = licence_id;
    $.post(admin_url + 'licences/update_licence_status', data).done(function (response) {
        //table_licences.DataTable().ajax.reload(null, false);
        reload_licences_tables();
    });
}

// Reload all licences possible table where the table data needs to be refreshed after an action is performed on task.
function reload_licences_tables() {
    var av_licences_tables = ['.table-licences', '.table-program_items', '.table-inspection_items' ,'.table-licence_items'];
    $.each(av_licences_tables, function (i, selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().ajax.reload(null, false);
        }
    });
}


function licences_add_licence_item(licence_id, id) {
    var data = {};
    data.licence_id = licence_id;
    data.id = id;
    console.log(data);
    $.post(admin_url + 'licences/add_licence_item', data).done(function (response) {
        reload_licences_tables();
    });
}

function licences_remove_licence_item(id) {
    var data = {};
    data.id = id;
    console.log(data);
    $.post(admin_url + 'licences/remove_licence_item', data).done(function (response) {
        reload_licences_tables();
    });
}


function licences_load_licence_template(id) {
    var data = {};
    data.id = id;
    console.log(data);
    $.post(admin_url + 'licences/load_licence_template', data).done(function (response) {
        //reload_licences_tables();
    });
}

function licences_add_licence_item_number(id){
    var data = {};
    data.id = id;
    console.log(data);
    $.post(admin_url + 'licences/add_licence_item_number', data).done(function (response) {
        reload_licences_tables();
    });
}

// Init licence modal and get data from server
function init_licence_items_modal(licence_id, jenis_pesawat_id) {
  var queryStr = "";
  var $leadModal = $("#lead-modal");
  var $licenceAddEditModal = $("#_licence_modal");
  if ($leadModal.is(":visible")) {
    queryStr +=
      "?opened_from_lead_id=" + $leadModal.find('input[name="leadid"]').val();
    $leadModal.modal("hide");
  } else if ($licenceAddEditModal.attr("data-lead-id") != undefined) {
    queryStr +=
      "?opened_from_lead_id=" + $licenceAddEditModal.attr("data-lead-id");
  }

  requestGet(admin_url + "licences/get_licence_item_data/" + licence_id + '/' + jenis_pesawat_id)
    .done(function (response) {
      _licence_append_html(response);
      /*
      if (typeof jenis_pesawat_id != "undefined") {
        setTimeout(function () {
          $('[data-licence-jenis_pesawat-href-id="' + jenis_pesawat_id + '"]').click();
        }, 1000);
      }
      */

    })
    .fail(function (data) {
      $("#licence-modal").modal("hide");
      alert_float("danger", data.responseText);
    });
}

// General function to append licence html returned from request
function _licence_append_html(html) {
  var $licenceModal = $("#licence-modal");

  $licenceModal.find(".data").html(html);
  //init_licences_checklist_items(false, licence_id);
  //recalculate_checklist_items_progress();
  //do_licence_checklist_items_height();

  setTimeout(function () {
    $licenceModal.modal("show");
    // Init_tags_input is trigged too when licence modal is shown
    // This line prevents triggering twice.
    if ($licenceModal.is(":visible")) {
      init_tags_inputs();
    }
    //init_form_reminder("licence");
    //fix_licence_modal_left_col_height();

    // Show the comment area on mobile when licence modal is opened
    // Because the user may want only to upload file, but if the comment textarea is not focused the dropzone won't be shown

    if (is_mobile()) {
      //init_new_licence_comment(true);
    }
  }, 150);
}