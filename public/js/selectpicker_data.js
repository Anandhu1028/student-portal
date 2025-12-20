async function loadOptions(params, url, func, elem, selected_id = '', trigger_change = '', save_not_exist = '') {
    params._token = $('meta[name="csrf-token"]').attr('content');
    preloader.load('loader-container');
    try {
        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: url,
                type: 'post',
                data: params,
                success: function (response) {
                    resolve(response)
                },
                error: function (xhr, _, _) {
                    reject(xhr);
                }
            });
        });
        preloader.stop('loader-container');
        if (response.data && selected_id == '') {
            func(response.data, elem, response.selected_id, trigger_change);
        } else {
            func(response, elem, selected_id, trigger_change);
        }

    } catch (e) {
        preloader.stop('loader-container');
        if ($('#modal_body #alert_modal').length > 0) {
            if (e.responseJSON.error) {
                $('#modal_body #alert_modal').html(e.responseJSON.error);
                $('#modal_body #alert_modal').removeClass('d-none')
            } else {
                $('#modal_body #alert_modal').html('Something Went Wrong, Please Contact IT support');
                $('#modal_body #alert_modal').removeClass('d-none')
            }

        }
    }


}

async function updateOptions(data, elem, selectedId = 0, triggerChange = false) {
    const $elem = $(elem);
    $elem.empty(); // Clear existing options

    const $defaultOption = $('<option></option>').val("").html("Select Option");
    $elem.append($defaultOption);  // Append default option immediately

    // Await appending all options
    await appendOptionsWithDelay(data, $elem, selectedId);


    // Refresh the selectpicker and trigger change if necessary
    $elem.selectpicker('refresh');
    $elem.selectpicker('refresh');

    if (triggerChange) {
        $elem.trigger('change');
    }
}

// Function to handle appending options with a delay
async function appendOptionsWithDelay(data, $elem, selectedId) {
    for (const item of data) {
        let selected = '';

        if (Array.isArray(selectedId)) {
            selected = selectedId.includes(String(item.id)) ? 'selected' : '';
        } else {
            selected = selectedId == item.id ? 'selected' : '';
        }

        let $option;

        if (item.state) {
            const { id, name, country } = item.state;
            $option = $('<option></option>')
                .val(item.id)
                .attr({
                    state_name: name,
                    state_id: id,
                    country_id: country.id,
                    country_name: country.name
                })
                .html(`${item.name}, ${name}, ${country.name}`)
                .prop('selected', selected);

        } else if (item.country_id) {
            const countryName = item.country ? item.country.name : item.country_name;
            $option = $('<option></option>')
                .val(item.id)
                .attr({
                    country_name: countryName,
                    country_id: item.country_id
                })
                .html(`${item.name}, ${countryName}`)
                .prop('selected', selected);

        } else if (item.other_value) {
            $option = $('<option></option>')
                .val(item.id)
                .attr({
                    other_value: other_value,
                })
                .html(item.name)
                .prop('selected', selected);

        } else {
            $option = $('<option></option>')
                .val(item.id)
                .html(item.name)
                .prop('selected', selected);
        }

        // Append the option and wait for a short delay
        $elem.append($option);
    }
}

// Helper function to simulate a delay with Promise
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
