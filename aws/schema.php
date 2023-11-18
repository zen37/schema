<?php require_once('schema.ctrl.php'); ?>
<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
      <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
      <title>Schema Update</title>
      <?php require_once('styles.php'); ?>
   </head>
   <body class="antialiased">
      <div class="wrapper">
         <?php require_once('header.php'); ?>
         <?php require_once('nav.php'); ?>
         <div class="page-wrapper">
            <div class="container-xl">
               <!-- Page title -->
               <div class="page-header d-print-none">
                  <div class="row align-items-center">
                     <div class="col">
                        <h2 class="page-title">
                           Schema Update
                        </h2>
                     </div>
                  </div>
               </div>
               <!-- Add a form with checkboxes and input textboxes -->
               <form method="post" action="schema.ctrl.php" class="row g-3 align-items-center">
                  <!-- Checkbox for listDatabases -->
                  <div class="col-auto form-check">
                     <input class="form-check-input" type="checkbox" id="listDatabases" name="listDatabases">
                     <label class="form-check-label" for="listDatabases">
                        List Databases with prefix <b><?php echo DB_PREFIX ?></b>
                     </label>
                  </div>
                  <!-- Input textarea for alterQuery -->
                  <div class="mb-3">
                     <textarea class="form-control" id="alterQuery" name="alterQuery" rows="7"></textarea>
                  </div>
                  <!-- Input textbox for targetDatabase -->
                  <?php if (!$UPDATE_ALL_DATABASES): ?>    
                  <div class="mb-3">
                     <label for="targetDatabase" class="form-label">Target Database (id, separated by comma)</label>
                     <input type="text" class="form-control" id="targetDatabase" name="targetDatabase">
                  </div>
                  <?php endif; ?>
                  <!-- Input textbox for ignoreDatabase -->
                  <?php if ($UPDATE_ALL_DATABASES): ?>           
                  <div class="mb-3">
                     <label for="ignoreDatabase" class="form-label">Ignore Database (id, separated by comma)</label>
                     <input type="text" class="form-control" id="ignoreDatabase" name="ignoreDatabase">
                  </div>
                  <?php endif; ?>
                  <!-- Checkbox for updateAllDatabases and the text -->
                  <?php if ($UPDATE_ALL_DATABASES): ?>
                  <div class="col-auto form-check">
                     <input class="form-check-input" type="checkbox" id="updateAllDatabases" name="updateAllDatabases">
                     <label class="form-check-label" for="updateAllDatabases">
                        I acknowledge <b>ALL DATABASES</b> will be updated except those in Ignore
                     </label>
                  </div>
                  <?php endif; ?>
                  <!-- Submit button -->
                  <div class="col-auto">
                     <button type="submit" name="submit" class="btn btn-primary" id="submitButton" disabled>Submit</button>
                  </div>
               </form>
               <!-- Display variables -->
               <div>
                  <?php
                    printConfig('schema/config.php');
                  ?>
               </div>
               <script>
                  // Get references to the checkbox and submit button
                  const checkbox = document.getElementById('updateAllDatabases');
                  const submitButton = document.getElementById('submitButton');
                  
                  // Function to enable/disable the button
                  function toggleSubmitButton() {
                      submitButton.disabled = !checkbox.checked;
                  }
                      // Enable the button by default when $UPDATE_ALL_DATABASES is false
                <?php if (!$UPDATE_ALL_DATABASES): ?>
                    submitButton.disabled = false;
                <?php endif; ?>
                  
                  // Add an event listener to the checkbox to enable/disable the button
                  checkbox.addEventListener('change', toggleSubmitButton);
                  
                  // Initialize the button state
                  toggleSubmitButton();

               </script>
            </div>
         </div>
      </div>
   </body>
</html>