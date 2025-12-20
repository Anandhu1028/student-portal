function handleFormErrors(response, parent_elem = "#offcanvasCustom") {

   const parentElement = document.querySelector(parent_elem);

   if (parentElement) {
      parentElement.querySelectorAll('.form-control').forEach((elem) => {
         elem.classList.remove('error-outline');
      });
   }

   let message = 'Something went wrong, Please contact IT support';
   let title = "Error";
   let messageAlign = 'center';

   try {
      if (response.errors) {
         const errors = response.errors;
         let messages = [];
         Object.keys(errors).forEach(function (key) {
            const elem_id = `${parent_elem} #id_${key}`;
            const elem = document.querySelector(elem_id);
            const push_value = `${key.replace("_", " ").toUpperCase()} : ${errors[key]}`;
            messages.push("<li>" + push_value + "</li>");
            if (elem) {


               if (elem.classList.contains('select2')) {
                  const select2Container = elem.nextElementSibling;
                  if (select2Container && select2Container.classList.contains('select2-container')) {
                     select2Container.classList.add('error-outline');
                  }
               } else {
                  elem.classList.add('error-outline');
               }
            }
         });
         messageAlign = 'start';
         message = "<ul style='list-style: disc;'>" + messages.join(" ") + "</ul>";
      } else if (response.errors_table) {
         const errors = response.errors_table;
         // Handle table errors if needed
      } else {
         if (response.message) {
            message = response.message;

         }
         if (response.title) {
            title = response.title;
         } else {
            title = "Error";
         }
      }
   } catch (error) {
      try {
         const response = event.responseJSON;
         if (response.message) {
            message = response.message;
            if (response.title) {
               title = response.title;
            } else {
               title = "Error";
            }
         }
      }
      catch (error) {
         //console.log("Error parsing JSON response:");
         //console.log(error);
      }

   }
   popAlert(message, title, 'error', messageAlign);
}

function handleHandsonErrors(errors, row = "ROW", title = "Errors",) {
   let messages = [];

   try {

      Object.keys(errors).forEach(field => {
         let inputField = document.getElementById("id_" + field);

         if (inputField) {
            let errorSpan = document.createElement("span");
            errorSpan.className = "error-text text-danger";
            errorSpan.textContent = errors[field][0]; // Show first error message
            inputField.insertAdjacentElement("afterend", errorSpan);
         } else {
            if (row == "") {
               Object.entries(errors[field]).forEach((lookup_value, _) => {
                  messages.push(`${lookup_value[1]} <br />`)
               })
            } else {
               Object.entries(errors[field]).forEach((lookup_value, _) => {
                  messages.push(`${row} ${lookup_value[0].replace("_", " ").toUpperCase()} : ${lookup_value[1]} <br />`)
               })
            }

         }
      });
   }
   catch (e) {
      //console.log(e)
   }

   const messageAlign = "start";

   const message = `<ul style="list-style: disc;">${messages.join(" ")}</ul>`;

   popAlert(message, title, "error", messageAlign);
}


function appendOptionToSelect2(selector, optionValue, optionText) {
   const selectElement = document.querySelector(selector);
   if (selectElement) {
      const newOption = document.createElement('option');
      newOption.value = optionValue;
      newOption.textContent = optionText;
      selectElement.appendChild(newOption);
      // Trigger change event if needed
      const event = new Event('change', { bubbles: true });
      selectElement.dispatchEvent(event);
   }
}

function get_default_time(format = 'HH:MM:SS') {

   var now = new Date();

   var hours = now.getHours();
   var minutes = now.getMinutes();
   var seconds = now.getSeconds();

   var formattedTime = format
      .replace('HH', (hours < 10 ? '0' : '') + hours)
      .replace('MM', (minutes < 10 ? '0' : '') + minutes)
      .replace('SS', (seconds < 10 ? '0' : '') + seconds);

   return formattedTime
}


function popAlert(message, title = "Alert", iconType = 'error', align = "center") {
   const modalElement = document.getElementById("modal_custom_error");

   if (!modalElement) return;
   if (title == 'Success' || title == "success") {
      title = "Success"
      iconType = "success"
   }

   // Determine image path based on URL
   const currentUrl = window.location.href;
   const image_path = (iconType === 'success') ? "success.gif" : "error.gif";
   const imgSrc = currentUrl.startsWith("http://127.0.0.1:8000")
      ? `/v2/images/alerts/${image_path}`
      : `/public/v2/images/alerts/${image_path}`;

   // Build HTML content
   let html = '<div class="text-center">';
   html += `<img src="${imgSrc}" width="80" class="mb-3" alt="${iconType}">`;
   html += `<div class="container">
                <div class="row">
                    <div class="col-12 text-${align}">
                        ${message}
                    </div>
                </div>
             </div>`;

   const modalBody = modalElement.querySelector('#modal_body_custom_error');
   const modalTitle = modalElement.querySelector('#modal_title_custom_error');

   if (modalBody) modalBody.innerHTML = html;
   if (modalTitle) modalTitle.innerHTML = title;

   // Show modal
   const myModal = new bootstrap.Modal(modalElement, {});
   modalElement.style.zIndex = 9999999;
   myModal.show();

   // Cleanup backdrop and body class when modal is closed
   modalElement.addEventListener('hidden.bs.modal', function () {
      // Remove any lingering backdrops
      const backdrops = document.querySelectorAll('.modal-backdrop');
      backdrops.forEach(b => b.remove());

      // Remove modal-open class from body
      document.body.classList.remove('modal-open');
   });
}


/**
 * Reusable renderer function to initialize a datepicker in a Handsontable cell.
 *
 * @param {Object} instance - The Handsontable instance.
 * @param {HTMLElement} td - The table cell element.
 * @param {number} row - The row index.
 * @param {number} col - The column index.
 * @param {string} prop - The property name associated with the cell.
 * @param {string} value - The current value of the cell.
 * @param {Object} cellProperties - Additional cell properties.
 * @returns {HTMLElement} - The modified table cell element.
 */

function datepickerRenderer(instance, td, row, col, prop, value, cellProperties) {
   // Clear existing content in the cell
   while (td.firstChild) {
      td.removeChild(td.firstChild);
   }

   // Create an input field
   const input = document.createElement('input');
   input.type = 'text';
   input.className = 'form-control datepicker';
   input.value = value || '';

   // Apply styles to ensure proper sizing and visibility
   input.style.border = 'none';
   input.style.margin = '0';
   input.style.width = '100%';
   input.style.padding = '0 8px';
   input.style.height = '100%';
   input.style.boxSizing = 'border-box';

   // Append the input to the cell
   td.appendChild(input);

   // Initialize Bootstrap Datepicker
   const $datepickerContainer = $(td).closest('.offcanvas').length
      ? $(td).closest('.offcanvas') // Use offcanvas as container if present
      : $('body'); // Fallback to body

   $(input).datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true,
      container: $datepickerContainer[0], // Append to offcanvas or body
      zIndexOffset: 10000 // High z-index to ensure visibility
   }).on('show', function () {
      // Adjust datepicker position and z-index
      const $datepicker = $('.bootstrap-datepicker');
      $datepicker.css({
         'z-index': 10001, // Ensure it’s above offcanvas and other elements
         position: 'absolute',
         top: $(input).offset().top + $(input).outerHeight(),
         left: $(input).offset().left
      });
   }).on('changeDate', function (e) {
      const dateStr = e.format(0);
      instance.setDataAtCell(row, col, dateStr);
   }).on('hide', function () {
      // Clean up to prevent duplicate datepickers
      $(input).datepicker('destroy');
   });

   input.addEventListener('input', function () {
      instance.setDataAtCell(row, col, input.value);
   });

   // Adjust cell styles
   td.style.padding = '0';
   td.style.position = 'relative';
   td.style.height = '30px'; // Fixed height for consistency

   return td;
}


function dayMonthRenderer(instance, td, row, col, prop, value, cellProperties) {
   // Clear existing content in the cell
   while (td.firstChild) {
      td.removeChild(td.firstChild);
   }

   // Create an input field
   const input = document.createElement('input');
   input.type = 'text';
   input.className = 'form-control datepicker';
   input.value = value || '';

   // Apply styles to ensure proper sizing and visibility
   input.style.border = 'none';
   input.style.margin = '0';
   input.style.width = '100%';
   input.style.padding = '0 8px';
   input.style.height = '100%';
   input.style.boxSizing = 'border-box';

   // Append the input to the cell
   td.appendChild(input);

   // Initialize Bootstrap Datepicker
   const $datepickerContainer = $(td).closest('.offcanvas').length
      ? $(td).closest('.offcanvas') // Use offcanvas as container if present
      : $('body'); // Fallback to body

   $(input).datepicker({
      format: 'dd-MM',
      autoclose: true,
      todayHighlight: true,
      container: $datepickerContainer[0], // Append to offcanvas or body
      zIndexOffset: 10000 // High z-index to ensure visibility
   }).on('show', function () {
      // Adjust datepicker position and z-index
      const $datepicker = $('.bootstrap-datepicker');
      $datepicker.css({
         'z-index': 10001, // Ensure it’s above offcanvas and other elements
         position: 'absolute',
         top: $(input).offset().top + $(input).outerHeight(),
         left: $(input).offset().left
      });
   }).on('changeDate', function (e) {
      const dateStr = e.format(0);
      instance.setDataAtCell(row, col, dateStr);
   }).on('hide', function () {
      // Clean up to prevent duplicate datepickers
      $(input).datepicker('destroy');
   });

   input.addEventListener('input', function () {
      instance.setDataAtCell(row, col, input.value);
   });

   // Adjust cell styles
   td.style.padding = '0';
   td.style.position = 'relative';
   td.style.height = '30px'; // Fixed height for consistency

   return td;
}


function fetchSource(query, process, apiUrl, additionalParams = {}, minQueryLength = 1) {
   if (query.length > minQueryLength) {
      axios.post(apiUrl, {
         q: query,
         ...additionalParams // Add additional params to the body
      }).then(response => {
         if (response.data && response.data) {
            // let valueField = 'name'
            // if (response.data.valueField) {
            //     valueField = response.data.valueField;
            // }
            // const keyValuePairs = response.data.results.map(data => {
            //     return {
            //         key: data['id'],  // Use 'id' for 'key'
            //         [additionalParams['fetch_option']]: data[valueField]  // Dynamically set the key using value of additionalParams['fetch_option']
            //     };
            // });

            // process(keyValuePairs);
            process(response.data.results);
         } else {
            process([]);
         }

      })
         .catch(error => {
            process([]);
         });
   } else {
      process([]);
   }
}



function fetchSourceLoad(query, process, apiUrl, additionalParams = {}) {
   axios.post(apiUrl, {
      q: query,
      ...additionalParams // Add additional params to the body
   }).then(response => {

      if (response.data && response.data) {

         process(response.data.results);
      } else {
         process([]);
      }

   })
      .catch(error => {
         process([]);
      });
}

function handleFormWrapper(event, functionName = null) {
   if (typeof window[functionName] === "function") {
      handleFormDefault(event, window[functionName]);
   } else {
      handleFormDefault(event);
   }
}


function handleFormDefault(event, function_call = null) {
   const xhr = event.detail.xhr;

   if (xhr.status === 200) {
      let message = "Something went wrong, Please contact IT support";
      let title = "Error";

      try {
         let response = JSON.parse(xhr.responseText);
         message = "Operation completed successfully";

         title = "Success";
         popAlert(message, title, "success");
         if (typeof function_call === "function") {
            function_call(response);  // Only call if function_call is valid
         }
      } catch (error) {

         let response_content = xhr.responseText;
         if (response_content.includes('Permission Error')) {
            popAlert('Permission Error: This Operation is not allowed ', title, "error");
         } else {
            popAlert(message, title, "error");
         }

      }
   }
}


function sendErrorReport(event) {
   console.log(event)
}
