<div class="footer">
    <div class="float-right">
        <b>Server Response Time : {{ number_format((microtime(true) - LARAVEL_START), 2, '.', '') }} seconds | Log Size : {{ log_file_size() }} | Database Size : {{ number_format((float) database_size(), 2, '.', '') }} MB</b>
    </div>
    <div>
        <strong>Copyright</strong> <b><a href="https://neputer.com/">Neputer Tech Pvt Ltd</a></b> &copy; 2019
    </div>
</div>
