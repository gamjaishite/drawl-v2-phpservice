<div class="full-container">
    <div class="edit-container">
        <div class="edit-parameters">
            <div class="my-profile-container">
                <div class="my-profile-text">My Profile</div>
            </div>
            <div class="display-name" id="name">
                <div class="display-text">Nama</div>
                <div class="input-container">
                    <div class="input-box">
                        <input class="input">
                        </input>
                    </div>
                </div>
            </div>

            <div class="display-name">
                <div class="display-text">Email</div>
                <div class="display-smaller-text">
                    <?= $model['data']['email'] ?>
                </div>
            </div>
            <div class="password display-name">
                <div class="password display-text">Change Password</div>
                <div class="password-button-container">
                    <div class="password-container">
                        <div class="password-title">
                            <div class="password-texts">
                                <div class="password-text">Password</div>
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
                                <div class="password-text">New Password</div>
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
                    <button class="save-button">
                        <div class="save-text">SAVE</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>