<script>
    $(function () {
        $(document).on('click', '.nic-asia-endpoint', function (e) {
            e.preventDefault()
            $('input[name="gateway[nicasia][endpoint]"]').val($(this).data('endpoint'))
        })
    })
</script>
