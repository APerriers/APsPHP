{if condition="input('param.aps_iframe') || cookie('aps_iframe')"}
</body>
</html>
{else /}
        </div>
    </div>
    <div class="layui-footer footer">
        <span class="fl">Powered by <a href="{:config('apsphp.url')}" target="_blank">{:config('apsphp.name')}</a> v{:config('apsphp.version')}</span>
        <span class="fr"> © 2017-2018 <a href="{:config('apsphp.url')}" target="_blank">{:config('apsphp.copyright')}</a> All Rights Reserved.</span>
    </div>
</div>
</body>
</html>
{/if}