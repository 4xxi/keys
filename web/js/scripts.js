$(document).ready(function() {
    $(document).on('click', '.password-show-btn', function(e) {
        var $this = $(this);
        copyToClipboard($this.data('secret'));

        // TODO: ask the frontenders about copying text to clipboard from ajax. Now it's restricted by browser.
        // $this.data('text', $this.text());
        // $this.prop('disabled', true);
        // $.post($this.data('url'), function(response) {
        //     if (response && !response.status) {
        //         alert(response.message || 'Something goes wrong!');
        //     } else {
        //         copyToClipboard(response.data);
        //     }
        // }).fail(function() {
        //     $this.text('Error!').removeClass('btn-info').addClass('btn-error');
        // }).always(function() {
        //     $this.removeAttr('disabled');
        // });

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