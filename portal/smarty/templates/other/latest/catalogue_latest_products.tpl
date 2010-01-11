                            {if $use_catalogue_latest_products}
                                <div class="news">
                                    <h1>Последние товары</h1>
                                    <table width="100%">
                                        {foreach key=key item=catalogue from=$catalogue_latest_products_list}
                                                            <div>
                                                                <h3>{$catalogue.Name}</h3>
                                                                <div><a href="{$catalogue.Url}"><img src="{$catalogue.s_img}"></a></div>
                                                                <div>{$catalogue.article}</div>
                                                                <div>{$catalogue.description}</div>
                                                            </div>
                                        {/foreach}
                                    </table>
                                </div>
                            {/if}