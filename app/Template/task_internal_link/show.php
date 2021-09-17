<details class="accordion-section" <?= empty($links) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Internal links') ?></summary>
    <div class="accordion-content">
        <?= $this->render('task_internal_link/table', array(
            'links' => $links,
            'task' => $task,
            'project' => $project,
            'editable' => $editable,
            'is_public' => $is_public,
        )) ?>

        <?= $this->hook->render('template:task_internal_link:show:after-table', array('task' => $task, 'project' => $project, 'links' => $links)) ?>
    </div>
</details>
