// --- MTRGEN ---
// name: js-template
// filename: <% name="MyTemplate"|lower %>.js
// path: assets/js
// --- MTRGEN ---
document.addEventListener('<% event="event"|truncate:20 %>', function() {
    var template = document.querySelector('#<% id="myId" %>');
    var templateContent = template.content;
    template.classList.add('<% classes="TEMPLATE"|lower %>');
    var clone = document.importNode(templateContent, true);
    document.body.appendChild(clone);
});
