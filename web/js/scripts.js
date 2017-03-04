$(document).ready(function() {
    $(document).on('click', '.password-show-btn', function(e) {
        var $this = $(this);
        copyToClipboard($this.data('secret'));

        return false;
    });

    function copyToClipboard(text) {
        var aux = document.createElement('input');
        aux.setAttribute('value', text);
        document.body.appendChild(aux);
        aux.select();
        document.execCommand('copy');
        document.body.removeChild(aux);
    }
});