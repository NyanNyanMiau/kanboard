<details class="accordion-section" <?= empty($links) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('External links') ?></summary>
    <div class="accordion-content">
        <?= $this->render('task_external_link/table', array(
            'links' => $links,
            'task' => $task,
            'project' => $project,
        )) ?>

        <?= $this->hook->render('template:task_external_link:show:after-table', array('task' => $task, 'project' => $project, 'links' => $links)) ?>
    </div>
</details>
