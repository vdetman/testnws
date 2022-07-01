{include file="../_units/header.tpl"}
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="clearfix"></div>
                {include file="./filter.tpl" filter=$filter}
                <div class="clearfix"></div>
            </div>
            <div id="chartContent" class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet">
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark">Сумма оплат по менеджерам за период</h3>
                                <div class="portlet-widgets"><a href="javascript:;" data-toggle="expand"><i class="ion-lightbulb"></i></a><span class="divider"></span>
                                    <span class="divider"></span>
                                    <a data-toggle="collapse" href="#summRefillsVerticalBarChart"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="summRefillsVerticalBarChart" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <canvas id="summ-refills-vertical-bar-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet">
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark">Сумма оплат по всем менеджерам</h3>
                                <div class="portlet-widgets"><a href="javascript:;" data-toggle="expand"><i class="ion-lightbulb"></i></a><span class="divider"></span>
                                    <span class="divider"></span>
                                    <a data-toggle="collapse" href="#summRefillsLineChart"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="summRefillsLineChart" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <canvas id="summ-refills-line-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet">
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark">Первичные оплаты по всем менеджерам</h3>
                                <div class="portlet-widgets"><a href="javascript:;" data-toggle="expand"><i class="ion-lightbulb"></i></a><span class="divider"></span>
                                    <span class="divider"></span>
                                    <a data-toggle="collapse" href="#firstRefillsLineChart"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="firstRefillsLineChart" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <canvas id="first-refills-line-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="portlet">
                            <div class="portlet-heading">
                                <h3 class="portlet-title text-dark">Повторные оплаты по всем менеджерам</h3>
                                <div class="portlet-widgets"><a href="javascript:;" data-toggle="expand"><i class="ion-lightbulb"></i></a><span class="divider"></span>
                                    <span class="divider"></span>
                                    <a data-toggle="collapse" href="#notFirstRefillsLineChart"><i class="ion-minus-round"></i></a>
                                    <span class="divider"></span>
                                    <a href="#" data-toggle="remove"><i class="ion-close-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="notFirstRefillsLineChart" class="panel-collapse collapse in">
                                <div class="portlet-body">
                                    <canvas id="not-first-refills-line-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $.managersGraphs = {$managersGraphs};
</script>
{include file="../_units/footer.tpl"}