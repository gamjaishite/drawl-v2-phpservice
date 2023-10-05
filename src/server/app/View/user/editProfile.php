<main>
    <div class="edit-parameters">
        <div class="my-profile-container">
            <h2><?= $model['data']['name'] ?> - Profile</h2>
        </div>
        <form class="form-default" action="/editProfile" method="post">
            <div class="display-name">
                <h3>Name</h3>
            </div>
            <p>Change name</p>
            <div class="input-container">
                <div class="input-box">
                    <input class="input" name="name">
                    </input>
                </div>
            </div>
            <div class="display-name">
                <h3>Email</h3>
                <p>
                    <?= $model['data']['email'] ?>
                </p>
            </div>
            <div class="password display-name">
                <h3>Change Password</h3>
                <div class="password-button-container">
                    <div class="password-container">
                        <div class="password-title">
                            <div class="password-texts">
                                <p>Old Password</p>
                                <div class="red-star">*</div>
                            </div>
                            <div class="input-container">
                                <div class="input-box">
                                    <input class="input" type="password" name="oldPassword">
                                    </input>
                                </div>
                            </div>
                        </div>
                        <div class="password-title">
                            <div class="password-texts">
                                <p>New Password</p>
                                <div class="red-star">*</div>
                            </div>
                            <div class="input-container">
                                <div class="input-box">
                                    <input class="input" type="password" name="newPassword">

                                    </input>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input class="btn-primary" name="update_button" type="submit" value="SAVE">

                </input>
                <input class="btn-primary" name="delete_button" type="submit" value="DELETE ACCOUNT">

                </input>

        </form>
        <?php if (isset($model['error'])) { ?>
            <div class="alert-error">
                <p>
                    <?= $model['error'] ?>
                </p>
            </div>
        <?php } ?>
    </div>



    </div>


</main>