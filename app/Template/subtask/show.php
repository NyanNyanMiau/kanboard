<details class="accordion-section" <?= empty($subtasks) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Sub-Tasks') ?></summary>
    <div class="accordion-content">
        <?= $this->render('subtask/table', array(
            'subtasks' => $subtasks,
            'task' => $task,
            'editable' => $editable
        )) ?>

        <?= $this->hook->render('template:subtask:show:after-table', array('task' => $task, 'project' => $project, 'subtasks' => $subtasks)) ?>
    </div>
</details>
