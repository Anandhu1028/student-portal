var shiftKeyPressed = false;

// Detect Shift key press
$(document).on('keydown', function (event) {
   if (event.key === "Shift") {
      shiftKeyPressed = true;
   }
});

$(document).on('keyup', function (event) {
   if (event.key === "Shift") {
      shiftKeyPressed = false;
   }
});

function shiftSelect(parent_elem, check_box_elem, all_check_box_elem, form_inputs_elem = '', action_class = '', input_name = 'payment_ids') {
   let lastChecked = null;

   $(parent_elem).on('change', check_box_elem, function () {
      preloader.load();

      let checkboxes = $(parent_elem).find(check_box_elem);

      if (shiftKeyPressed && lastChecked) {
         let start = checkboxes.index(lastChecked);
         let end = checkboxes.index(this);

         if (start > end) {
            [start, end] = [end, start]; // Swap values
         }

         checkboxes.slice(start, end + 1).prop('checked', true);
      }

      lastChecked = this; // Always update lastChecked

      if (form_inputs_elem != '') {

         updateInputs(parent_elem, check_box_elem, form_inputs_elem, action_class, input_name);
      }

   });

   $(all_check_box_elem).on('change', function () {
      preloader.load();
      let checkboxes = $(parent_elem).find(check_box_elem);
      checkboxes.prop('checked', $(this).is(':checked'));
      if (form_inputs_elem != '') {
         updateInputs(parent_elem, check_box_elem, form_inputs_elem, action_class, input_name);
      }
   });
}

function updateInputs(parent_elem, check_box_elem, form_inputs_elem, action_class, input_name) {

   let checkboxes = $(parent_elem).find(check_box_elem);
   const anyChecked = checkboxes.filter(":checked").length > 0;
   const manageInputs = $(form_inputs_elem);
   manageInputs.empty();

   if (anyChecked) {
      $(action_class).removeClass('d-none');
      checkboxes.filter(":checked").each(function () {
         const id_ = $(this).val();
         const hiddenInput = $('<input>', {
            type: 'hidden',
            name: `${input_name}[]`,
            value: id_
         });
         manageInputs.append(hiddenInput);
      });
   } else {
      $(action_class).addClass('d-none');
   }

   preloader.stop();
}
