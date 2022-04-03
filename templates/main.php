<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
            <li class="main-navigation__list-item">
                <a class="main-navigation__list-item-link" href="#"><?= htmlspecialchars($project); ?></a>
                <span class="main-navigation__list-item-count"><?= count_tasks_in_project($tasks, $project); ?></span>
            </li>
            <?php endforeach;?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
    href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="post" autocomplete="off">
        <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

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
        <?php foreach ($tasks as $key => $task): ?>
        <?php if($task['Выполнен'] && 0 == $show_complete_tasks) continue; ?>
            <tr class="tasks__item task <?php if($task['Выполнен']) echo 'task--completed'; ?> <?php if($task['Дата выполнения'] && strtotime($task['Дата выполнения']) < strtotime("now + 24 hours")) echo 'task--important'; ?>">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                    <span class="checkbox__text"><?= htmlspecialchars($task['Задача']); ?></span>
                </label>
            </td>

            <td class="task__file">
                <a class="download-link" href="#">Home.psd</a>
            </td>

            <td class="task__date"><?= $task['Дата выполнения'] ?></td>
            </tr>
        <?php endforeach;?>
                    <!--показывать следующий тег <tr/>, если переменная $show_complete_tasks равна единице-->
            
    </table>
</main>