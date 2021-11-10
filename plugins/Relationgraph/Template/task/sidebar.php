<?php
    $routerController = $this->app->getRouterController();
    $routerPlugin = $this->app->getPluginName();

    $active = $routerController == 'Relationgraph' && $routerPlugin == 'Relationgraph';
?>
<li class="<?= $active ? 'active' : '' ?>">
    <?= $this->url->link(
        '<i class="fa fa-rotate-left fa-fw"></i>'. t('Relation graph'),
        'relationgraph',
        'show',
        ['plugin' => 'relationgraph', 'task_id' => $task['id']]
    ) ?>
</li>

