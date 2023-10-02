<main>
    <div class="edit-parameters">
        <div class="my-profile-container">
            <h2>My Profile</h2>
        </div>
        <div class="display-name" id="name">
            <h3>Nama</h3>
            <div class="input-container">
                <div class="input-box">
                    <input class="input">
                    </input>
                </div>
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
                <div class="display-name">
                    <h3>Edit Banner</h3>
                    <p>
                        Suggested size: 1280 x 400
                    </p>
                    <button class="btn-primary">
                        <div class="btn-text">UPLOAD</div>
                    </button>
                </div>

            </div>

        </div>



    </div>
    <div class="display-name" id="profile-pic">
        <h3>Edit Profile Picture</h3>
        <p>
            Suggested size: 160 x 222
        </p>
        <button class="btn-primary">
            <div class="btn-text">UPLOAD</div>
        </button>
    </div>
    <button class="btn-primary">
        <div class="btn-text">SAVE</div>
    </button>
</main>