<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project) : $project_name = $project['project_name']; ?>
                <li class="main-navigation__list-item <?php if ($project['project_id'] == $project_id) {
                                                            echo 'main-navigation__list-item--active';
                                                        } ?>">
                    <a class="main-navigation__list-item-link" href="/index.php?id=<?= $project['project_id']; ?>"><?= htmlspecialchars($project_name); ?></a>
                    <span class="main-navigation__list-item-count"><?= $project['count_tasks']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <!--Полнотекстовый поиск -->
    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= filter_input(INPUT_GET, 'search'); ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>
    <!-- Полнотекстовый поиск -->

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
            <a href="/" class="tasks-switch__item">Повестка дня</a>
            <a href="/" class="tasks-switch__item">Завтра</a>
            <a href="/" class="tasks-switch__item">Просроченные</a>
        </nav>

        <label class="checkbox">
            <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if (1 == $show_complete_tasks) echo 'checked'; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <?php foreach ($tasks as $key => $task) : ?>
            <?php if ($task['task_status'] && 0 == $show_complete_tasks) continue; ?>
            <tr class="tasks__item task <?php if ($task['task_status']) echo 'task--completed'; ?> <?php if (!empty($task['task_deadline']) && strtotime($task['task_deadline']) < strtotime("now + 24 hours")) echo 'task--important'; ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                        <span class="checkbox__text"><?= htmlspecialchars($task['task_name']); ?></span>
                    </label>
                </td>

                <td class="task__file">
                    <?php if (null !== $task['task_file']) : $task_file = $task['task_file'];
                    else : $task_file = 'Home.psd';
                    endif ?>
                    <a class="download-link" href="uploads/<?= $task['task_file'] ?>" target="_blank"><?= $task_file; ?></a>
                </td>

                <td class="task__date"><?= $task['task_deadline'] ?></td>
            </tr>
        <?php endforeach; ?>

        <!-- Вывод сообщения об отрицательном результате поиска задач -->
        <?php if ($not_found) : ?>
            <tr class="tasks__item task">
                <td class="task__select">
                    <span class=""><?= $not_found; ?></span>
                </td>
            </tr>
        <?php endif ?>

    </table>
</main>