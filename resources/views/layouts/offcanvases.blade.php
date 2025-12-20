   <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCustom" aria-labelledby="offcanvasCustom">
       <div class="offcanvas-header border-bottom">
           <h5 class="offcanvas-title" id="offcanvasCustomHead"></h5>
           <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
       </div>
       <div class="offcanvas-body">
           <div id="offcanvasCustomBody"></div>

           <div id="offcanvasCustomFooter" class="px-3"
               style="z-index: 999999; position: absolute; bottom: 0; left: 0; right: 0;
                    border-top: 1px solid rgba(0, 0, 0, 0.1);
                    background-color: #f5f8f9;
                    height: 40px; display: flex; align-items: center;">
               <!-- You can put buttons here -->
           </div>
       </div>
   </div>
   <script>
       const offcanvasEl = document.getElementById('offcanvasCustom');

       offcanvasEl.addEventListener('hidden.bs.offcanvas', function() {
           if (bsOffcanvasCustom) {
               bsOffcanvasCustom.dispose(); // destroys JS instance
               bsOffcanvasCustom = null;
           }
       });
   </script>
