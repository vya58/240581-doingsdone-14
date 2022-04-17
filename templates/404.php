<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): $project_name = $project['project_name']; ?>
            <li class="main-navigation__list-item <?php if ($project['project_id'] == $project_id) {echo 'main-navigation__list-item--active';} ?>">
                <a class="main-navigation__list-item-link" href="/index.php?id=<?= $project['project_id']; ?>"><?= htmlspecialchars($project_name); ?></a>
                <span class="main-navigation__list-item-count"><?= $project['count_tasks']; ?></span>
            </li>
            <?php endforeach; ?>
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

    <section class="task">
        <h2>Ошибка 404: страница не найдена</h2>
    </section>
    
</main>