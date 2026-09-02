// complete order row clicked
$(document).on("click", ".completed-order-triggerer", function () {
    var orderid = $(this).data('orderid');
    $.ajax({
        "url": "/Orders/getDriverAndOrderDetail",
        "method": "GET",
        "dataType": "json",
        "data": {
            "orderid": orderid
        },
        success: function (response) {
            if (response) {
                // hide start and end time for new and planned order
                if (response.data.status == 0 || response.data.status == 1) {
                    $('#order_start_end_times').addClass('d-none');
                } else {
                    $('#order_start_end_times').removeClass('d-none');
                }
                $("#request").text(response.data.order_created_at ?? '00-00-00');
                $("#approve").text(response.data.order_planned_at ?? '00-00-00');
                $("#work-start").text(response.data.order_progress_at ?? '00-00-00');
                $("#work-complete").text(response.data.order_completed_at ?? '00-00-00');
                $("#detailedOrderModal #start_time").text(response.data.order_created_at_time ?? '00-00');
                $("#detailedOrderModal #end_time").text(response.data.order_completed_at_time ?? '00-00');
                $("#detailedOrderModal #order_number").html(response.data.order_num);
                $("#detailedOrderModal .order-form .quote .numbers").html(response.data.order_num.split('').map(function (num) {
                    return `<span>${num}</span>`;
                }));
                $("#detailedOrderModal #driver-name").text(response.data.worker_name);
                $("#detailedOrderModal #driver-img").attr('src', `/storage/Driver-${response.data.worker_id}/${response.data.worker_photo}`);
                $("#detailedOrderModal #equipment-name").text(response.data.equipment_name);
                $("#detailedOrderModal #equipment-type-name").text(response.data.equipment_type_name);
                $("#detailedOrderModal #vehicle-img").attr('src', `/storage/Truck-${response.data.equipment_id}/${response.data.equipment_picture}`);
                $("#detailedOrderModal #capacity").text(response.data.equipment_safe_load);
                $("#detailedOrderModal .order-view-link").attr('href', `/orders/completed?id=${orderid}`);
                $("#detailedOrderModal #equipment_type_name2").text(response.data.equipment_type_name);
                $("#detailedOrderModal #company_name").text(response.data.company_name);
                $("#detailedOrderModal #company_address").text(response.data.address_line_1);
                $("#detailedOrderModal #company_contact").text(response.data.telephone);
                if (response.land_field) {
                    $("#detailedOrderModal #waste_disposal_site").text(response.land_field.location_name);
                }
                $("#detailedOrderModal #sevice_type_name").text(response.data.service_type_name);
                if (response.data.service_type_name.toLowerCase() == 'waste disposal service') {
                    $("#detailedOrderModal #waste-disposal-weight").text('10MT'); // will be dynamic later
                }
                $("#detailedOrderModal #scanned_qr_code").html(`<a href="javascript:void(0);" data-toggle="modal" data-target="#scannedRegistrationModal">Scanned: ${response.order_total_qr_codes_scanned} / ${response.order_total_qr_codes}</a>`);

                // replanned orders
                if (response.data.is_replanned == 1 && response.order_log) {
                    var replanned = response.order_log;
                    $("#replanned-info").removeClass('d-none');
                    $("#replanned-info #order-history-link").attr('href', '/order_history?id=' + replanned.order_id)
                }
                else {
                    $("#replanned-info").addClass('d-none');
                }

                //  scanned registration numbers 
                if (response.scanned_reg) {
                    var html = `
            <table class="table mt-4">
              <thead>
                <tr>
                  <th>#</td>
                  <th class="text-center">Registration Number</td>
                </tr>
              </thead>
              <tbody>`;
                    if (response.scanned_reg.length) {
                        response.scanned_reg.forEach((scanned_reg, index) => {
                            html += `
                <tr>
                <td>${index + 1}</td>
                <td class="text-center">${scanned_reg.reg_no}</td>
                </tr>`;
                        });
                    }
                    else {
                        html += `
              <tr>
                <td class="text-center" colspan="100%">No scanned registration</td>
              </tr>`;
                    }


                    html += '</tbody></table>';

                    $("#scanned-registration-nunmbers-ajax-response").html(html);
                }

                // order site images
                if (response.site_images) {
                    var images = '';
                    response.site_images.forEach(site_image => {
                        images += `
                <div class="image">
                  <img class='zoomit' src="${site_image.image_path}" alt="">
                  <span>${site_image.type}</span>
                </div>`;
                    });
                    $("#vehicle-site-images").html(images);
                }

                $("#order_tipping_image").attr('src', `/storage/tipping/${response.order_tipping ? response.order_tipping.tipping_qr_image : ''}`);


            }
        }
    });
    $('#pdf-report-download-btn').attr('href', `/orders/report/?id=${orderid}`)
});