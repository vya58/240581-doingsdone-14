<div class="content">
    <?= $content_project; ?>

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