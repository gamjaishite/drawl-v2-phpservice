<?php
function alert($title, $message, $type = 'error')
{
    require __DIR__ . '/../components/alert.php';
}

?>


<main>
    <div class="edit-parameters">
        <div class="my-profile-container">
            <h2>
                <?= $model['data']['name'] ?> - Profile
            </h2>
        </div>
        <?php if (isset($model['error'])): ?>
            <?php alert('Failed to Update', $model['error']); ?>
        <?php endif; ?>
        <?php if (isset($model['success'])): ?>
            <?php alert('Success', $model['success'], 'success'); ?>
        <?php endif; ?>
        <form id="profile-edit-form" class="form-default">
            <div class="display-name">
                <h3>Name</h3>
                <p id="name">
                    <?= $model['data']['name'] ?>
                </p>
            </div>
            <p>Change name</p>
            <div class="input-container">
                <div class="input-box">
                    <input class="input" name="name" placeholder="Enter new name"
                        value="<?= $model['data']['name'] ?>" />
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
                                    <input class="input" type="password" name="oldPassword"
                                        placeholder="Enter old password" />
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
                                    <input class="input" type="password" name="newPassword"
                                        placeholder="Enter new password" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="update-account" class="btn-primary save" name="update_button" type="submit">
                    save
                </button>
                <button id="delete-account" class="btn-bold" type="button">
                    Delete Account
                </button>
        </form>
    </div>
    </div>
</main>