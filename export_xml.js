document.addEventListener('DOMContentLoaded', function() {
    const exportActions = document.querySelector('.export-actions');
    const confirmMsgTemplate = exportActions ? exportActions.getAttribute('data-confirm-msg') : '';
    
    const exportButtons = document.querySelectorAll('a[href="export_xml.php"], a[href="export_csv.php"]');
    exportButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirmMsgTemplate) return;

            const isXml = this.getAttribute('href') === 'export_xml.php';
            const formatName = isXml ? 'XML' : 'CSV';
            
            const confirmMsg = confirmMsgTemplate.replace('%s', formatName);
            
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    });
});
