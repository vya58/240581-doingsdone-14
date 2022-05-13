<div class="content">
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

        <a class="button button--transparent button--plus content__side-button" href="add_project.php">Добавить проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Добавление проекта</h2>

        <form class="form" action="add_project.php" method="post" autocomplete="off">
            <div class="form__row">
                <label class="form__label" for="project_name">Название <sup>*</sup></label>
                <?php if (isset($errors['name'])) : ?>
                    <?php $name_class = "form__input--error"; ?>
                    <p class="form__message">
                        <strong><?= $errors['name']; ?></strong>
                    </p>
                <?php endif; ?>
                <input class="form__input <?= $name_class; ?>" type="text" name="name" id="project_name" value="<?= get_post_val('name'); ?>" placeholder="Введите название проекта">
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Добавить">
            </div>
        </form>
    </main>
</div>