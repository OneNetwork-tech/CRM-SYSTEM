/*
 * Main application JavaScript
 */

// Initialize tooltips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

// Initialize popovers
$(function () {
  $('[data-toggle="popover"]').popover()
})

// Handle sidebar navigation active state
$(document).ready(function() {
  var url = window.location;
  var element = $('ul.nav a').filter(function() {
    return this.href == url || url.href.indexOf(this.href) == 0;
  }).addClass('active').parent().parent().addClass('show');
  
  element.parent().addClass('active');
});

// Confirm before delete
function confirmDelete(message = 'Are you sure you want to delete this item?') {
  return confirm(message);
}

// Toggle password visibility
function togglePasswordVisibility(fieldId) {
  var field = document.getElementById(fieldId);
  if (field.type === "password") {
    field.type = "text";
  } else {
    field.type = "password";
  }
}