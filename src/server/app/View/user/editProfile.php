<main>
    <div class="edit-parameters">
        <div class="my-profile-container">
            <h2><?= $model['data']['name'] ?> - Profile</h2>
        </div>
        <div class="display-name">
            <h3>Name</h3>

        </div>
        <p>Change name</p>
        <div class="input-container">
            <div class="input-box">
                <input class="input">
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
                            <p>Password</p>
                            <div class="red-star">*</div>
                        </div>

                        <div class="input-container">
                            <div class="input-box">
                                <input class="input">
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
                                <input class="input">

                                </input>
                            </div>
                        </div>
                    </div>



                </div>


            </div>
            <button class="btn-primary">
                <div class="btn-text">SAVE</div>
            </button>

            <button class="btn-primary">
                <div class="btn-text">DELETE ACCOUNT</div>
            </button>
        </div>



    </div>


</main>