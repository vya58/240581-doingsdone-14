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
    <h2 class="content__main-heading">Добавление задачи</h2>
    <!--Форма добавления новой задачи-->
    <form class="form" action="add_task.php" method="post" autocomplete="off" enctype="multipart/form-data">
      <div class="form__row">
        <label class="form__label" for="name">Название <sup>*</sup></label>
        <!--Вывод тега <p> с сообщением об ошибке заполнения поля 'name' -->
        <?php if (isset($errors['name'])) : ?>
          <?php $name_class = "form__input--error"; ?>
          <p class="form__message">
            <strong><?= $errors['name']; ?></strong>
          </p>
        <?php endif; ?>
        <input class="form__input <?= $name_class; ?>" type="text" name="name" id="name" value="<?= get_post_val('name'); ?>" placeholder="Введите название">
      </div>

      <div class="form__row">
        <label class="form__label" for="project">Проект <sup>*</sup></label>
        <!--Вывод тега <p> с сообщением об ошибке заполнения поля 'project' -->
        <?php if (isset($errors['project'])) : ?>
          <?php $project_class = "form__input--error"; ?>
          <p class="form__message">
            <strong><?= $errors['project']; ?></strong>
          </p>
        <?php endif; ?>
        <select class="form__input form__input--select <?= $project_class; ?>" name="project" id="project">
          <?php foreach ($projects as $project) : $project_name = $project['project_name']; ?>
            <option value="<?= $project['project_id']; ?>" <?php if (get_post_val('project') == $project['project_id']) echo " selected"; ?>><?= htmlspecialchars($project_name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form__row">
        <label class="form__label" for="date">Дата выполнения</label>
        <!--Вывод тега <p> с сообщением об ошибке заполнения поля 'date' -->
        <?php if (isset($errors['date'])) : ?>
          <?php $date_class = "form__input--error"; ?>
          <p class="form__message">
            <strong><?= $errors['date']; ?></strong>
          </p>
        <?php endif; ?>
        <input class="form__input form__input--date <?= $date_class; ?>" type="text" name="date" id="date" value="<?= get_post_val('date'); ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
      </div>

      <div class="form__row">
        <label class="form__label" for="file">Файл</label>
        <div class="form__input-file">
          <!--Вывод сообщения об ошибке заполнения поля загрузки файла -->
          <?php if (isset($errors['file'])) : ?>
            <?php $classname = "form__input--error"; ?>
            <p class="form__message">
              <strong><?= $errors['file']; ?></strong>
            </p>
          <?php endif; ?>
          <input class="visually-hidden <?= $classname ?>" type="file" name="file" id="file" value="">
          <label class="button button--transparent" for="file">
            <span>Выберите файл</span>
          </label>
        </div>
      </div>

      <div class="form__row form__row--controls">
        <!-- Вывод сообщения об ошибке заполнения формы -->
        <?php if (isset($errors)) : ?>
          <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
        <?php endif; ?>
        <input class="button" type="submit" name="" value="Добавить">
      </div>
    </form>
  </main>
</div>