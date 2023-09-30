    <div class="container__default">
        <div class="user-profile">
            <div class="images">
                <img class="banner" src="https://via.placeholder.com/1280x400" />
                <div class="profile-pic">
                    <img class="profile-img" src="https://via.placeholder.com/160x222" />
                </div>
            </div>
            <div class="profile-info">
                <div class="profile-text">
                    <h2>
                        <?= $model['data']['name'] ?>
                    </h2>
                    <div class="profile-type">
                        <div class="type-circle">
                            <div class="type-name">
                                <?= $model['data']['type'] ?>
                            </div>
                        </div>
                        <p class="subtitle">
                            <?= $model['data']['year'] ?>
                        </p>
                    </div>
                    <p>
                        <?= $model['data']['desc'] ?>
                    </p>
                </div>
                <div class="edit positioning">
                    <button class="edit button">
                        <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
                    </button>
                </div>
                <div class="delete positioning">
                    <button class="delete button">
                        <?php require PUBLIC_PATH . 'assets/icons/delete.php' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>