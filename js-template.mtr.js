// --- MTRGEN ---
// name: js-template
// filename: <% name %>.js
// path: assets/js
// --- MTRGEN ---

document.addEventListener('<% event %>', function() {
    var template = document.querySelector('#<% id %>');
    var templateContent = template.content;
    var clone = document.importNode(templateContent, true);
    document.body.appendChild(clone);
});
