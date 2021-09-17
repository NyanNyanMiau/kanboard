<details class="accordion-section" <?= empty($files) && empty($images) ? '' : 'open' ?>>
    <summary class="accordion-title"><?= t('Attachments') ?></summary>
    <div class="accordion-content">
        <?= $this->render('task_file/images', array('task' => $task, 'images' => $images)) ?>
        <?= $this->render('task_file/files', array('task' => $task, 'files' => $files)) ?>

        <?= $this->hook->render('template:task_file:show:after-files', array('task' => $task, 'project' => $project, 'subtasks' => $subtasks, 'files' => $files, 'images' => $images)) ?>
    </div>
</details>
