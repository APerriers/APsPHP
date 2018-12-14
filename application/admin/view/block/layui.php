<script src="__ADMIN_JS__/layui/layui.js?v={:config('apsphp.version')}"></script>
<script>
    var ADMIN_PATH = "{$_SERVER['SCRIPT_NAME']}";
    layui.config({
        base: '__ADMIN_JS__/',
        version: '{:config("apsphp.version")}'
    }).use('global');
</script>